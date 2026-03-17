import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../home/model/properties_model.dart';

/// Repository for PropertyType operations
class PropertyTypeRepository {
  final DioClient _dioClient;

  PropertyTypeRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient();

  /// Fetch representative property types
  /// Returns one property per unique tag/type where status = 1 (available)
  Future<ApiResult<List<PropertyModel>>> fetchRepresentativePropertyTypes() async {
    try {
      // Fetch all properties using the main property endpoint
      final response = await _dioClient.get(
        ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, '')
      );

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
            message: 'Unexpected API response format',
            statusCode: 200,
          );
        }

        final allProperties = dataList
            .map((item) => PropertyModel.fromJson(item as Map<String, dynamic>))
            .toList();

        AppLogger.d('Fetched ${allProperties.length} total properties', 'PROPERTYTYPE-REPO');

        // Extract representative properties (one per type)
        final Map<String, PropertyModel> representativeMap = {};

        for (var property in allProperties) {
          // Only include properties with tags and status = 1 (available)
          if (property.tags.isNotEmpty && property.status == 1) {
            final type = property.tags.toLowerCase();

            // Add first property of each type
            if (!representativeMap.containsKey(type)) {
              representativeMap[type] = property;
              AppLogger.d(
                'Added representative for type: $type (${property.name})',
                'PROPERTYTYPE-REPO'
              );
            }
          }
        }

        final representativeList = representativeMap.values.toList();

        AppLogger.s(
          'Found ${representativeList.length} unique property types',
          'PROPERTYTYPE-REPO'
        );

        return Success(representativeList);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e(
        'Unexpected error in fetchRepresentativePropertyTypes',
        e,
        stackTrace,
        'PROPERTYTYPE-REPO'
      );
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch representative property types: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses from API
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode - $message', 'PROPERTYTYPE-REPO');

    return switch (statusCode) {
      400 => Failure(
          errorType: ApiErrorType.badRequest,
          message: message.isNotEmpty ? message : 'Permintaan tidak valid',
          statusCode: statusCode,
        ),
      401 => Failure(
          errorType: ApiErrorType.unauthorized,
          message: message.isNotEmpty ? message : 'Sesi Anda telah berakhir',
          statusCode: statusCode,
        ),
      403 => Failure(
          errorType: ApiErrorType.forbidden,
          message: message.isNotEmpty ? message : 'Akses ditolak',
          statusCode: statusCode,
        ),
      404 => Failure(
          errorType: ApiErrorType.notFound,
          message: message.isNotEmpty ? message : 'Data tidak ditemukan',
          statusCode: statusCode,
        ),
      _ => Failure(
          errorType: ApiErrorType.unknown,
          message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)',
          statusCode: statusCode,
        ),
    };
  }

  /// Handle DioException errors
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in repository', e, e.stackTrace, 'PROPERTYTYPE-REPO');

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
