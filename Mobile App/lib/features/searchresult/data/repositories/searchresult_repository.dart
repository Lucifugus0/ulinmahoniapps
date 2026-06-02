import 'package:dio/dio.dart';
import 'package:intl/intl.dart';
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

  /// Daily Multi Tier Pricing: search rooms with availability checking and per-date pricing
  /// Uses /api/v1/search/rooms endpoint for daily period with date range
  /// Maps the grouped-by-property response back to [List] of [PropertyModel]
  Future<ApiResult<List<PropertyModel>>> searchRoomsDaily({
    String? type,
    required String checkIn,
    required String checkOut,
    String? province,
    String? city,
  }) async {
    try {
      /* Convert date format: filter stores dd-MM-yyyy, API expects yyyy-MM-dd */
      String apiCheckIn = checkIn;
      String apiCheckOut = checkOut;
      try {
        final inputFormat = DateFormat('dd-MM-yyyy');
        final outputFormat = DateFormat('yyyy-MM-dd');
        apiCheckIn = outputFormat.format(inputFormat.parse(checkIn));
        apiCheckOut = outputFormat.format(inputFormat.parse(checkOut));
      } catch (_) {
        /* If parsing fails, assume dates are already in yyyy-MM-dd format */
      }

      final queryParams = <String, dynamic>{
        'period': 'daily',
        'check_in': apiCheckIn,
        'check_out': apiCheckOut,
        if (type != null && type.isNotEmpty) 'type': type,
        if (province != null && province.isNotEmpty) 'province': province,
        if (city != null && city.isNotEmpty) 'city': city,
        'per_page': '50',
      };

      AppLogger.i('Daily search: $queryParams', 'SEARCH-REPO');

      final response = await _dioClient.get(
        '/api/v1/search/rooms',
        queryParameters: queryParams,
      );

      if (response.statusCode == 200) {
        final jsonResponse = response.data;

        if (jsonResponse is! Map<String, dynamic> || !jsonResponse.containsKey('data')) {
          return Failure(
            errorType: ApiErrorType.parsing,
            message: 'Format API response tidak terduga',
            statusCode: 200,
          );
        }

        final dataList = jsonResponse['data'] as List<dynamic>;

        /* Map search API response (grouped by property with rooms) to PropertyModel */
        final properties = dataList.map((propertyData) {
          final p = propertyData as Map<String, dynamic>;
          final rooms = (p['available_rooms'] as List<dynamic>?) ?? [];

          /* Get first room's total_days for the property-level field */
          final firstRoom = rooms.isNotEmpty ? rooms.first as Map<String, dynamic> : null;
          final totalDays = firstRoom?['total_days'];

          /* Build images array in the format PropertyModel.fromJson expects */
          final rawImages = p['images'] as List<dynamic>? ?? [];
          final images = rawImages.map((img) {
            if (img is Map<String, dynamic>) return img;
            return <String, dynamic>{};
          }).toList();

          return PropertyModel.fromJson({
            'idrec': p['id'],
            'slug': p['slug'],
            'tags': p['tags'] ?? '',
            'name': p['name'] ?? '',
            'description': '',
            'province': p['province'] ?? '',
            'city': p['city'] ?? '',
            'subdistrict': '',
            'village': '',
            'postal_code': '',
            'address': p['address'] ?? '',
            'location': '',
            'distance': '',
            'price_discounted_daily': '0',
            'price_discounted_monthly': '0',
            'price_original_daily': p['lowest_price']?.toString() ?? '0',
            'price_original_monthly': '0',
            'images': images,
            'thumbnail': null,
            'status': 1,
            'created_at': '',
            'updated_at': '',
            'total_rooms': rooms.length,
            'available_rooms': p['available_rooms_count'] ?? rooms.length,
            'gender': p['gender'],
            /* Daily Multi Tier Pricing: pass total price fields */
            'lowest_total_price': p['lowest_total_price'],
            'total_days': totalDays,
            'is_flat_rate': firstRoom?['is_flat_rate'],
          });
        }).toList();

        AppLogger.s('Daily search returned ${properties.length} properties', 'SEARCH-REPO');
        return Success(properties);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error in daily room search', e, stackTrace, 'SEARCH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal mencari kamar: ${e.toString()}',
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
