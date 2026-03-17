import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/voucher_model.dart';

/// Repository for Voucher operations
///
/// Handles all voucher-related API calls with type-safe error handling
class VoucherRepository {
  final DioClient _dioClient;

  VoucherRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Validate a voucher code
  ///
  /// Checks if voucher is valid, eligible, and calculates discount
  /// Returns [ApiResult<VoucherValidationResponse>] with either success or failure
  Future<ApiResult<VoucherValidationResponse>> validateVoucher(
    VoucherValidationRequest request,
  ) async {
    try {
      AppLogger.d(
        'Validating voucher: ${request.voucherCode} for user ${request.userId}',
        'VOUCHER-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.validateVoucher.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      // Handle success responses
      if (response.statusCode == 200) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          final voucherResponse = VoucherValidationResponse.fromJson(data);

          if (voucherResponse.isValid) {
            AppLogger.s(
              'Voucher ${request.voucherCode} is valid. Discount: ${voucherResponse.calculation.discountAmount}',
              'VOUCHER-REPO',
            );
            return Success(voucherResponse);
          } else {
            final message = voucherResponse.message.isNotEmpty
                ? voucherResponse.message
                : voucherResponse.eligibility.message;

            AppLogger.w('Voucher validation failed: $message', 'VOUCHER-REPO');
            return Failure(
              errorType: ApiErrorType.validation,
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

      // Handle 4xx errors
      if (response.statusCode! >= 400 && response.statusCode! < 500) {
        // Try to get error from errors array first, fallback to message
        String message = 'Invalid voucher code';
        if (response.data != null) {
          if (response.data['errors'] != null &&
              response.data['errors'] is List &&
              (response.data['errors'] as List).isNotEmpty) {
            message = response.data['errors'][0].toString();
          } else if (response.data['message'] != null) {
            message = response.data['message'];
          }
        }

        AppLogger.w('Voucher validation error: $message', 'VOUCHER-REPO');
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
        'DioException during voucher validation',
        e,
        StackTrace.current,
        'VOUCHER-REPO',
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
        String message = 'Voucher validation failed';
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
        'Unexpected error during voucher validation',
        e,
        stackTrace,
        'VOUCHER-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred: ${e.toString()}',
      );
    }
  }

  /// Apply a voucher to a booking
  ///
  /// Applies validated voucher to the booking
  /// Returns [ApiResult<VoucherApplicationResponse>] with either success or failure
  Future<ApiResult<VoucherApplicationResponse>> applyVoucher(
    VoucherApplicationRequest request,
  ) async {
    try {
      AppLogger.d(
        'Applying voucher: ${request.voucherCode} for property ${request.propertyId}, room ${request.roomId}',
        'VOUCHER-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.applyVoucher.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      AppLogger.d(
        'Apply voucher response: status=${response.statusCode}, data=${response.data}',
        'VOUCHER-REPO',
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          final applicationResponse = VoucherApplicationResponse.fromJson(data);

          AppLogger.d(
            'Parsed application response: isSuccess=${applicationResponse.isSuccess}, message=${applicationResponse.message}',
            'VOUCHER-REPO',
          );

          if (applicationResponse.isSuccess) {
            AppLogger.s(
              'Voucher applied successfully. Discount: ${applicationResponse.discountApplied}',
              'VOUCHER-REPO',
            );
            return Success(applicationResponse);
          } else {
            AppLogger.w(
              'Voucher application failed: ${applicationResponse.message}',
              'VOUCHER-REPO',
            );
            return Failure(
              errorType: ApiErrorType.validation,
              message: applicationResponse.message,
              statusCode: response.statusCode,
            );
          }
        }

        AppLogger.w(
          'Invalid response format, data is not Map<String, dynamic>: $data',
          'VOUCHER-REPO',
        );
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: response.statusCode,
        );
      }

      // Handle error responses
      final message = response.data?['message'] ?? 'Failed to apply voucher';
      return Failure(
        errorType: ApiErrorType.server,
        message: message,
        statusCode: response.statusCode,
      );
    } on DioException catch (e) {
      AppLogger.e(
        'DioException during voucher application',
        e,
        StackTrace.current,
        'VOUCHER-REPO',
      );

      if (e.response != null) {
        final message = e.response!.data?['message'] ?? 'Failed to apply voucher';
        return Failure(
          errorType: ApiErrorType.server,
          message: message,
          statusCode: e.response!.statusCode,
        );
      }

      return Failure(
        errorType: ApiErrorType.network,
        message: 'Network error occurred',
      );
    } catch (e, stackTrace) {
      AppLogger.e(
        'Unexpected error during voucher application',
        e,
        stackTrace,
        'VOUCHER-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'An unexpected error occurred: ${e.toString()}',
      );
    }
  }
}
