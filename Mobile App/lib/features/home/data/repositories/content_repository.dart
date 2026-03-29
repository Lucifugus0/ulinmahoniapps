import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';

/// Repository for content management — fetches taglines and hero video URL from API
class ContentRepository {
  final DioClient _dioClient;

  ContentRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient();

  /// Fetch a random active tagline from the API
  Future<ApiResult<String?>> fetchRandomTagline() async {
    try {
      final response = await _dioClient
          .get(ApiConfig.contentTagline.replaceFirst(ApiConfig.baseUrl, ''));

      if (response.statusCode == 200) {
        final data = response.data;
        if (data is Map<String, dynamic> && data['data'] != null) {
          final tagline = data['data']['tagline'] as String?;
          AppLogger.s('Fetched tagline: $tagline', 'CONTENT-REPO');
          return Success(tagline);
        }
        return Success(null);
      }

      return Failure(
        errorType: ApiErrorType.server,
        message: 'Failed to fetch tagline',
        statusCode: response.statusCode,
      );
    } on DioException catch (e) {
      AppLogger.e('DioException fetching tagline', e, null, 'CONTENT-REPO');
      return Failure(
        errorType: ApiErrorType.network,
        message: e.message ?? 'Network error',
        originalError: e,
      );
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching tagline', e, stackTrace, 'CONTENT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error fetching tagline: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch the active hero video URL from the API
  Future<ApiResult<String?>> fetchActiveVideoUrl() async {
    try {
      final response = await _dioClient
          .get(ApiConfig.contentHeroVideo.replaceFirst(ApiConfig.baseUrl, ''));

      if (response.statusCode == 200) {
        final data = response.data;
        if (data is Map<String, dynamic> && data['data'] != null) {
          final videoUrl = data['data']['video_url'] as String?;
          AppLogger.s('Fetched hero video URL: $videoUrl', 'CONTENT-REPO');
          return Success(videoUrl);
        }
        return Success(null);
      }

      return Failure(
        errorType: ApiErrorType.server,
        message: 'Failed to fetch hero video',
        statusCode: response.statusCode,
      );
    } on DioException catch (e) {
      AppLogger.e(
          'DioException fetching hero video', e, null, 'CONTENT-REPO');
      return Failure(
        errorType: ApiErrorType.network,
        message: e.message ?? 'Network error',
        originalError: e,
      );
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching hero video', e, stackTrace, 'CONTENT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Error fetching hero video: ${e.toString()}',
        originalError: e,
      );
    }
  }
}
