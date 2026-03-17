import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/doku_cc_model.dart';

/// Repository for DOKU Credit Card operations
///
/// Handles DOKU CC payment generation API calls with type-safe error handling
class DokuCCRepository {
  final DioClient _dioClient;

  DokuCCRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Generate DOKU Credit Card Payment
  ///
  /// Creates a payment URL for credit card payment
  /// Returns [ApiResult<DokuCCResponse>] with either success or failure
  Future<ApiResult<DokuCCResponse>> generateCC(
    DokuCCRequest request,
  ) async {
    try {
      AppLogger.d(
        'Generating DOKU CC for order: ${request.orderId}, amount: ${request.amount}',
        'DOKU-CC-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.dokuGenerateCC.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      AppLogger.d(
        'DOKU CC response: status=${response.statusCode}, data=${response.data}',
        'DOKU-CC-REPO',
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          // Parse the response directly (not wrapped in {status, data})
          final ccData = DokuCCData.fromJson(data);

          if (ccData.success && ccData.paymentUrl.isNotEmpty) {
            AppLogger.s(
              'DOKU CC payment generated successfully. Invoice: ${ccData.invoiceNumber}, URL: ${ccData.paymentUrl}',
              'DOKU-CC-REPO',
            );

            // Wrap in response format for consistency with provider
            final ccResponse = DokuCCResponse(
              status: 'success',
              message: 'CC payment URL generated successfully',
              data: ccData,
            );

            return Success(ccResponse);
          } else {
            AppLogger.w(
              'DOKU CC generation failed: payment_url is empty or success=false',
              'DOKU-CC-REPO',
            );
            return Failure(
              errorType: ApiErrorType.validation,
              message: 'Failed to generate payment URL',
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
        String message = 'Failed to generate credit card payment';
        if (response.data != null) {
          if (response.data['errors'] != null &&
              response.data['errors'] is List &&
              (response.data['errors'] as List).isNotEmpty) {
            message = response.data['errors'][0].toString();
          } else if (response.data['message'] != null) {
            message = response.data['message'];
          }
        }

        AppLogger.w('DOKU CC generation error: $message', 'DOKU-CC-REPO');
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
        'DioException during DOKU CC generation',
        e,
        StackTrace.current,
        'DOKU-CC-REPO',
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

        String message = 'Failed to generate credit card payment';
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
        'Unexpected error during DOKU CC generation',
        e,
        stackTrace,
        'DOKU-CC-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred: ${e.toString()}',
      );
    }
  }
}

/// Provider for DokuCCRepository
final dokuCCRepositoryProvider = DokuCCRepository();
