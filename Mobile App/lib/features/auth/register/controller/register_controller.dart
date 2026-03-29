import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/register_repository.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';

final registerRepositoryProvider = Provider<RegisterRepository>((ref) {
  return RegisterRepository();
});

/// Migrated from StateNotifierProvider to NotifierProvider for Riverpod 3.x
final registerControllerProvider = NotifierProvider<RegisterController, bool>(RegisterController.new);

/// Register controller — migrated from StateNotifier to Notifier.
/// Uses build() instead of constructor for initial state.
/// ref is available as a property (no need to store it).
class RegisterController extends Notifier<bool> {
  /// Returns initial loading state (false) via build method
  @override
  bool build() => false;

  Future<String?> register(String username, String email, String password, String phoneNumber, String firstName, String lastName) async {
    state = true;

    // Validation
    if (username.trim().isEmpty || email.trim().isEmpty || password.trim().isEmpty) {
      state = false;
      return 'Semua kolom harus diisi.';
    }

    final emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
    if (!emailRegex.hasMatch(email)) {
      state = false;
      return 'Format email tidak valid.';
    }

    if (password.length < 8) {
      state = false;
      return 'Password minimal 8 karakter.';
    }

    try {
      final repository = ref.read(registerRepositoryProvider);

      final result = await repository.register(
        username: username,
        email: email,
        password: password,
        phoneNumber: phoneNumber,
        firstName: firstName,
        lastName: lastName,
      );

      return switch (result) {
        Success() => null, // Success, no error message
        Failure(:final message) => message,
      };
    } catch (e) {
      AppLogger.e('Registration failed', e, StackTrace.current, 'REGISTER-CONTROLLER');
      return 'Registration gagal: $e';
    } finally {
      state = false;
    }
  }
}
