import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/deactivateaccount_repository.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';

/// Provider for DeactivateAccountRepository singleton.
final deactivateAccountRepositoryProvider = Provider((ref) => DeactivateAccountRepository());

/// Notifier for deactivate account state.
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
class DeactivateAccountNotifier extends Notifier<AsyncValue<void>> {
  /// build() returns the initial state.
  @override
  AsyncValue<void> build() {
    return const AsyncValue.data(null);
  }

  /// Deactivate the user account — returns a result map with success/failure info.
  Future<Map<String, dynamic>> deactivateAccount(int userId) async {
    state = const AsyncValue.loading();
    try {
      final repository = ref.read(deactivateAccountRepositoryProvider);
      final result = await repository.deactivateAccount(userId);

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

/// Provider for DeactivateAccountNotifier — Riverpod 3.x NotifierProvider.
final deactivateAccountProvider = NotifierProvider<DeactivateAccountNotifier, AsyncValue<void>>(
  DeactivateAccountNotifier.new,
);
