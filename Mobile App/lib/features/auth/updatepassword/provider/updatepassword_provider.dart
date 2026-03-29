import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../login/provider/auth_provider.dart';
import '../data/repositories/updatepassword_repository.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';

final updatePasswordRepositoryProvider = Provider<UpdatePasswordRepository>((ref) {
  return UpdatePasswordRepository();
});



sealed class UpdatePasswordState {}


class UpdatePasswordInitial extends UpdatePasswordState {}


class UpdatePasswordLoading extends UpdatePasswordState {}


class UpdatePasswordSuccess extends UpdatePasswordState {
  final String message;
  UpdatePasswordSuccess(this.message);
}


class UpdatePasswordError extends UpdatePasswordState {
  final String message;
  UpdatePasswordError(this.message);
}


/// Migrated from StateNotifierProvider to NotifierProvider for Riverpod 3.x
final updatePasswordNotifierProvider = NotifierProvider<UpdatePasswordNotifier, UpdatePasswordState>(UpdatePasswordNotifier.new);

/// Update password notifier — migrated from StateNotifier to Notifier.
/// Uses build() instead of constructor for initial state.
/// ref is available as a property (no need to store it).
class UpdatePasswordNotifier extends Notifier<UpdatePasswordState> {
  /// Returns initial state via build method (Riverpod 3.x pattern)
  @override
  UpdatePasswordState build() => UpdatePasswordInitial();

  Future<void> updatePassword({
    required String oldPassword,
    required String newPassword,
    required String confirmNewPassword,
  }) async {
    state = UpdatePasswordLoading();

    final currentUserId = ref.read(authProvider).user.value?.id;

    if (currentUserId == null) {
      state = UpdatePasswordError('Pengguna tidak terautentikasi atau userId tidak ditemukan.');
      AppLogger.w('Update password failed - user not authenticated', 'UPDATE-PASSWORD');
      return;
    }

    final repository = ref.read(updatePasswordRepositoryProvider);
    final result = await repository.updatePassword(
      userId: currentUserId.toString(),
      oldPassword: oldPassword,
      newPassword: newPassword,
      confirmNewPassword: confirmNewPassword,
    );

    switch (result) {
      case Success(:final data):
        AppLogger.s('Password updated successfully', 'UPDATE-PASSWORD');
        state = UpdatePasswordSuccess(data);

      case Failure(:final errorType, :final message):
        AppLogger.w('Password update failed - $errorType: $message', 'UPDATE-PASSWORD');
        state = UpdatePasswordError(message);
    }
  }


  void resetState() {
    state = UpdatePasswordInitial();
  }
}
