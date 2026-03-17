import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../home/model/properties_model.dart';

/// Repository for search result operations
/// Note: This uses PropertyRepository functionality, consider consolidating in the future
class SearchResultRepository {
  final DioClient _dioClient;

  SearchResultRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Fetch properties by tag (search by tag)
  Future<ApiResult<List<PropertyModel>>> fetchPropertiesByTag(String tag) async {
    try {
      final response = await _dioClient.get(ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, ''), queryParameters: {'tags': tag});

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        List<dynamic> dataList;
        if (jsonResponse is Map<String, dynamic> && jsonResponse.containsKey('data')) {
          dataList = jsonResponse['data'] as List<dynamic>;
        } else if (jsonResponse is List) {
          dataList = jsonResponse;
        } else {
          return Failure(
            errorType: ApiErrorType.parsing,
            message: 'Format API response tidak terduga',
            statusCode: 200,
          );
        }

        final properties = dataList.map((item) => PropertyModel.fromJson(item as Map<String, dynamic>)).toList();

        AppLogger.s('Fetched ${properties.length} properties by tag: $tag', 'SEARCH-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching properties by tag: $tag', e, stackTrace, 'SEARCH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal memuat properti: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch properties by province
  Future<ApiResult<List<PropertyModel>>> fetchPropertiesByProvince(String province) async {
    try {
      final response = await _dioClient.get(ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, ''), queryParameters: {'province': province});

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        List<dynamic> dataList;
        if (jsonResponse is Map<String, dynamic> && jsonResponse.containsKey('data')) {
          dataList = jsonResponse['data'] as List<dynamic>;
        } else if (jsonResponse is List) {
          dataList = jsonResponse;
        } else {
          return Failure(
            errorType: ApiErrorType.parsing,
            message: 'Format API response tidak terduga',
            statusCode: 200,
          );
        }

        final properties = dataList.map((item) => PropertyModel.fromJson(item as Map<String, dynamic>)).toList();

        AppLogger.s('Fetched ${properties.length} properties by province: $province', 'SEARCH-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching properties by province: $province', e, stackTrace, 'SEARCH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal memuat properti: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode', 'SEARCH-REPO');

    return switch (statusCode) {
      400 => Failure(errorType: ApiErrorType.badRequest, message: message.isNotEmpty ? message : 'Permintaan tidak valid', statusCode: statusCode),
      404 => Failure(errorType: ApiErrorType.notFound, message: message.isNotEmpty ? message : 'Data tidak ditemukan', statusCode: statusCode),
      _ => Failure(errorType: ApiErrorType.unknown, message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)', statusCode: statusCode),
    };
  }

  /// Handle DioException
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException', e, e.stackTrace, 'SEARCH-REPO');

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
