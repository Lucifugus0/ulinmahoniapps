import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../model/rooms_model.dart';
import '../../detailproperty/model/detailproperty_model.dart';
import '../../../searchresult/model/searchfilter_model.dart';
import '../../../searchresult/provider/searchresult_provider.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/constants/api_constants.dart';
import 'package:intl/intl.dart';

/// Riverpod 3.x Notifier for room details state — migrated from StateNotifier
/// Manages room data, rent type, duration, check-in/out dates, and multi-tier daily pricing
class RoomDetailsNotifier extends Notifier<AsyncValue<Map<String, dynamic>>> {
  /// Build method returns the initial loading state (replaces constructor super call)
  @override
  AsyncValue<Map<String, dynamic>> build() => const AsyncValue.loading();

  /// Parse custom date format "dd-MM-yyyy HH:mm" to DateTime
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

  /// Load room details from room model and property data, applying search filter defaults
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

          // Clamped month addition: if target month has fewer days, clamp to last day
          // e.g. Jan 31 + 1 month = Feb 28, Mar 31 + 1 month = Apr 30
          final targetMonth = initialCheckInDate.month + initialDuration;
          final targetYear = initialCheckInDate.year + (targetMonth - 1) ~/ 12;
          final normalizedMonth = ((targetMonth - 1) % 12) + 1;
          final maxDay = DateTime(targetYear, normalizedMonth + 1, 0).day;
          final clampedDay = initialCheckInDate.day > maxDay ? maxDay : initialCheckInDate.day;
          initialCheckOutDate = DateTime(targetYear, normalizedMonth, clampedDay,
            initialCheckInDate.hour, initialCheckInDate.minute, initialCheckInDate.second,
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

  /// Update rent type and reset duration/checkout — triggers checkout recalculation
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

  /// Update booking duration — triggers checkout recalculation
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

  /// Update check-in date — triggers checkout recalculation
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

  /// Recalculate check-out date based on current rent type, duration, and check-in date
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

          // Clamped month addition: clamp to last day of target month
          final tMonth = checkInDate.month + duration;
          final tYear = checkInDate.year + (tMonth - 1) ~/ 12;
          final nMonth = ((tMonth - 1) % 12) + 1;
          final mDay = DateTime(tYear, nMonth + 1, 0).day;
          final cDay = checkInDate.day > mDay ? mDay : checkInDate.day;
          calculatedCheckOutDate = DateTime(tYear, nMonth, cDay,
            checkInDate.hour, checkInDate.minute, checkInDate.second,
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

  /// Get current booking data map from state — returns empty map if no data loaded
  Map<String, dynamic> getBookingData() {
    if (state.hasValue) {
      return state.value!;
    }
    return {};
  }

  /// Reset state to empty initial values
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

/// Provider for RoomDetailsNotifier — migrated from StateNotifierProvider to NotifierProvider
final roomDetailsProvider = NotifierProvider<RoomDetailsNotifier, AsyncValue<Map<String, dynamic>>>(RoomDetailsNotifier.new);
