import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/cancel_booking_repository.dart';
import '../model/cancel_refund_model.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';

// Repository Provider
final cancelBookingRepositoryProvider = Provider<CancelBookingRepository>((ref) {
  return CancelBookingRepository();
});

/// Notifier for cancel booking state — Riverpod 3.x Notifier with autoDispose.
class CancelBookingNotifier extends Notifier<AsyncValue<CancelBookingResponse?>> {
  late final CancelBookingRepository _repository;

  @override
  AsyncValue<CancelBookingResponse?> build() {
    _repository = ref.watch(cancelBookingRepositoryProvider);
    return const AsyncData(null);
  }

  /// Fetch refund preview without cancelling.
  Future<CancelPreviewResponse?> previewCancelRefund({
    required String orderId,
  }) async {
    try {
      AppLogger.d('Fetching cancel preview for: $orderId', 'CANCEL-BOOKING-PROVIDER');

      final result = await _repository.previewCancelRefund(orderId: orderId);

      switch (result) {
        case Success(:final data):
          AppLogger.i('Cancel preview loaded', 'CANCEL-BOOKING-PROVIDER');
          return data;
        case Failure(:final message):
          AppLogger.e('Cancel preview failed', message, null, 'CANCEL-BOOKING-PROVIDER');
          return null;
      }
    } catch (e, st) {
      AppLogger.e('Error in cancel preview', e, st, 'CANCEL-BOOKING-PROVIDER');
      return null;
    }
  }

  /// Cancel a booking. Returns response on success, null on failure.
  Future<CancelBookingResponse?> cancelBooking({
    required String orderId,
    String? reason,
    String? bankName,
    String? accountNo,
    String? accountHolder,
  }) async {
    state = const AsyncLoading();

    try {
      AppLogger.d('Cancelling booking: $orderId', 'CANCEL-BOOKING-PROVIDER');

      final result = await _repository.cancelBooking(
        orderId: orderId,
        reason: reason,
        bankName: bankName,
        accountNo: accountNo,
        accountHolder: accountHolder,
      );

      switch (result) {
        case Success(:final data):
          AppLogger.i('Booking cancelled successfully', 'CANCEL-BOOKING-PROVIDER');
          state = AsyncData(data);
          return data;
        case Failure(:final message):
          AppLogger.e('Cancel booking failed', message, null, 'CANCEL-BOOKING-PROVIDER');
          state = AsyncError(message, StackTrace.current);
          return null;
      }
    } catch (e, st) {
      AppLogger.e('Error cancelling booking', e, st, 'CANCEL-BOOKING-PROVIDER');
      state = AsyncError(e, st);
      return null;
    }
  }

  void resetState() {
    state = const AsyncData(null);
  }
}

/// Provider for cancel booking state.
final cancelBookingProvider =
    NotifierProvider.autoDispose<CancelBookingNotifier, AsyncValue<CancelBookingResponse?>>(
  CancelBookingNotifier.new,
);
