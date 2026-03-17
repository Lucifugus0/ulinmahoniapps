import 'package:dio/dio.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/roomimages_model.dart';
import '../../../mybooking/model/mybooking_model.dart';

/// Repository for my booking details operations
class MyBookingDetailsRepository {
  final DioClient _dioClient;

  MyBookingDetailsRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient();

  /// Upload image as base64
  Future<ApiResult<bool>> uploadImageAsBase64(
      String idrec, String base64Image) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.uploadimageUrl
            .replaceFirst(ApiConfig.baseUrl, '')
            .replaceFirst('{idrec}', idrec),
        data: {
          'attachment_file': base64Image,
        },
      );

      if (response.statusCode == 200) {
        AppLogger.s("Upload success for idrec: $idrec", 'MYBOOKING-DETAIL-REPO');
        return Success(true);
      }

      return Failure(
        errorType: ApiErrorType.server,
        message: 'Failed to upload image',
        statusCode: response.statusCode,
      );
    } on DioException catch (e) {
      AppLogger.e("Error uploading image for idrec: $idrec", e, null,
          'MYBOOKING-DETAIL-REPO');
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e("Unexpected error uploading image for idrec: $idrec", e,
          stackTrace, 'MYBOOKING-DETAIL-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Unexpected error: ${e.toString()}',
      );
    }
  }

  /// Fetch room image model by room ID
  Future<ApiResult<RoomImageModel>> fetchRoomImageModelByRoomId(
      int roomId) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.roombyidUrl
            .replaceFirst(ApiConfig.baseUrl, '')
            .replaceFirst('{roomId}', roomId.toString()),
      );

      if (response.statusCode == 200) {
        final responseBody = response.data;

        if (responseBody is Map &&
            responseBody.containsKey('data') &&
            responseBody['data'] is Map<String, dynamic>) {
          final Map<String, dynamic> roomData = responseBody['data'];
          AppLogger.s(
              'Room data found for roomId: $roomId', 'MYBOOKING-DETAIL-REPO');
          return Success(RoomImageModel.fromJson(roomData));
        }

        AppLogger.w('Key "data" not found in response for roomId: $roomId',
            'MYBOOKING-DETAIL-REPO');
        return Failure(
          errorType: ApiErrorType.parsing,
          message:
              'Format respons API tidak sesuai harapan untuk detail ruangan: kunci "data" tidak ada atau bukan map.',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        AppLogger.i('Room detail not found for roomId: $roomId (404)',
            'MYBOOKING-DETAIL-REPO');
        return Failure(
          errorType: ApiErrorType.notFound,
          message: 'Room not found',
          statusCode: 404,
        );
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching room detail for roomId: $roomId', e,
          stackTrace, 'MYBOOKING-DETAIL-REPO');

      if (e is FormatException) {
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid JSON response from API: $e',
        );
      } else if (e is TypeError) {
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Type mismatch in API response parsing: $e',
        );
      }

      return Failure(
        errorType: ApiErrorType.unknown,
        message:
            'An unexpected error occurred while fetching room detail: $e',
      );
    }
  }

  /// Fetch booking by ID
  Future<ApiResult<MyBookingModel>> fetchBookingById(int idrec) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.mybookingidUrl
            .replaceFirst(ApiConfig.baseUrl, '')
            .replaceFirst('{idrec}', idrec.toString()),
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map &&
            body['status'] == 'success' &&
            body['data'] is Map<String, dynamic>) {
          final Map<String, dynamic> bookingData = body['data'];
          AppLogger.s('Booking data received successfully for idrec: $idrec',
              'MYBOOKING-DETAIL-REPO');
          return Success(MyBookingModel.fromJson(bookingData));
        }

        AppLogger.w('Unexpected response format for idrec: $idrec',
            'MYBOOKING-DETAIL-REPO');
        return Failure(
          errorType: ApiErrorType.parsing,
          message:
              'Unexpected API response format or operation not successful.',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        AppLogger.i('Booking not found for idrec: $idrec (404)',
            'MYBOOKING-DETAIL-REPO');
        return Failure(
          errorType: ApiErrorType.notFound,
          message: 'Booking not found',
          statusCode: 404,
        );
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching booking by idrec: $idrec', e, stackTrace,
          'MYBOOKING-DETAIL-REPO');

      if (e is FormatException) {
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid JSON response from API: $e',
        );
      } else if (e is TypeError) {
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Type mismatch in API response parsing: $e',
        );
      }

      return Failure(
        errorType: ApiErrorType.unknown,
        message:
            'An unexpected error occurred while fetching booking by ID: $e',
      );
    }
  }

  /// Handle error response
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    AppLogger.w(
        'Failed request - Status: ${response.statusCode}', 'MYBOOKING-DETAIL-REPO');

    return Failure(
      errorType: ApiErrorType.server,
      message: 'Request failed with status code: ${response.statusCode}',
      statusCode: response.statusCode,
    );
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout ||
        e.type == DioExceptionType.sendTimeout) {
      AppLogger.e('Timeout error', e, null, 'MYBOOKING-DETAIL-REPO');
      return Failure(
        errorType: ApiErrorType.timeout,
        message: 'Request timeout. Please try again.',
      );
    }

    if (e.type == DioExceptionType.connectionError) {
      AppLogger.e('Connection error', e, null, 'MYBOOKING-DETAIL-REPO');
      return Failure(
        errorType: ApiErrorType.network,
        message: 'No internet connection. Please check your network.',
      );
    }

    if (e.response != null) {
      AppLogger.e('Server error: ${e.response?.statusCode}', e, null,
          'MYBOOKING-DETAIL-REPO');
      return Failure(
        errorType: ApiErrorType.server,
        message: e.response?.data?['message'] ?? 'Server error occurred',
        statusCode: e.response?.statusCode,
      );
    }

    AppLogger.e('Unknown Dio error', e, null, 'MYBOOKING-DETAIL-REPO');
    return Failure(
      errorType: ApiErrorType.unknown,
      message: 'Unknown error: ${e.message}',
    );
  }
}
