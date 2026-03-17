import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/forgotpassword_repository.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart'; 


enum ForgotPasswordStatus {
  initial,
  loading,
  success,
  error,
}


class ForgotPasswordState {
  final ForgotPasswordStatus status;
  final String? errorMessage; 

  ForgotPasswordState({
    this.status = ForgotPasswordStatus.initial,
    this.errorMessage,
  });

  
  ForgotPasswordState copyWith({
    ForgotPasswordStatus? status,
    String? errorMessage,
  }) {
    return ForgotPasswordState(
      status: status ?? this.status,
      errorMessage: errorMessage, 
    );
  }
}

final forgotPasswordRepositoryProvider = Provider<ForgotPasswordRepository>((ref) {
  return ForgotPasswordRepository();
});

final forgotPasswordControllerProvider = StateNotifierProvider<ForgotPasswordController, ForgotPasswordState>((ref) {
  return ForgotPasswordController(ref);
});

class ForgotPasswordController extends StateNotifier<ForgotPasswordState> {
  final Ref _ref;

  ForgotPasswordController(this._ref) : super(ForgotPasswordState());


  Future<void> requestPasswordReset(String email) async {
    state = state.copyWith(status: ForgotPasswordStatus.loading, errorMessage: null);

    final repository = _ref.read(forgotPasswordRepositoryProvider);
    final result = await repository.requestPasswordReset(email);

    switch (result) {
      case Success():
        AppLogger.s('Password reset request successful for: $email', 'FORGOT-PASSWORD');
        state = state.copyWith(status: ForgotPasswordStatus.success);

      case Failure(:final errorType, :final message):
        AppLogger.w('Password reset request failed - $errorType: $message', 'FORGOT-PASSWORD');

        // Customize message for email not found
        String displayMessage = message;
        if (errorType == ApiErrorType.notFound) {
          displayMessage = 'Email belum terdaftar. Mohon periksa kembali atau daftar.';
        }

        state = state.copyWith(
          status: ForgotPasswordStatus.error,
          errorMessage: displayMessage,
        );
    }
  }

  
  void resetState() {
    state = ForgotPasswordState();
  }
}