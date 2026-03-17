import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';

/// Repository for update password operations
class UpdatePasswordRepository {
  final DioClient _dioClient;

  UpdatePasswordRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Update password
  Future<ApiResult<String>> updatePassword({
    required String userId,
    required String oldPassword,
    required String newPassword,
    required String confirmNewPassword,
  }) async {
    try {
      final response = await _dioClient.put(
        ApiConfig.updatePasswordUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{userId}', userId),
        data: {
          'current_password': oldPassword,
          'password': newPassword,
          'password_confirmation': confirmNewPassword,
        },
      );

      if (response.statusCode == 200) {
        final data = response.data;
        final message = data is Map<String, dynamic> ? data['message'] ?? 'Password berhasil diperbarui' : 'Password berhasil diperbarui';

        AppLogger.s('Password updated for userId: $userId', 'UPDATE-PASSWORD-REPO');
        return Success(message);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error updating password for userId: $userId', e, stackTrace, 'UPDATE-PASSWORD-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal memperbarui password: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    String message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    // Translate specific error messages
    if (message == "Current password is incorrect") {
      message = "Password saat ini salah";
    }

    AppLogger.w('API error response: Status $statusCode - $message', 'UPDATE-PASSWORD-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Password saat ini salah', statusCode: statusCode),
      401 => Failure(errorType: ApiErrorType.unauthorized, message: message.isNotEmpty ? message : 'Sesi berakhir', statusCode: statusCode),
      422 => _handleValidationError(data),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle validation errors
  Failure<T> _handleValidationError<T>(dynamic data) {
    if (data is Map<String, dynamic> && data['errors'] != null) {
      final errors = data['errors'];
      if (errors is Map) {
        final errorMessages = errors.values.map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString()).join(', ');
        return Failure(errorType: ApiErrorType.validation, message: errorMessages, statusCode: 422);
      }
    }
    return Failure(errorType: ApiErrorType.validation, message: 'Data tidak valid', statusCode: 422);
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'UPDATE-PASSWORD-REPO');

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
