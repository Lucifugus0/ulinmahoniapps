import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';
import '../../model/promo_banner_model.dart';

/// Repository for promo banner operations
/// Handles all promo banner-related API calls following backend documentation
class PromoBannerRepository {
  final DioClient _dioClient;

  PromoBannerRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Get active promo banners
  /// GET /promo-banners
  /// Returns list of active promo banners sorted by priority
  Future<ApiResult<List<PromoBannerModel>>> getActiveBanners() async {
    try {
      final response = await _dioClient.get(
        ApiConfig.promoBanners,
      );

      if (response.statusCode == 200) {
        final body = response.data;

        AppLogger.separator('PROMO BANNER API RESPONSE DEBUG');
        AppLogger.d('Response status code: ${response.statusCode}', 'PROMO-BANNER-REPO');
        AppLogger.d('Response body type: ${body.runtimeType}', 'PROMO-BANNER-REPO');
        AppLogger.d('Response body: $body', 'PROMO-BANNER-REPO');

        if (body is Map) {
          AppLogger.d('Body is Map ✓', 'PROMO-BANNER-REPO');
          AppLogger.d('Body keys: ${body.keys.toList()}', 'PROMO-BANNER-REPO');
          AppLogger.d('Has "status" key: ${body.containsKey('status')}', 'PROMO-BANNER-REPO');
          AppLogger.d('Has "data" key: ${body.containsKey('data')}', 'PROMO-BANNER-REPO');

          if (body.containsKey('data')) {
            AppLogger.d('body["data"] type: ${body['data'].runtimeType}', 'PROMO-BANNER-REPO');
            AppLogger.d('body["data"] is List: ${body['data'] is List}', 'PROMO-BANNER-REPO');
            AppLogger.d('body["data"] value: ${body['data']}', 'PROMO-BANNER-REPO');
          }

          if (body.containsKey('status')) {
            AppLogger.d('body["status"] value: ${body['status']}', 'PROMO-BANNER-REPO');
          }
        } else {
          AppLogger.w('Body is NOT Map! Type: ${body.runtimeType}', 'PROMO-BANNER-REPO');
        }
        AppLogger.separator();

        // Handle both response formats:
        // Format 1: { "status": "success", "data": [...] }
        // Format 2: { "data": [...] }
        if (body is Map) {
          List? dataList;

          // Check format 1 (with status wrapper)
          if (body['status'] == 'success' && body['data'] is List) {
            dataList = body['data'] as List;
            AppLogger.i('✓ Format 1 detected (with status wrapper)', 'PROMO-BANNER-REPO');
          }
          // Check format 2 (direct data array)
          else if (body['data'] is List) {
            dataList = body['data'] as List;
            AppLogger.i('✓ Format 2 detected (direct data array)', 'PROMO-BANNER-REPO');
          } else {
            AppLogger.w('✗ No valid format detected', 'PROMO-BANNER-REPO');
            AppLogger.w('  - body["status"] = ${body['status']}', 'PROMO-BANNER-REPO');
            AppLogger.w('  - body["data"] is List = ${body['data'] is List}', 'PROMO-BANNER-REPO');
          }

          if (dataList != null) {
            AppLogger.i('Processing ${dataList.length} banners...', 'PROMO-BANNER-REPO');

            try {
              final allBanners = dataList
                  .map((e) => PromoBannerModel.fromJson(e as Map<String, dynamic>))
                  .toList();

              // Filter only active banners (status = 1)
              final activeBanners = allBanners.where((banner) => banner.status == 1).toList();

              AppLogger.s('✓ Successfully fetched ${allBanners.length} promo banners (${activeBanners.length} active)', 'PROMO-BANNER-REPO');
              return Success(activeBanners);
            } catch (e, stackTrace) {
              AppLogger.e('Error parsing banner models', e, stackTrace, 'PROMO-BANNER-REPO');
              return Failure(
                errorType: ApiErrorType.parsing,
                message: 'Failed to parse banner data: ${e.toString()}',
                statusCode: 200,
              );
            }
          }
        }

        AppLogger.w('✗ Invalid response format - body is not Map or data is not List', 'PROMO-BANNER-REPO');
        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      // Return empty list for 404 or 401 (unauthenticated)
      if (e.response?.statusCode == 404) {
        AppLogger.i('No promo banners found (404)', 'PROMO-BANNER-REPO');
        return Success([]);
      }
      if (e.response?.statusCode == 401) {
        AppLogger.w('Promo banners endpoint requires authentication (401) - returning empty list', 'PROMO-BANNER-REPO');
        return Success([]);
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching promo banners', e, stackTrace, 'PROMO-BANNER-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch promo banners: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Get promo banner detail by ID
  /// GET /promo-banner?id={id}
  /// Returns specific promo banner detail
  Future<ApiResult<PromoBannerModel>> getBannerById(int bannerId) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.promoBannerbyId(bannerId),
      );

      if (response.statusCode == 200) {
        final body = response.data;

        AppLogger.separator('PROMO BANNER DETAIL API RESPONSE DEBUG');
        AppLogger.d('Response status code: ${response.statusCode}', 'PROMO-BANNER-REPO');
        AppLogger.d('Response body: $body', 'PROMO-BANNER-REPO');

        // Handle both response formats:
        // Format 1: { "status": "success", "data": {...} }
        // Format 2: { "data": {...} }
        if (body is Map) {
          Map<String, dynamic>? bannerData;

          // Check format 1 (with status wrapper)
          if (body['status'] == 'success' && body['data'] != null) {
            bannerData = body['data'] as Map<String, dynamic>;
          }
          // Check format 2 (direct data object)
          else if (body['data'] != null) {
            bannerData = body['data'] as Map<String, dynamic>;
          }

          if (bannerData != null) {
            AppLogger.d('Banner data before parsing: $bannerData', 'PROMO-BANNER-REPO');

            // Log images array specifically
            if (bannerData['images'] != null) {
              final imagesArray = bannerData['images'];
              AppLogger.d('Images array type: ${imagesArray.runtimeType}', 'PROMO-BANNER-REPO');
              AppLogger.d('Images array length: ${imagesArray is List ? imagesArray.length : 'not a list'}', 'PROMO-BANNER-REPO');
              AppLogger.d('Images array content: $imagesArray', 'PROMO-BANNER-REPO');
            }

            final banner = PromoBannerModel.fromJson(bannerData);
            AppLogger.d('Parsed banner.images.length: ${banner.images.length}', 'PROMO-BANNER-REPO');
            AppLogger.s('✓ Fetched promo banner detail: $bannerId', 'PROMO-BANNER-REPO');
            AppLogger.separator();
            return Success(banner);
          }
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching banner detail', e, stackTrace, 'PROMO-BANNER-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch banner detail: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error response
  Failure<T> _handleErrorResponse<T>(Response response) {
    final body = response.data;
    String message = 'An error occurred';

    if (body is Map) {
      message = body['message'] ?? message;
    }

    // Handle specific status codes
    if (response.statusCode == 403) {
      return Failure(
        errorType: ApiErrorType.unauthorized,
        message: message,
        statusCode: 403,
      );
    }

    if (response.statusCode == 404) {
      return Failure(
        errorType: ApiErrorType.notFound,
        message: message,
        statusCode: 404,
      );
    }

    if (response.statusCode == 422) {
      // Include validation errors in message if available
      final validationMessage = body is Map && body['errors'] != null
          ? '$message: ${body['errors']}'
          : message;
      return Failure(
        errorType: ApiErrorType.validation,
        message: validationMessage,
        statusCode: 422,
      );
    }

    return Failure(
      errorType: ApiErrorType.server,
      message: message,
      statusCode: response.statusCode,
    );
  }

  /// Handle Dio exceptions
  Failure<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in PromoBannerRepository', e, e.stackTrace, 'PROMO-BANNER-REPO');

    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout ||
        e.type == DioExceptionType.sendTimeout) {
      return Failure(
        errorType: ApiErrorType.timeout,
        message: 'Request timeout. Please check your connection.',
        originalError: e,
      );
    }

    if (e.type == DioExceptionType.connectionError) {
      return Failure(
        errorType: ApiErrorType.network,
        message: 'No internet connection',
        originalError: e,
      );
    }

    if (e.response != null) {
      return _handleErrorResponse(e.response!);
    }

    return Failure(
      errorType: ApiErrorType.unknown,
      message: e.message ?? 'Unknown error occurred',
      originalError: e,
    );
  }
}
