import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/doku_va_model.dart';

/// Repository for DOKU Virtual Account operations
///
/// Handles DOKU VA generation API calls with type-safe error handling
class DokuVARepository {
  final DioClient _dioClient;

  DokuVARepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Generate DOKU Virtual Account
  ///
  /// Creates a virtual account for payment
  /// Returns [ApiResult<DokuVAResponse>] with either success or failure
  Future<ApiResult<DokuVAResponse>> generateVA(
    DokuVARequest request,
  ) async {
    try {
      AppLogger.d(
        'Generating DOKU VA for order: ${request.orderId}, bank: ${request.bank}',
        'DOKU-VA-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.dokuGenerateVA.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      AppLogger.d(
        'DOKU VA response: status=${response.statusCode}, data=${response.data}',
        'DOKU-VA-REPO',
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          final vaResponse = DokuVAResponse.fromJson(data);

          if (vaResponse.isSuccess) {
            AppLogger.s(
              'DOKU VA generated successfully. VA Number: ${vaResponse.data?.virtualAccountNo}',
              'DOKU-VA-REPO',
            );
            return Success(vaResponse);
          } else {
            AppLogger.w(
              'DOKU VA generation failed: ${vaResponse.message}',
              'DOKU-VA-REPO',
            );
            return Failure(
              errorType: ApiErrorType.validation,
              message: vaResponse.message,
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

      // Handle 4xx errors
      if (response.statusCode! >= 400 && response.statusCode! < 500) {
        // Try to get error from errors array first, fallback to message
        String message = 'Failed to generate virtual account';
        if (response.data != null) {
          if (response.data['errors'] != null &&
              response.data['errors'] is List &&
              (response.data['errors'] as List).isNotEmpty) {
            message = response.data['errors'][0].toString();
          } else if (response.data['message'] != null) {
            message = response.data['message'];
          }
        }

        AppLogger.w('DOKU VA generation error: $message', 'DOKU-VA-REPO');
        return Failure(
          errorType: ApiErrorType.validation,
          message: message,
          statusCode: response.statusCode,
        );
      }

      // Handle other status codes
      return Failure(
        errorType: ApiErrorType.server,
        message: 'Server error occurred',
        statusCode: response.statusCode,
      );
    } on DioException catch (e) {
      AppLogger.e(
        'DioException during DOKU VA generation',
        e,
        StackTrace.current,
        'DOKU-VA-REPO',
      );

      // Handle specific Dio errors
      if (e.type == DioExceptionType.connectionTimeout ||
          e.type == DioExceptionType.receiveTimeout) {
        return Failure(
          errorType: ApiErrorType.timeout,
          message: 'Connection timeout. Please try again.',
        );
      }

      if (e.type == DioExceptionType.connectionError) {
        return Failure(
          errorType: ApiErrorType.network,
          message: 'No internet connection',
        );
      }

      if (e.response != null) {
        final statusCode = e.response!.statusCode;

        // Try to get error from errors array first, fallback to message
        String message = 'Failed to generate virtual account';
        if (e.response!.data != null) {
          if (e.response!.data['errors'] != null &&
              e.response!.data['errors'] is List &&
              (e.response!.data['errors'] as List).isNotEmpty) {
            message = e.response!.data['errors'][0].toString();
          } else if (e.response!.data['message'] != null) {
            message = e.response!.data['message'];
          }
        }

        if (statusCode == 400 || statusCode == 404) {
          return Failure(
            errorType: ApiErrorType.validation,
            message: message,
            statusCode: statusCode,
          );
        }

        return Failure(
          errorType: ApiErrorType.server,
          message: message,
          statusCode: statusCode,
        );
      }

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred',
      );
    } catch (e, stackTrace) {
      AppLogger.e(
        'Unexpected error during DOKU VA generation',
        e,
        stackTrace,
        'DOKU-VA-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred: ${e.toString()}',
      );
    }
  }
}

/// Provider for DokuVARepository
final dokuVARepositoryProvider = DokuVARepository();
