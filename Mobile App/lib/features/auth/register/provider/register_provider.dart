import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/register_repository.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import 'package:http/http.dart' as http;
import 'dart:convert';


enum RegisterStatus {
  initial,
  loading,
  success,
  error,
}


class RegisterState {
  final RegisterStatus status;
  final String? errorMessage; // Pesan error jika ada

  RegisterState({
    this.status = RegisterStatus.initial,
    this.errorMessage,
  });

  // Method copyWith untuk memudahkan update state
  RegisterState copyWith({
    RegisterStatus? status,
    String? errorMessage,
  }) {
    return RegisterState(
      status: status ?? this.status,
      errorMessage: errorMessage,
    );
  }
}


// Provider for RegisterRepository
final registerRepositoryProvider = Provider<RegisterRepository>((ref) {
  return RegisterRepository();
});

final registerControllerProvider = StateNotifierProvider<RegisterController, RegisterState>((ref) {
  return RegisterController(ref);
});

class RegisterController extends StateNotifier<RegisterState> {
  final Ref _ref;

  RegisterController(this._ref) : super(RegisterState());

  
  
  Future<void> register(String username, String email, String password, String phoneNumber, String firstName, String lastName) async {
    state = state.copyWith(status: RegisterStatus.loading, errorMessage: null);

    // Use Repository with ApiResult pattern
    final repository = _ref.read(registerRepositoryProvider);
    final result = await repository.register(
      username: username,
      email: email,
      password: password,
      phoneNumber: phoneNumber,
      firstName: firstName,
      lastName: lastName,
    );

    // Handle result with pattern matching
    switch (result) {
      case Success(:final data):
        AppLogger.s('Registration successful for user: $username', 'REGISTER');
        state = state.copyWith(status: RegisterStatus.success);

      case Failure(:final errorType, :final message):
        AppLogger.w('Registration failed - $errorType: $message', 'REGISTER');
        state = state.copyWith(
          status: RegisterStatus.error,
          errorMessage: message,
        );
    }
  }

  
  void setLoading(bool isLoading) {
    if (isLoading) {
      state = state.copyWith(status: RegisterStatus.loading);
    } else {
      state = state.copyWith(status: RegisterStatus.initial);
    }
  }

  
  
  Future<void> registerWithGoogle(String idToken) async {
    state = state.copyWith(status: RegisterStatus.loading, errorMessage: null);

    try {
      // Ganti URL ini dengan endpoint API backend Anda
      final url = Uri.parse('https://api.yourdomain.com/auth/google');
      final response = await http.post(
        url,
        headers: {'Content-Type': 'application/json'},
        body: json.encode({'idToken': idToken}),
      );

      if (response.statusCode == 200) {
        state = state.copyWith(status: RegisterStatus.success);
      } else {
        // Asumsikan backend mengembalikan pesan error dalam JSON
        final Map<String, dynamic> responseBody = json.decode(response.body);
        final errorMessage = responseBody['message'] ?? 'Pendaftaran Google gagal.';
        state = state.copyWith(
          status: RegisterStatus.error,
          errorMessage: errorMessage,
        );
      }
    } catch (e) {
      state = state.copyWith(
        status: RegisterStatus.error,
        errorMessage: 'Gagal menghubungi server. Periksa koneksi internet Anda.',
      );
    }
  }

  
  void resetState() {
    state = RegisterState();
  }
}