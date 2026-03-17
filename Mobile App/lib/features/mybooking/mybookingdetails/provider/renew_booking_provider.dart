import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/renew_booking_repository.dart';
import '../model/renew_booking_model.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';

// Repository Provider
final renewBookingRepositoryProvider = Provider<RenewBookingRepository>((ref) {
  return RenewBookingRepository();
});

// State Notifier for Renew Booking
class RenewBookingNotifier extends StateNotifier<AsyncValue<RenewBookingResponse?>> {
  final RenewBookingRepository _repository;

  RenewBookingNotifier(this._repository) : super(const AsyncData(null));

  Future<RenewBookingResponse?> renewBooking({
    required String orderId,
    required int userId,
    required String userName,
    required String userPhoneNumber,
    required String userEmail,
    required int propertyId,
    required String propertyName,
    required String propertyType,
    required int roomId,
    required String roomName,
    required String bookingType,
    required String checkIn,
    required String checkOut,
    double? monthlyPrice,
    int? bookingMonths,
    double? adminFees,
    required double serviceFees,
    required String transactionType,
    String? voucherCode,
    double? parkingFee,
    String? parkingType,
    int? parkingDuration,
  }) async {
    state = const AsyncLoading();

    try {
      AppLogger.d('Starting renew booking process for: $orderId', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('User: $userId - $userName', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Property: $propertyId - $propertyName', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Room: $roomId - $roomName', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Check In: $checkIn, Check Out: $checkOut', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Booking Type: $bookingType, Months: $bookingMonths', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Pricing: Monthly=$monthlyPrice, Service=$serviceFees, Admin=$adminFees', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Transaction Type: $transactionType', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Voucher Code: $voucherCode', 'RENEW-BOOKING-PROVIDER');
      AppLogger.d('Parking: $parkingFee ($parkingType) for $parkingDuration months', 'RENEW-BOOKING-PROVIDER');

      final result = await _repository.renewBooking(
        orderId: orderId,
        userId: userId,
        userName: userName,
        userPhoneNumber: userPhoneNumber,
        userEmail: userEmail,
        propertyId: propertyId,
        propertyName: propertyName,
        propertyType: propertyType,
        roomId: roomId,
        roomName: roomName,
        bookingType: bookingType,
        checkIn: checkIn,
        checkOut: checkOut,
        monthlyPrice: monthlyPrice,
        bookingMonths: bookingMonths,
        adminFees: adminFees,
        serviceFees: serviceFees,
        transactionType: transactionType,
        voucherCode: voucherCode,
        parkingFee: parkingFee,
        parkingType: parkingType,
        parkingDuration: parkingDuration,
      );

      switch (result) {
        case Success(:final data):
          AppLogger.i('Renew booking successful', 'RENEW-BOOKING-PROVIDER');
          AppLogger.d('Response data: $data', 'RENEW-BOOKING-PROVIDER');
          AppLogger.d('Response data.data: ${data.data}', 'RENEW-BOOKING-PROVIDER');
          AppLogger.d('Response data.data.orderId: ${data.data?.orderId}', 'RENEW-BOOKING-PROVIDER');
          if (mounted) {
            state = AsyncData(data);
          }
          return data;

        case Failure(:final message):
          AppLogger.e('Renew booking failed', message, null, 'RENEW-BOOKING-PROVIDER');
          if (mounted) {
            state = AsyncError(message, StackTrace.current);
          }
          return null;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in renew booking provider', e, stackTrace, 'RENEW-BOOKING-PROVIDER');
      if (mounted) {
        state = AsyncError(e, stackTrace);
      }
      return null;
    }
  }

  void resetState() {
    state = const AsyncData(null);
  }
}

// Provider for Renew Booking State
final renewBookingProvider = StateNotifierProvider.autoDispose<RenewBookingNotifier, AsyncValue<RenewBookingResponse?>>(
  (ref) {
    return RenewBookingNotifier(ref.watch(renewBookingRepositoryProvider));
  },
);
