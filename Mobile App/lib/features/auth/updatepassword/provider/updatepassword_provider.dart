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



class UpdatePasswordNotifier extends StateNotifier<UpdatePasswordState> {
  final Ref _ref;

  UpdatePasswordNotifier(this._ref) : super(UpdatePasswordInitial());

  Future<void> updatePassword({
    required String oldPassword,
    required String newPassword,
    required String confirmNewPassword,
  }) async {
    state = UpdatePasswordLoading();

    final currentUserId = _ref.read(authProvider).user.value?.id;

    if (currentUserId == null) {
      state = UpdatePasswordError('Pengguna tidak terautentikasi atau userId tidak ditemukan.');
      AppLogger.w('Update password failed - user not authenticated', 'UPDATE-PASSWORD');
      return;
    }

    final repository = _ref.read(updatePasswordRepositoryProvider);
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



final updatePasswordNotifierProvider = StateNotifierProvider<UpdatePasswordNotifier, UpdatePasswordState>((ref) {
  return UpdatePasswordNotifier(ref);
});
