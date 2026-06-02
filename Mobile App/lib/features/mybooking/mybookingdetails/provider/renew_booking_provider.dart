import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/renew_booking_repository.dart';
import '../model/renew_booking_model.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';

// Repository Provider
final renewBookingRepositoryProvider = Provider<RenewBookingRepository>((ref) {
  return RenewBookingRepository();
});

/// Notifier for renew booking state.
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
/// Removed `mounted` checks since Notifier handles lifecycle automatically.
class RenewBookingNotifier extends Notifier<AsyncValue<RenewBookingResponse?>> {
  late final RenewBookingRepository _repository;

  /// build() returns the initial state and initializes dependencies.
  @override
  AsyncValue<RenewBookingResponse?> build() {
    _repository = ref.watch(renewBookingRepositoryProvider);
    return const AsyncData(null);
  }

  /// Submit a renew booking request — returns the response on success, null on failure.
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
          state = AsyncData(data);
          return data;

        case Failure(:final message):
          AppLogger.e('Renew booking failed', message, null, 'RENEW-BOOKING-PROVIDER');
          state = AsyncError(message, StackTrace.current);
          return null;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in renew booking provider', e, stackTrace, 'RENEW-BOOKING-PROVIDER');
      state = AsyncError(e, stackTrace);
      return null;
    }
  }

  /// Reset state back to initial (no data).
  void resetState() {
    state = const AsyncData(null);
  }
}

/// Provider for renew booking state — Riverpod 3.x NotifierProvider with autoDispose.
final renewBookingProvider = NotifierProvider.autoDispose<RenewBookingNotifier, AsyncValue<RenewBookingResponse?>>(
  RenewBookingNotifier.new,
);
