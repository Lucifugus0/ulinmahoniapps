import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/payment_model.dart';
import '../../model/update_payment_method_model.dart';

/// Repository for Payment/Booking operations
///
/// Handles all booking-related API calls with type-safe error handling
class PaymentRepository {
  final DioClient _dioClient;

  PaymentRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Update payment method for a booking
  ///
  /// Returns [ApiResult<UpdatePaymentMethodResponse>] with either success or failure
  Future<ApiResult<UpdatePaymentMethodResponse>> updatePaymentMethod(
    String bookingId,
    UpdatePaymentMethodRequest request,
  ) async {
    try {
      AppLogger.d(
        'Updating payment method for booking $bookingId: ${request.paymentMethod}',
        'PAYMENT-REPO',
      );

      final endpoint = ApiConfig.updatePaymentMethod
          .replaceFirst(ApiConfig.baseUrl, '')
          .replaceFirst('{idrec}', bookingId);

      final response = await _dioClient.put(
        endpoint,
        data: request.toJson(),
      );

      AppLogger.d(
        'Update payment method response: status=${response.statusCode}',
        'PAYMENT-REPO',
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          final updateResponse = UpdatePaymentMethodResponse.fromJson(data);

          if (updateResponse.isSuccess) {
            AppLogger.s(
              'Payment method updated successfully for booking $bookingId',
              'PAYMENT-REPO',
            );
            return Success(updateResponse);
          } else {
            AppLogger.w(
              'Payment method update failed: ${updateResponse.message}',
              'PAYMENT-REPO',
            );
            return Failure(
              errorType: ApiErrorType.unknown,
              message: updateResponse.message,
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
      AppLogger.e(
        'Unexpected error in updatePaymentMethod',
        e,
        stackTrace,
        'PAYMENT-REPO',
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan tak terduga: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Create a new booking
  ///
  /// Returns [ApiResult<BookingResponse>] with either success or failure
  Future<ApiResult<BookingResponse>> createBooking(BookingRequest request) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.bookingUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: request.toJson(),
      );

      // Handle success responses
      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic>) {
          // Check for success status in response
          if (data['status'] == 'success' && data['data'] != null) {
            final bookingResponse = BookingResponse.fromJson(data['data']);
            AppLogger.s('Booking created successfully: ${bookingResponse.idrec}', 'PAYMENT-REPO');
            return Success(bookingResponse);
          } else {
            final message = data['message'] ?? 'Booking creation failed';
            AppLogger.w('Booking failed: $message', 'PAYMENT-REPO');
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
      AppLogger.e('Unexpected error in createBooking', e, stackTrace, 'PAYMENT-REPO');
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

    AppLogger.w('API error response: Status $statusCode - $message', 'PAYMENT-REPO');

    return switch (statusCode) {
      400 => Failure(
          errorType: ApiErrorType.badRequest,
          message: message.isNotEmpty ? message : 'Permintaan tidak valid',
          statusCode: statusCode,
        ),
      401 => Failure(
          errorType: ApiErrorType.unauthorized,
          message: message.isNotEmpty ? message : 'Sesi Anda telah berakhir. Silakan login kembali.',
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

        AppLogger.w('Validation error: $errorMessages', 'PAYMENT-REPO');

        return Failure(
          errorType: ApiErrorType.validation,
          message: errorMessages,
          statusCode: 422,
        );
      }
    }

    return Failure(
      errorType: ApiErrorType.validation,
      message: 'Data yang dikirim tidak valid',
      statusCode: 422,
    );
  }

  /// Handle DioException errors
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in repository', e, e.stackTrace, 'PAYMENT-REPO');

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

/// Booking response model
class BookingResponse {
  final int idrec;
  final String? message;
  final Map<String, dynamic>? data;

  BookingResponse({
    required this.idrec,
    this.message,
    this.data,
  });

  factory BookingResponse.fromJson(Map<String, dynamic> json) {
    return BookingResponse(
      idrec: json['idrec'] ?? json['id'] ?? 0,
      message: json['message'],
      data: json,
    );
  }
}
