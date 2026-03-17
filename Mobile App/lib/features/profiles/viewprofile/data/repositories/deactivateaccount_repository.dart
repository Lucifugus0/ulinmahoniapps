import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../../../../core/utils/app_logger.dart';

class DeactivateAccountRepository {
  final DioClient _dioClient;

  DeactivateAccountRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient();

  Future<ApiResult<Map<String, dynamic>>> deactivateAccount(int userId) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.deactiveaccountUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{userId}', userId.toString()),
        data: {
          'user_id': userId,
        },
      );

      // Check if response is successful
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        AppLogger.s('Account deactivated successfully for userId: $userId', 'DEACTIVATE-REPO');

        return Success({
          'success': true,
          'message': data['message'] ?? 'Akun berhasil dinonaktifkan.',
          'data': data,
        });
      }

      // Handle error responses
      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Exception in deactivateAccount for userId: $userId', e, stackTrace, 'DEACTIVATE-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan: ${e.toString()}',
      );
    }
  }

  /// Handle non-2xx HTTP responses
  ApiResult<Map<String, dynamic>> _handleErrorResponse(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map ? (data['message'] ?? 'Terjadi kesalahan') : 'Terjadi kesalahan';

    AppLogger.w('HTTP Error $statusCode: $message', 'DEACTIVATE-REPO');

    ApiErrorType errorType;
    if (statusCode == 400) {
      errorType = ApiErrorType.badRequest;
    } else if (statusCode == 401) {
      errorType = ApiErrorType.unauthorized;
    } else if (statusCode == 403) {
      errorType = ApiErrorType.forbidden;
    } else if (statusCode == 404) {
      errorType = ApiErrorType.notFound;
    } else if (statusCode == 422) {
      errorType = ApiErrorType.validation;
    } else if (statusCode >= 500) {
      errorType = ApiErrorType.server;
    } else {
      errorType = ApiErrorType.unknown;
    }

    return Failure(
      errorType: errorType,
      message: message,
      statusCode: statusCode,
    );
  }

  /// Handle Dio exceptions
  ApiResult<Map<String, dynamic>> _handleDioException(DioException e) {
    AppLogger.e('DioException in deactivateAccount', e, e.stackTrace, 'DEACTIVATE-REPO');

    switch (e.type) {
      case DioExceptionType.connectionTimeout:
      case DioExceptionType.sendTimeout:
      case DioExceptionType.receiveTimeout:
        return Failure(
          errorType: ApiErrorType.timeout,
          message: 'Koneksi timeout. Silakan coba lagi.',
          originalError: e,
        );

      case DioExceptionType.connectionError:
        return Failure(
          errorType: ApiErrorType.network,
          message: 'Tidak ada koneksi internet.',
          originalError: e,
        );

      case DioExceptionType.badResponse:
        final response = e.response;
        if (response != null) {
          return _handleErrorResponse(response);
        }
        return Failure(
          errorType: ApiErrorType.server,
          message: 'Server error',
          originalError: e,
        );

      default:
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Terjadi kesalahan: ${e.message}',
          originalError: e,
        );
    }
  }
}
