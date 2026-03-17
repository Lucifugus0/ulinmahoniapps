import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/register_model.dart';

/// Repository for user registration
class RegisterRepository {
  final DioClient _dioClient;

  RegisterRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Register a new user
  ///
  /// Returns [ApiResult<Register>] with either success or failure
  Future<ApiResult<Register>> register({
    required String username,
    required String email,
    required String password,
    required String phoneNumber,
    required String firstName,
    required String lastName,
  }) async {
    try {
      final requestBody = {
        'username': username,
        'email': email,
        'password': password,
        'phone_number': phoneNumber,
        'first_name': firstName,
        'last_name': lastName,
      };

      final response = await _dioClient.post(
        ApiConfig.registerUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: requestBody,
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          if (data['status'] == 'success' && data['data'] != null) {
            final registerData = Register.fromJson(data['data']);
            AppLogger.s('Registration successful for user: $username', 'REGISTER-REPO');
            return Success(registerData);
          } else {
            final message = data['message'] ?? 'Registration failed';
            AppLogger.w('Registration failed: $message', 'REGISTER-REPO');
            return Failure(
              errorType: ApiErrorType.unknown,
              message: message,
              statusCode: response.statusCode,
            );
          }
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: response.statusCode,
        );
      }

      // Handle error responses
      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error in register', e, stackTrace, 'REGISTER-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan tak terduga: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses from API
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode - $message', 'REGISTER-REPO');

    return switch (statusCode) {
      400 => Failure(
          errorType: ApiErrorType.badRequest,
          message: message.isNotEmpty ? message : 'Permintaan tidak valid',
          statusCode: statusCode,
        ),
      401 => Failure(
          errorType: ApiErrorType.unauthorized,
          message: message.isNotEmpty ? message : 'Tidak diizinkan. Silakan coba lagi.',
          statusCode: statusCode,
        ),
      403 => Failure(
          errorType: ApiErrorType.forbidden,
          message: message.isNotEmpty ? message : 'Akses ditolak',
          statusCode: statusCode,
        ),
      404 => Failure(
          errorType: ApiErrorType.notFound,
          message: message.isNotEmpty ? message : 'Endpoint tidak ditemukan',
          statusCode: statusCode,
        ),
      422 => _handleValidationError(data),
      500 => Failure(
          errorType: ApiErrorType.server,
          message: message.isNotEmpty ? message : 'Terjadi kesalahan server',
          statusCode: statusCode,
        ),
      _ => Failure(
          errorType: ApiErrorType.unknown,
          message: message.isNotEmpty ? message : 'Terjadi kesalahan tidak terduga ($statusCode)',
          statusCode: statusCode,
        ),
    };
  }

  /// Handle 422 validation errors
  Failure<T> _handleValidationError<T>(dynamic data) {
    if (data is Map<String, dynamic> && data['errors'] != null) {
      final errors = data['errors'];
      if (errors is Map) {
        final errorMessages = errors.values
            .map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString())
            .join(', ');

        AppLogger.w('Validation error: $errorMessages', 'REGISTER-REPO');

        return Failure(
          errorType: ApiErrorType.validation,
          message: errorMessages,
          statusCode: 422,
        );
      }
    }

    return Failure(
      errorType: ApiErrorType.validation,
      message: data is Map ? (data['message'] ?? 'Data tidak valid') : 'Data tidak valid',
      statusCode: 422,
    );
  }

  /// Handle DioException errors
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in repository', e, e.stackTrace, 'REGISTER-REPO');

    return switch (e.type) {
      DioExceptionType.connectionTimeout ||
      DioExceptionType.sendTimeout ||
      DioExceptionType.receiveTimeout =>
        Failure(
          errorType: ApiErrorType.timeout,
          message: 'Koneksi timeout. Silakan coba lagi.',
          originalError: e,
        ),
      DioExceptionType.connectionError => Failure(
          errorType: ApiErrorType.network,
          message: 'Tidak ada koneksi internet',
          originalError: e,
        ),
      DioExceptionType.badResponse => _handleErrorResponse(e.response!),
      _ => Failure(
          errorType: ApiErrorType.unknown,
          message: e.message ?? 'Terjadi kesalahan tidak terduga',
          originalError: e,
        ),
    };
  }
}
