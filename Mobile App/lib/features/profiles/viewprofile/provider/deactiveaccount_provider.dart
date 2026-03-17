import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/deactivateaccount_repository.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';

final deactivateAccountRepositoryProvider = Provider((ref) => DeactivateAccountRepository());

class DeactivateAccountNotifier extends StateNotifier<AsyncValue<void>> {
  final DeactivateAccountRepository _repository;

  DeactivateAccountNotifier(this._repository) : super(const AsyncValue.data(null));

  Future<Map<String, dynamic>> deactivateAccount(int userId) async {
    state = const AsyncValue.loading();
    try {
      final result = await _repository.deactivateAccount(userId);

      // Use pattern matching for ApiResult
      switch (result) {
        case Success(:final data):
          state = const AsyncValue.data(null);
          AppLogger.s('Deactivate account successful in provider for userId: $userId', 'DEACTIVATE-PROVIDER');
          return {
            'success': true,
            'message': data['message'] ?? 'Akun berhasil dinonaktifkan.',
            'data': data,
          };

        case Failure(:final errorType, :final message, :final statusCode):
          state = AsyncValue.error(message, StackTrace.current);
          AppLogger.e('Deactivate account failed in provider for userId: $userId - $errorType: $message', null, null, 'DEACTIVATE-PROVIDER');
          return {
            'success': false,
            'message': message,
            'statusCode': statusCode,
          };
      }
    } catch (e, st) {
      state = AsyncValue.error(e, st);
      AppLogger.e('Exception in deactivate account provider for userId: $userId', e, st, 'DEACTIVATE-PROVIDER');
      return {
        'success': false,
        'message': e.toString(),
        'statusCode': null,
      };
    }
  }
}

final deactivateAccountProvider = StateNotifierProvider<DeactivateAccountNotifier, AsyncValue<void>>((ref) {
  final repository = ref.watch(deactivateAccountRepositoryProvider);
  return DeactivateAccountNotifier(repository);
});