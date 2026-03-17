import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';
import '../../model/properties_model.dart';

/// Repository for Property operations (Home module)
class PropertyRepository {
  final DioClient _dioClient;

  PropertyRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Fetch all properties
  Future<ApiResult<List<PropertyModel>>> fetchProperties() async {
    try {
      final response = await _dioClient.get(ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, ''));

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        // Log removed - response too long

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

        final properties = dataList
            .map((item) => PropertyModel.fromJson(item as Map<String, dynamic>))
            .toList();


        AppLogger.s('Fetched ${properties.length} properties', 'PROPERTY-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error in fetchProperties', e, stackTrace, 'PROPERTY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error fetching properties: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch property by ID
  Future<ApiResult<PropertyModel?>> fetchPropertyById(int propertyId) async {
    try {
      final response = await _dioClient.get(ApiConfig.propertybyidUrl.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{propertyId}', propertyId.toString()));

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        Map<String, dynamic> data;
        if (jsonResponse is Map<String, dynamic> && jsonResponse.containsKey('data')) {
          data = jsonResponse['data'] as Map<String, dynamic>;
        } else if (jsonResponse is Map<String, dynamic>) {
          data = jsonResponse;
        } else {
          return Failure(
            errorType: ApiErrorType.parsing,
            message: 'Unexpected API response format for single property',
            statusCode: 200,
          );
        }

        final property = PropertyModel.fromJson(data);
        AppLogger.s('Fetched property: ${property.name}', 'PROPERTY-REPO');
        return Success(property);
      } else if (response.statusCode == 404) {
        AppLogger.i('Property with ID $propertyId not found', 'PROPERTY-REPO');
        return Success(null);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      if (e.response?.statusCode == 404) {
        return Success(null);
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching property by ID $propertyId', e, stackTrace, 'PROPERTY-REPO');
      return Success(null); // Return null on error for backward compatibility
    }
  }

  /// Fetch properties by tag
  Future<ApiResult<List<PropertyModel>>> fetchPropertiesByTag(String tag) async {
    try {
      final response = await _dioClient.get(ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, ''), queryParameters: {'tags': tag});

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        // Log removed - response too long

        List<dynamic> dataList;
        if (jsonResponse is Map<String, dynamic> && jsonResponse.containsKey('data')) {
          dataList = jsonResponse['data'] as List<dynamic>;
        } else if (jsonResponse is List) {
          dataList = jsonResponse;
        } else {
          return Failure(
            errorType: ApiErrorType.parsing,
            message: 'Unexpected API response format for properties by tag',
            statusCode: 200,
          );
        }

        final properties = dataList
            .map((item) => PropertyModel.fromJson(item as Map<String, dynamic>))
            .toList();

        AppLogger.s('Fetched ${properties.length} properties by tag: $tag', 'PROPERTY-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching properties by tag $tag', e, stackTrace, 'PROPERTY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error fetching properties by tag: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch properties by cities
  Future<ApiResult<List<PropertyModel>>> fetchPropertiesByCities(List<String> cities) async {
    if (cities.isEmpty) {
      AppLogger.w('No cities provided, returning empty list', 'PROPERTY-REPO');
      return Success([]);
    }

    try {
      final citiesParam = cities.join(',');
      final response = await _dioClient.get(ApiConfig.propertyUrl.replaceFirst(ApiConfig.baseUrl, ''), queryParameters: {'cities': citiesParam});

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
            message: 'Unexpected API response format for properties by cities',
            statusCode: 200,
          );
        }

        final properties = dataList
            .map((item) => PropertyModel.fromJson(item as Map<String, dynamic>))
            .toList();

        AppLogger.s('Fetched ${properties.length} properties by cities: $cities', 'PROPERTY-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching properties by cities $cities', e, stackTrace, 'PROPERTY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error fetching properties by cities: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch distinct cities (limit 5)
  Future<ApiResult<List<String>>> fetchDistinctCities({int limit = 5}) async {
    final result = await fetchProperties();

    return switch (result) {
      Success(:final data) => _extractDistinctCities(data, limit),
      Failure(:final errorType, :final message) => Failure(
          errorType: errorType,
          message: message,
        ),
    };
  }

  /// Extract distinct cities from properties
  ApiResult<List<String>> _extractDistinctCities(List<PropertyModel> properties, int limit) {
    try {
      final Set<String> distinctCities = {};

      for (var property in properties) {
        if (property.city != null && property.city.isNotEmpty) {
          distinctCities.add(property.city);
        }
      }

      final resultCities = distinctCities.toList();
      final limitedCities = resultCities.length > limit ? resultCities.sublist(0, limit) : resultCities;

      AppLogger.s('Fetched ${limitedCities.length} distinct cities', 'PROPERTY-REPO');
      return Success(limitedCities);
    } catch (e, stackTrace) {
      AppLogger.e('Error extracting distinct cities', e, stackTrace, 'PROPERTY-REPO');
      return Success([]); // Return empty list on error
    }
  }

  /// Fetch top 3 cheapest properties
  Future<ApiResult<List<PropertyModel>>> fetchTop3CheapestProperties() async {
    final result = await fetchProperties();

    return switch (result) {
      Success(:final data) => _extractTop3Cheapest(data),
      Failure(:final errorType, :final message) => Failure(
          errorType: errorType,
          message: message,
        ),
    };
  }

  /// Extract top 3 cheapest properties
  ApiResult<List<PropertyModel>> _extractTop3Cheapest(List<PropertyModel> properties) {
    try {
      properties.sort((a, b) => a.priceOriginalDaily.compareTo(b.priceOriginalDaily));
      final top3 = properties.take(3).toList();

      AppLogger.s('Fetched top 3 cheapest properties: ${top3.map((p) => p.name).toList()}', 'PROPERTY-REPO');
      return Success(top3);
    } catch (e, stackTrace) {
      AppLogger.e('Error extracting top 3 cheapest properties', e, stackTrace, 'PROPERTY-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error getting cheapest properties: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error responses from API
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode - $message', 'PROPERTY-REPO');

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
    AppLogger.e('DioException in repository', e, e.stackTrace, 'PROPERTY-REPO');

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
