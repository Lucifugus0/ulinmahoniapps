import 'package:dio/dio.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../../utils/app_logger.dart';

/// Dio interceptor for handling authentication
/// - Automatically adds auth token to requests if available
/// - Handles 401 responses globally
class AuthInterceptor extends Interceptor {
  final Function()? onUnauthorized;
  static const String _authTokenKey = 'auth_token';

  AuthInterceptor({this.onUnauthorized});

  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) async {
    try {
      // Try to get token from SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString(_authTokenKey);

      if (token != null && token.isNotEmpty) {
        // Add Bearer token to Authorization header
        options.headers['Authorization'] = 'Bearer $token';
        AppLogger.d('Auth token added to request: ${options.path}', 'AUTH-INTERCEPTOR');
      } else {
        AppLogger.d('No auth token found for request: ${options.path}', 'AUTH-INTERCEPTOR');
      }
    } catch (e) {
      AppLogger.e('Error adding auth token to request', e, null, 'AUTH-INTERCEPTOR');
    }

    handler.next(options);
  }

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) async {
    if (err.response?.statusCode == 401) {
      AppLogger.w(
        'Unauthorized request detected - Session expired',
        'AUTH',
      );

      // Trigger logout/redirect callback if provided
      if (onUnauthorized != null) {
        AppLogger.i('Triggering onUnauthorized callback', 'AUTH');
        onUnauthorized!();
      }
    }

    handler.next(err);
  }
}
