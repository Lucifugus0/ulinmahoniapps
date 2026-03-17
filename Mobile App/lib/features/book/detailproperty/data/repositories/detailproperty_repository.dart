import 'package:dio/dio.dart';
import 'dart:convert';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/detailproperty_model.dart';

/// Repository for property detail operations
class DetailPropertyRepository {
  final DioClient _dioClient;

  DetailPropertyRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Fetch property details by ID
  Future<ApiResult<DetailPropertyModel>> fetchDetailProperty(int propertyId) async {
    try {
      final url = ApiConfig.detailpropertyUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{propertyId}', propertyId.toString());
      AppLogger.d('=== DETAIL PROPERTY REQUEST ===', 'DETAIL-PROPERTY-REPO');
      AppLogger.d('URL: $url', 'DETAIL-PROPERTY-REPO');
      AppLogger.d('Property ID: $propertyId', 'DETAIL-PROPERTY-REPO');

      final response = await _dioClient.get(url);

      AppLogger.d('=== DETAIL PROPERTY RESPONSE ===', 'DETAIL-PROPERTY-REPO');
      AppLogger.d('Status Code: ${response.statusCode}', 'DETAIL-PROPERTY-REPO');

      // Log response in chunks to avoid truncation
      final responseJson = jsonEncode(response.data);
      final chunkSize = 800;
      for (int i = 0; i < responseJson.length; i += chunkSize) {
        final end = (i + chunkSize < responseJson.length) ? i + chunkSize : responseJson.length;
        AppLogger.d('Response Part ${(i ~/ chunkSize) + 1}: ${responseJson.substring(i, end)}', 'DETAIL-PROPERTY-REPO');
      }

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data != null && data['data'] != null) {
          final property = DetailPropertyModel.fromJson(data['data']);
          AppLogger.s('Fetched detail property: $propertyId', 'DETAIL-PROPERTY-REPO');
          return Success(property);
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
      AppLogger.e('Error fetching detail property: $propertyId', e, stackTrace, 'DETAIL-PROPERTY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Tidak dapat mengambil detail properti: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode', 'DETAIL-PROPERTY-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      401 => Failure(errorType: ApiErrorType.unauthorized, message: message.isNotEmpty ? message : 'Sesi berakhir. Login ulang', statusCode: statusCode),
      403 => Failure(errorType: ApiErrorType.forbidden, message: message.isNotEmpty ? message : 'Akses ditolak', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Detail properti tidak ditemukan', statusCode: statusCode),
      422 => _handleValidationError(data),
      500 => Failure(errorType: ApiErrorType.server, message: message.isNotEmpty ? message : 'Kesalahan server', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle validation errors
  Failure<T> _handleValidationError<T>(dynamic data) {
    if (data is Map<String, dynamic> && data['errors'] != null) {
      final errors = data['errors'];
      if (errors is Map) {
        final errorMessages = errors.values.map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString()).join(', ');
        return Failure(errorType: ApiErrorType.validation, message: errorMessages, statusCode: 422);
      }
    }
    return Failure(errorType: ApiErrorType.validation, message: 'Data tidak valid', statusCode: 422);
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'DETAIL-PROPERTY-REPO');

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
