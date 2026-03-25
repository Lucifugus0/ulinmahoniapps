import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../model/rooms_model.dart';
import '../../detailproperty/model/detailproperty_model.dart';
import '../../../searchresult/model/searchfilter_model.dart';
import '../../../searchresult/provider/searchresult_provider.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/constants/api_constants.dart';
import 'package:intl/intl.dart';

class RoomDetailsNotifier extends StateNotifier<AsyncValue<Map<String, dynamic>>> {
  RoomDetailsNotifier(this.ref) : super(const AsyncValue.loading());

  final Ref ref;

  DateTime? _parseCustomDate(String? dateString) {
    if (dateString == null || dateString.isEmpty) {
      return null;
    }
    try {

      final DateFormat formatter = DateFormat("dd-MM-yyyy HH:mm");
      return formatter.parseStrict(dateString);
    } catch (e) {
      AppLogger.w('Error parsing custom date format "$dateString": $e. Using null.', 'ROOM-DETAILS');
      return null;
    }
  }

  void loadRoomDetails(RoomModel room, DetailPropertyModel propertyData) {
    state = const AsyncValue.loading();

    try {
      final SearchFilter currentSearchFilter = ref.read(searchFilterProvider);

      String? initialRentType = currentSearchFilter.rentType;
      int? initialDuration = currentSearchFilter.durationRaw;


      DateTime? initialCheckInDate = _parseCustomDate(currentSearchFilter.checkInDate);
      if (currentSearchFilter.checkInDate != null && currentSearchFilter.checkInDate!.isNotEmpty) {
        AppLogger.d('checkInDate from filter (raw String): ${currentSearchFilter.checkInDate}', 'ROOM-DETAILS');
        AppLogger.d('checkInDate after parsing (DateTime): ${initialCheckInDate?.toIso8601String()}', 'ROOM-DETAILS');
      }


      DateTime? initialCheckOutDate;
      if (initialCheckInDate != null && initialDuration != null && initialDuration > 0 && initialRentType != null) {
        if (initialRentType == 'Daily') {
          initialCheckOutDate = initialCheckInDate.add(Duration(days: initialDuration));
          AppLogger.d('Calculated checkOutDate (Daily): ${initialCheckOutDate.toIso8601String()}', 'ROOM-DETAILS');
        } else if (initialRentType == 'Monthly') {

          initialCheckOutDate = DateTime(
            initialCheckInDate.year,
            initialCheckInDate.month + initialDuration,
            initialCheckInDate.day,
            initialCheckInDate.hour,
            initialCheckInDate.minute,
            initialCheckInDate.second,
            initialCheckInDate.millisecond,
            initialCheckInDate.microsecond,
          );
          AppLogger.d('Calculated checkOutDate (Monthly): ${initialCheckOutDate.toIso8601String()}', 'ROOM-DETAILS');
        }
      }

      state = AsyncValue.data({
        'room': room,
        'propertyData': propertyData,
        'rentType': initialRentType,
        'duration': initialDuration,
        'checkInDate': initialCheckInDate,
        'checkOutDate': initialCheckOutDate,
      });

      AppLogger.i('Room details loaded with initial filter - RentType=$initialRentType, Duration=$initialDuration, CheckIn: ${initialCheckInDate?.toIso8601String()}, CheckOut: ${initialCheckOutDate?.toIso8601String()}', 'ROOM-DETAILS');

      /* Daily Multi Tier Pricing: fetch per-date pricing if daily with dates */
      _fetchAndApplyDailyPricing();
    } catch (e, st) {
      state = AsyncValue.error(e, st);
      AppLogger.e('Error loading room details', e, st, 'ROOM-DETAILS');
    }
  }

  void updateRentType(String? newRentType) {
    if (state.value != null) {
      state = AsyncValue.data({
        ...state.value!,
        'rentType': newRentType,
        'duration': 1,
        'checkOutDate': null,
      });
      _calculateAndSetCheckOutDate();
      AppLogger.i('RentType updated to: $newRentType', 'ROOM-DETAILS');
    }
  }

  void updateDuration(int? newDuration) {
    if (state.value != null) {
      state = AsyncValue.data({
        ...state.value!,
        'duration': newDuration,
      });
      _calculateAndSetCheckOutDate();
      AppLogger.i('Duration updated to: $newDuration', 'ROOM-DETAILS');
    }
  }

  void updateCheckInDate(DateTime newCheckInDate) {
    if (state.value != null) {
      state = AsyncValue.data({
        ...state.value!,
        'checkInDate': newCheckInDate,
      });
      _calculateAndSetCheckOutDate();
      AppLogger.i('CheckInDate updated to: ${newCheckInDate.toIso8601String()}', 'ROOM-DETAILS');
    }
  }


  void _calculateAndSetCheckOutDate() {
    if (state.value != null) {
      final rentType = state.value!['rentType'] as String?;
      final duration = state.value!['duration'] as int?;
      final checkInDate = state.value!['checkInDate'] as DateTime?;

      DateTime? calculatedCheckOutDate;

      if (checkInDate != null && duration != null && duration > 0 && rentType != null) {
        if (rentType == 'daily' || rentType == 'Daily') {
          calculatedCheckOutDate = checkInDate.add(Duration(days: duration));
        } else if (rentType == 'monthly' || rentType == 'Monthly') {

          calculatedCheckOutDate = DateTime(
            checkInDate.year,
            checkInDate.month + duration,
            checkInDate.day,
            checkInDate.hour,
            checkInDate.minute,
            checkInDate.second,
            checkInDate.millisecond,
            checkInDate.microsecond,
          );
        }
      }

      if (state.value!['checkOutDate'] != calculatedCheckOutDate) {
        state = AsyncValue.data({
          ...state.value!,
          'checkOutDate': calculatedCheckOutDate,
        });
        AppLogger.d('CheckOutDate recalculated to: ${calculatedCheckOutDate?.toIso8601String()}', 'ROOM-DETAILS');
      }

      /* Daily Multi Tier Pricing: re-fetch per-date pricing when dates change */
      _fetchAndApplyDailyPricing();
    }
  }

  /* Daily Multi Tier Pricing: call price-preview API for daily bookings */
  /* Override room.priceOriginalDaily with effective average so payment calculates correct total */
  Future<void> _fetchAndApplyDailyPricing() async {
    if (state.value == null) return;

    final rentType = state.value!['rentType'] as String?;
    final checkInDate = state.value!['checkInDate'] as DateTime?;
    final checkOutDate = state.value!['checkOutDate'] as DateTime?;
    final room = state.value!['room'] as RoomModel?;

    /* Only apply for daily bookings with valid dates */
    if (rentType == null || !(rentType == 'daily' || rentType == 'Daily')) return;
    if (checkInDate == null || checkOutDate == null || room == null) return;
    if (room.id == null) return;

    try {
      final dioClient = DioClient();
      final checkIn = DateFormat('yyyy-MM-dd').format(checkInDate);
      final checkOut = DateFormat('yyyy-MM-dd').format(checkOutDate);
      final url = ApiConfig.roomPricePreview(room.id.toString())
          .replaceFirst(ApiConfig.baseUrl, '');

      AppLogger.i('Fetching price-preview: roomId=${room.id}, $checkIn to $checkOut', 'ROOM-DETAILS');

      final response = await dioClient.get(url, queryParameters: {
        'check_in': checkIn,
        'check_out': checkOut,
      });

      if (response.statusCode == 200 && response.data is Map<String, dynamic>) {
        final data = response.data['data'] as Map<String, dynamic>?;
        if (data != null) {
          final totalPrice = (data['total_price'] as num?)?.toDouble() ?? 0;
          final totalDays = (data['total_days'] as num?)?.toInt() ?? 0;
          final isFlatRate = data['is_flat_rate'] as bool? ?? true;

          if (totalPrice > 0 && totalDays > 0) {
            /* Calculate effective average daily price */
            final effectiveDailyPrice = totalPrice / totalDays;

            /* Create room copy with overridden daily price */
            final updatedRoom = room.copyWithDailyPrice(effectiveDailyPrice.toStringAsFixed(0));

            /* Update state with modified room — payment will use this effective rate */
            if (state.value != null) {
              state = AsyncValue.data({
                ...state.value!,
                'room': updatedRoom,
                'multiTierTotalPrice': totalPrice,
                'multiTierIsFlatRate': isFlatRate,
              });
              AppLogger.i(
                'Daily price override: total=$totalPrice, days=$totalDays, '
                'effective=${effectiveDailyPrice.toStringAsFixed(0)}, flat=$isFlatRate',
                'ROOM-DETAILS',
              );
            }
          }
        }
      }
    } catch (e) {
      /* Non-fatal: payment will fall back to flat rate */
      AppLogger.w('Price-preview fetch failed (using flat rate): $e', 'ROOM-DETAILS');
    }
  }


  Map<String, dynamic> getBookingData() {

    if (state.hasValue) {
      return state.value!;
    }
    return {};
  }


  void resetState() {
    state = const AsyncValue.data({
      'room': null,
      'propertyData': null,
      'rentType': null,
      'duration': null,
      'checkInDate': null,
      'checkOutDate': null,
    });
  }
}


final roomDetailsProvider = StateNotifierProvider.autoDispose<RoomDetailsNotifier, AsyncValue<Map<String, dynamic>>>((ref) {
  AppLogger.d('roomDetailsProvider created', 'ROOM-DETAILS');
  return RoomDetailsNotifier(ref);
});
