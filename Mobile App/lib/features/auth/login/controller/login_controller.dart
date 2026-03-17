import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../login/provider/auth_provider.dart';
import '../../../../core/utils/app_logger.dart';

final loginControllerProvider = StateNotifierProvider<LoginController, bool>((ref) {
  return LoginController(ref);
});

class LoginController extends StateNotifier<bool> {
  final Ref _ref;

  LoginController(this._ref) : super(false);

  Future<String?> login(String email, String password,{required bool rememberMe}) async {
    state = true; 

    
    if (email.trim().isEmpty || password.trim().isEmpty) {
      state = false;
      return 'Semua kolom wajib terisi.';
    }

    try {
      await _ref.read(authProvider.notifier).login(email, password, rememberMe: rememberMe);

      final authState = _ref.read(authProvider);
      if (authState.isLoggedIn) {
        AppLogger.s('Login successful - User ID: ${authState.user.value?.id}', 'LOGIN-CONTROLLER');
        return null;
      } else {
        AppLogger.w('Login failed', 'LOGIN-CONTROLLER');
        return 'Login gagal. Periksa kembali data Anda.';
      }
    } catch (e) {
      AppLogger.e('Login exception', e, StackTrace.current, 'LOGIN-CONTROLLER');
      return 'Terjadi kesalahan: $e';
    } finally {
      state = false;
    }
  }
}
