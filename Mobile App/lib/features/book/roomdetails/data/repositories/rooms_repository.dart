import 'package:dio/dio.dart';
import 'dart:convert';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/rooms_model.dart';

/// Repository for room operations
class RoomsRepository {
  final DioClient _dioClient;

  RoomsRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Get rooms by property ID
  Future<ApiResult<List<RoomModel>>> getRoomsByPropertyId(int propertyId) async {
    try {
      final url = ApiConfig.roomdetailUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{propertyId}', propertyId.toString());
      AppLogger.d('=== ROOMS REQUEST ===', 'ROOMS-REPO');
      AppLogger.d('URL: $url', 'ROOMS-REPO');
      AppLogger.d('Property ID: $propertyId', 'ROOMS-REPO');

      final response = await _dioClient.get(url);

      AppLogger.d('=== ROOMS RESPONSE ===', 'ROOMS-REPO');
      AppLogger.d('Status Code: ${response.statusCode}', 'ROOMS-REPO');

      // Log response in chunks to avoid truncation
      final responseJson = jsonEncode(response.data);
      final chunkSize = 800;
      for (int i = 0; i < responseJson.length; i += chunkSize) {
        final end = (i + chunkSize < responseJson.length) ? i + chunkSize : responseJson.length;
        AppLogger.d('Response Part ${(i ~/ chunkSize) + 1}: ${responseJson.substring(i, end)}', 'ROOMS-REPO');
      }

      if (response.statusCode == 200 || response.statusCode == 201) {
        final body = response.data;

        if (body is Map && body.containsKey('data') && body['data'] is List) {
          final List<dynamic> roomDataList = body['data'];

          if (roomDataList.isEmpty) {
            AppLogger.i('No rooms found for property: $propertyId', 'ROOMS-REPO');
            return Success([]);
          }

          final rooms = <RoomModel>[];
          for (var i = 0; i < roomDataList.length; i++) {
            try {
              final roomJson = roomDataList[i] as Map<String, dynamic>;
              final room = RoomModel.fromJson(roomJson);
              rooms.add(room);
            } catch (e, st) {
              AppLogger.e('Failed to parse room at index $i for property $propertyId', e, st, 'ROOMS-REPO');
              AppLogger.w('Room JSON that failed: ${roomDataList[i]}', 'ROOMS-REPO');
            }
          }

          AppLogger.s('Fetched ${rooms.length} rooms for property: $propertyId', 'ROOMS-REPO');
          return Success(rooms);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Format data tidak valid',
          statusCode: response.statusCode,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching rooms for property: $propertyId', e, stackTrace, 'ROOMS-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Tidak dapat mengambil kamar: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Get single room by room ID
  Future<ApiResult<RoomModel>> getRoomById(int roomId) async {
    try {
      final url = ApiConfig.roombyidUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{roomId}', roomId.toString());
      AppLogger.d('=== ROOM BY ID REQUEST ===', 'ROOMS-REPO');
      AppLogger.d('URL: $url', 'ROOMS-REPO');
      AppLogger.d('Room ID: $roomId', 'ROOMS-REPO');

      final response = await _dioClient.get(url);

      AppLogger.d('=== ROOM BY ID RESPONSE ===', 'ROOMS-REPO');
      AppLogger.d('Status Code: ${response.statusCode}', 'ROOMS-REPO');

      // Log response in chunks to avoid truncation
      final responseJson = jsonEncode(response.data);
      final chunkSize = 800; // Flutter log max is usually around 1000
      for (int i = 0; i < responseJson.length; i += chunkSize) {
        final end = (i + chunkSize < responseJson.length) ? i + chunkSize : responseJson.length;
        AppLogger.d('Response Part ${(i ~/ chunkSize) + 1}: ${responseJson.substring(i, end)}', 'ROOMS-REPO');
      }

      if (response.statusCode == 200 || response.statusCode == 201) {
        final body = response.data;

        if (body is Map && body.containsKey('data')) {
          final roomData = body['data'];

          if (roomData is Map<String, dynamic>) {
            try {
              final room = RoomModel.fromJson(roomData);
              AppLogger.s('Successfully fetched room: ${room.name} (ID: $roomId)', 'ROOMS-REPO');
              AppLogger.d('🔍 Parsed Room Data:', 'ROOMS-REPO');
              AppLogger.d('  - Deposit Fee: ${room.depositFee}', 'ROOMS-REPO');
              AppLogger.d('  - Parking Fees Count: ${room.parkingFees.length}', 'ROOMS-REPO');
              for (var parking in room.parkingFees) {
                AppLogger.d('    * Type: ${parking.parkingType}, Fee: ${parking.fee}, Capacity: ${parking.capacity}', 'ROOMS-REPO');
              }
              return Success(room);
            } catch (e, st) {
              AppLogger.e('Failed to parse room data for ID $roomId', e, st, 'ROOMS-REPO');
              AppLogger.w('Room JSON that failed: $roomData', 'ROOMS-REPO');
              return Failure(
                errorType: ApiErrorType.parsing,
                message: 'Gagal memproses data kamar',
                originalError: e,
              );
            }
          }
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Format data tidak valid',
          statusCode: response.statusCode,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching room by ID: $roomId', e, stackTrace, 'ROOMS-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Tidak dapat mengambil data kamar: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode', 'ROOMS-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      401 => Failure(errorType: ApiErrorType.unauthorized, message: message.isNotEmpty ? message : 'Sesi berakhir', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Kamar tidak ditemukan', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'ROOMS-REPO');

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
