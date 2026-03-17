import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/checkin_repository.dart';

final checkInRepositoryProvider = Provider<CheckInRepository>((ref) {
  return CheckInRepository();
});

/// Provider for check-in state
final checkInStateProvider = StateProvider<AsyncValue<Map<String, dynamic>?>>((ref) {
  return const AsyncValue.data(null);
});

/// Check-in method provider
final checkInProvider = Provider<Future<Map<String, dynamic>> Function({
  required String orderId,
  required String idCardBase64,
})>((ref) {
  final repository = ref.read(checkInRepositoryProvider);

  return ({
    required String orderId,
    required String idCardBase64,
  }) async {
    ref.read(checkInStateProvider.notifier).state = const AsyncValue.loading();

    try {
      final result = await repository.checkIn(
        orderId: orderId,
        idCardBase64: idCardBase64,
      );

      ref.read(checkInStateProvider.notifier).state = AsyncValue.data(result);
      return result;
    } catch (e, stackTrace) {
      ref.read(checkInStateProvider.notifier).state = AsyncValue.error(e, stackTrace);
      rethrow;
    }
  };
});
