import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';

/// Repository for forgot password operations
class ForgotPasswordRepository {
  final DioClient _dioClient;

  ForgotPasswordRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Request password reset
  Future<ApiResult<String>> requestPasswordReset(String email) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.forgotPasswordUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: {'email': email},
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;
        final message = data is Map<String, dynamic> ? data['message'] ?? 'Reset password berhasil dikirim' : 'Reset password berhasil dikirim';

        AppLogger.s('Password reset requested for: $email', 'FORGOT-PASSWORD-REPO');
        return Success(message);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error requesting password reset for: $email', e, stackTrace, 'FORGOT-PASSWORD-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal meminta reset password: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode', 'FORGOT-PASSWORD-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Email tidak ditemukan', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'FORGOT-PASSWORD-REPO');

    return switch (e.type) {
      DioExceptionType.connectionTimeout || DioExceptionType.sendTimeout || DioExceptionType.receiveTimeout =>
        Failure(errorType: ApiErrorType.timeout, message: 'Koneksi timeout', originalError: e),
      DioExceptionType.connectionError =>
        Failure(errorType: ApiErrorType.network, message: 'Tidak ada koneksi internet', originalError: e),
      DioExceptionType.badResponse => _handleErrorResponse(e.response!),
      _ => Failure(errorType: ApiErrorType.unknown, message: e.message ?? 'Terjadi kesalahan', originalError: e),
    };
  }
}
