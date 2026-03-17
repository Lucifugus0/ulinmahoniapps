import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/mybooking_model.dart';
import '../../model/propertyimage_model.dart';

/// Repository for booking operations
class MyBookingRepository {
  final DioClient _dioClient;

  MyBookingRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Fetch bookings by user ID
  Future<ApiResult<List<MyBookingModel>>> fetchBookingByUserId(int userId) async {
    try {
      final response = await _dioClient.get(ApiConfig.mybookingUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{userId}', userId.toString()));

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] is List) {
          List data = body['data'];
          final bookings = data.map((json) => MyBookingModel.fromJson(json)).toList();

          AppLogger.s('Fetched ${bookings.length} bookings for user: $userId', 'MYBOOKING-REPO');
          return Success(bookings);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Format data tidak valid',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      // Handle 404 specially - return empty list instead of error
      if (e.response?.statusCode == 404) {
        AppLogger.i('No bookings found for user: $userId (404)', 'MYBOOKING-REPO');
        return Success([]);
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching bookings for user: $userId', e, stackTrace, 'MYBOOKING-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi error saat mengambil data booking: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch property image by property ID
  Future<ApiResult<PropertyImageModel?>> fetchPropertyImageByPropertyId(int propertyId) async {
    try {
      final response = await _dioClient.get(ApiConfig.propertybyidUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{propertyId}', propertyId.toString()));

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body.containsKey('data') && body['data'] is Map) {
          final propertyData = body['data'] as Map<String, dynamic>;
          final propertyImage = PropertyImageModel.fromJson(propertyData);

          AppLogger.s('Fetched property image for property: $propertyId', 'MYBOOKING-REPO');
          return Success(propertyImage);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Format data tidak valid',
          statusCode: 200,
        );
      } else if (response.statusCode == 404) {
        AppLogger.i('Property image not found for: $propertyId', 'MYBOOKING-REPO');
        return Success(null);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        return Success(null);
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching property image: $propertyId', e, stackTrace, 'MYBOOKING-REPO');
      return Success(null); // Return null on error for backward compatibility
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode', 'MYBOOKING-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      401 => Failure(errorType: ApiErrorType.unauthorized, message: message.isNotEmpty ? message : 'Sesi berakhir', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Data tidak ditemukan', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'MYBOOKING-REPO');

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
