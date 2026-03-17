import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';

/// Repository for uploading ID document (KTP/KITAS/Passport)
class UploadIdRepository {
  final DioClient _dioClient;

  UploadIdRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient();

  /// Upload ID document by user ID
  Future<ApiResult<Map<String, dynamic>>> uploadIdDocument(
      int userId, String base64Image) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.uploadPicture
            .replaceFirst(ApiConfig.baseUrl, '')
            .replaceFirst('{idrec}', userId.toString()),
        data: {
          'profile_picture': base64Image,
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final body = response.data;

        if (body is Map) {
          AppLogger.s("Successfully uploaded ID document for userId: $userId",
              "UPLOAD-ID-REPO");
          return Success(body.cast<String, dynamic>());
        }

        AppLogger.w('Success but invalid data format for userId: $userId',
            "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Unggah dokumen berhasil, namun format data tidak valid.',
          statusCode: response.statusCode,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e, userId);
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error uploading ID for userId: $userId', e,
          stackTrace, "UPLOAD-ID-REPO");
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Unexpected error: ${e.toString()}',
      );
    }
  }

  /// Handle error response
  ApiResult<Map<String, dynamic>> _handleErrorResponse(Response response) {
    final statusCode = response.statusCode ?? 0;
    final body = response.data;

    AppLogger.w('Failed to upload ID - Status: $statusCode',
        "UPLOAD-ID-REPO");

    switch (statusCode) {
      case 400:
        AppLogger.e('Error 400 - Bad Request - ${body?['message']}', null,
            null, "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.badRequest,
          message: body?['message'] ?? 'Permintaan tidak valid (400)',
          statusCode: 400,
        );

      case 401:
        AppLogger.e('Error 401 - Unauthorized', null, null,
            "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.unauthorized,
          message: 'Sesi Anda telah berakhir. Mohon login ulang (401).',
          statusCode: 401,
        );

      case 403:
        AppLogger.e(
            'Error 403 - Forbidden', null, null, "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.forbidden,
          message: 'Akses ditolak (403).',
          statusCode: 403,
        );

      case 404:
        AppLogger.e(
            'Error 404 - Not Found', null, null, "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.notFound,
          message: 'Endpoint API tidak ditemukan (404).',
          statusCode: 404,
        );

      case 422:
        final errors = body?['errors'];
        if (errors != null && errors is Map) {
          final errorMessages = errors.values
              .map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString())
              .join(', ');
          AppLogger.e('Error 422 - Validation Failed - $errorMessages', null,
              null, "UPLOAD-ID-REPO");
          return Failure(
            errorType: ApiErrorType.validation,
            message: errorMessages,
            statusCode: 422,
          );
        }
        AppLogger.e('Error 422 - Unprocessable Entity', null, null,
            "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.validation,
          message: body?['message'] ?? 'Data yang dikirim tidak valid (422).',
          statusCode: 422,
        );

      case 500:
        AppLogger.e('Error 500 - Internal Server Error', null, null,
            "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.server,
          message: 'Terjadi kesalahan server internal (500).',
          statusCode: 500,
        );

      default:
        AppLogger.e('Unexpected Error - Status: $statusCode', null, null,
            "UPLOAD-ID-REPO");
        return Failure(
          errorType: ApiErrorType.unknown,
          message:
              'Terjadi kesalahan tidak terduga ($statusCode): ${body?['message'] ?? 'Tidak diketahui'}.',
          statusCode: statusCode,
        );
    }
  }

  /// Handle DioException
  ApiResult<Map<String, dynamic>> _handleDioException(
      DioException e, int userId) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout ||
        e.type == DioExceptionType.sendTimeout) {
      AppLogger.e('Timeout error for userId: $userId', e, null,
          "UPLOAD-ID-REPO");
      return Failure(
        errorType: ApiErrorType.timeout,
        message: 'Request timeout. Please try again.',
      );
    }

    if (e.type == DioExceptionType.connectionError) {
      AppLogger.e('Connection error for userId: $userId', e, null,
          "UPLOAD-ID-REPO");
      return Failure(
        errorType: ApiErrorType.network,
        message:
            'Tidak dapat terhubung ke server. Periksa koneksi internet Anda.',
      );
    }

    if (e.response != null) {
      return _handleErrorResponse(e.response!);
    }

    AppLogger.e('Unknown Dio error for userId: $userId', e, null,
        "UPLOAD-ID-REPO");
    return Failure(
      errorType: ApiErrorType.unknown,
      message: 'Unknown error: ${e.message}',
    );
  }
}
