import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../login/provider/auth_provider.dart';
import '../../../../core/utils/app_logger.dart';

/// Migrated from StateNotifierProvider to NotifierProvider for Riverpod 3.x
final loginControllerProvider = NotifierProvider<LoginController, bool>(LoginController.new);

/// Login controller — migrated from StateNotifier to Notifier.
/// Uses build() instead of constructor for initial state.
/// ref is available as a property (no need to store it).
class LoginController extends Notifier<bool> {
  /// Returns initial loading state (false) via build method
  @override
  bool build() => false;

  Future<String?> login(String email, String password,{required bool rememberMe}) async {
    state = true;


    if (email.trim().isEmpty || password.trim().isEmpty) {
      state = false;
      return 'Semua kolom wajib terisi.';
    }

    try {
      await ref.read(authProvider.notifier).login(email, password, rememberMe: rememberMe);

      final authState = ref.read(authProvider);
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
