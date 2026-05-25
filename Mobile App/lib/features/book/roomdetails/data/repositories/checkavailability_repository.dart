import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/checkavaibility_model.dart';

/// Repository for room availability checking
class CheckAvailabilityRepository {
  final DioClient _dioClient;

  CheckAvailabilityRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Check room availability
  Future<ApiResult<AvailabilityCheckResponse>> checkRoomAvailability({
    required int propertyId,
    required int roomId,
    required String checkInDate,
    required String checkOutDate,
    bool isRenewal = false,
    String bookingType = 'daily',
  }) async {
    try {
      final requestBody = {
        "property_id": propertyId,
        "room_id": roomId,
        "check_in": checkInDate,
        "check_out": checkOutDate,
        "is_renewal": isRenewal ? 1 : 0,
        // Server uses booking_type to apply type-specific caps:
        //   new daily: check_in ≤ today+90d. new monthly: check_in ≤ today+14d.
        //   renewal daily: check_out ≤ today+60d. renewal monthly: no date cap.
        // Lowercase: Laravel's `in:daily,monthly` rule is case-sensitive and
        // the search filter still stores capitalized "Daily"/"Monthly".
        "booking_type": bookingType.toLowerCase(),
      };

      final response = await _dioClient.post(
        ApiConfig.checkavailabilityUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: requestBody,
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final availabilityResponse = AvailabilityCheckResponse.fromJson(response.data);
        AppLogger.s('Availability checked for room $roomId in property $propertyId', 'AVAILABILITY-REPO');
        return Success(availabilityResponse);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error checking availability for room $roomId', e, stackTrace, 'AVAILABILITY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Tidak dapat memeriksa ketersediaan: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Extract first error message from errors map, fallback to message field
  String _extractErrorMessage(Map<String, dynamic> data) {
    final errors = data['errors'];
    if (errors is Map<String, dynamic>) {
      for (final entry in errors.values) {
        if (entry is List && entry.isNotEmpty) {
          return entry.first.toString();
        }
        if (entry is String && entry.isNotEmpty) {
          return entry;
        }
      }
    }
    final message = data['message']?.toString() ?? '';
    return message;
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? _extractErrorMessage(data) : '';

    AppLogger.w('API error response: Status $statusCode - $message', 'AVAILABILITY-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Kamar tidak ditemukan', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'AVAILABILITY-REPO');

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
