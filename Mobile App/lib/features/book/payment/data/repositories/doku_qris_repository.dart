import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/doku_qris_model.dart';

/// Repository for DOKU QRIS operations
///
/// Handles DOKU QRIS generation API calls with type-safe error handling
class DokuQRISRepository {
  final DioClient _dioClient;

  DokuQRISRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Generate DOKU QRIS
  ///
  /// Creates a QRIS QR code for payment
  /// Returns [ApiResult<DokuQRISResponse>] with either success or failure
  Future<ApiResult<DokuQRISResponse>> generateQRIS(
    DokuQRISRequest request,
  ) async {
    try {
      AppLogger.d(
        'Generating DOKU QRIS for order: ${request.orderId}, amount: ${request.amount}',
        'DOKU-QRIS-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.dokuGenerateQRIS.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      AppLogger.d(
        'DOKU QRIS response: status=${response.statusCode}, data=${response.data}',
        'DOKU-QRIS-REPO',
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          final qrisResponse = DokuQRISResponse.fromJson(data);

          if (qrisResponse.isSuccess) {
            AppLogger.s(
              'DOKU QRIS generated successfully. Reference No: ${qrisResponse.data?.referenceNo}',
              'DOKU-QRIS-REPO',
            );
            return Success(qrisResponse);
          } else {
            AppLogger.w(
              'DOKU QRIS generation failed: ${qrisResponse.message}',
              'DOKU-QRIS-REPO',
            );
            return Failure(
              errorType: ApiErrorType.validation,
              message: qrisResponse.message,
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
        String message = 'Failed to generate QRIS';
        if (response.data != null) {
          if (response.data['errors'] != null &&
              response.data['errors'] is List &&
              (response.data['errors'] as List).isNotEmpty) {
            message = response.data['errors'][0].toString();
          } else if (response.data['message'] != null) {
            message = response.data['message'];
          }
        }

        AppLogger.w('DOKU QRIS generation error: $message', 'DOKU-QRIS-REPO');
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
        'DioException during DOKU QRIS generation',
        e,
        StackTrace.current,
        'DOKU-QRIS-REPO',
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

        String message = 'Failed to generate QRIS';
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
        'Unexpected error during DOKU QRIS generation',
        e,
        stackTrace,
        'DOKU-QRIS-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred: ${e.toString()}',
      );
    }
  }
}

/// Provider for DokuQRISRepository
final dokuQRISRepositoryProvider = DokuQRISRepository();
