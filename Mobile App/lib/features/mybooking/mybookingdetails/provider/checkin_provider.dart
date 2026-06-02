import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/checkin_repository.dart';

/// Provider for CheckInRepository singleton.
final checkInRepositoryProvider = Provider<CheckInRepository>((ref) {
  return CheckInRepository();
});

/// Notifier for check-in state — tracks async status of check-in operation.
/// Migrated from StateProvider to Notifier for Riverpod 3.x.
class CheckInStateNotifier extends Notifier<AsyncValue<Map<String, dynamic>?>> {
  /// build() returns the initial idle state.
  @override
  AsyncValue<Map<String, dynamic>?> build() {
    return const AsyncValue.data(null);
  }

  /// Update state directly.
  void setState(AsyncValue<Map<String, dynamic>?> newState) {
    state = newState;
  }
}

/// Provider for check-in state.
final checkInStateProvider =
    NotifierProvider<CheckInStateNotifier, AsyncValue<Map<String, dynamic>?>>(
  CheckInStateNotifier.new,
);

/// Check-in method provider — returns a function that performs check-in.
final checkInProvider = Provider<Future<Map<String, dynamic>> Function({
  required String orderId,
  required String idCardBase64,
})>((ref) {
  final repository = ref.read(checkInRepositoryProvider);

  return ({
    required String orderId,
    required String idCardBase64,
  }) async {
    /// Set loading state before API call
    ref.read(checkInStateProvider.notifier).setState(const AsyncValue.loading());

    try {
      final result = await repository.checkIn(
        orderId: orderId,
        idCardBase64: idCardBase64,
      );

      /// Set success state with result data
      ref.read(checkInStateProvider.notifier).setState(AsyncValue.data(result));
      return result;
    } catch (e, stackTrace) {
      /// Set error state on failure
      ref.read(checkInStateProvider.notifier).setState(AsyncValue.error(e, stackTrace));
      rethrow;
    }
  };
});
