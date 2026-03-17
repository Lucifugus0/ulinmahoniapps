import 'package:dio/dio.dart';
import 'package:dio_cookie_manager/dio_cookie_manager.dart';
import 'package:cookie_jar/cookie_jar.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../model/auth_model.dart';
import '../../../../../core/constants/api_constants.dart';

/// Repository for authentication operations
class AuthRepository {
  final DioClient _dioClient;
  final CookieJar _cookieJar;

  static const String _authTokenKey = 'auth_token';
  static const String _userProfileKey = 'user_profile';

  AuthRepository({DioClient? dioClient})
      : _dioClient = dioClient ?? DioClient(),
        _cookieJar = CookieJar() {
    // Add cookie manager to DioClient
    _dioClient.dio.interceptors.add(CookieManager(_cookieJar));
  }

  /// Login with email/phone and password
  Future<ApiResult<LoginResponse>> login(String login, String password) async {
    try {
      final requestData = {
        'login': login,
        'password': password,
      };

      final response = await _dioClient.post(
        ApiConfig.loginUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: requestData,
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data;

        if (data is Map<String, dynamic> &&
            data['status'] == 'success' &&
            data['data'] is Map &&
            data['data']['token'] != null) {
          final token = data['data']['token'] as String;
          final userMap = data['data']['user'] as Map<String, dynamic>?;

          final user = userMap != null ? User.fromJson(userMap) : null;

          final loginResponse = LoginResponse(
            token: token,
            user: user,
          );

          // Print cookies for debugging
          await _printCookiesInCookieJar();

          AppLogger.s('Login successful for: $login', 'AUTH-REPO');
          return Success(loginResponse);
        } else {
          final message = data['message'] ?? 'Login failed';
          AppLogger.w('Login failed: $message', 'AUTH-REPO');
          return Failure(
            errorType: ApiErrorType.unknown,
            message: message,
            statusCode: response.statusCode,
          );
        }
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error in login', e, stackTrace, 'AUTH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan tak terduga: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Fetch user profile by ID
  Future<ApiResult<User>> fetchUserProfileById(int userId) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.userById.replaceFirst(ApiConfig.baseUrl, '').replaceFirst('{userId}', userId.toString())
      );

      if (response.statusCode == 200) {
        final data = response.data;

        if (data is Map<String, dynamic> && data['data'] is Map) {
          final userMap = data['data'] as Map<String, dynamic>;
          final user = User.fromJson(userMap);

          AppLogger.s('Fetched profile for userId: $userId', 'AUTH-REPO');
          return Success(user);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Struktur data profil tidak valid',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching user profile for userId: $userId', e, stackTrace, 'AUTH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Check if email exists
  Future<ApiResult<Map<String, dynamic>>> checkEmailExists(String email) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.allUsers.replaceFirst(ApiConfig.baseUrl, ''),
        data: {'email': email},
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        final data = response.data as Map<String, dynamic>;
        AppLogger.s('Email check successful for: $email', 'AUTH-REPO');
        return Success(data);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error checking email: $email', e, stackTrace, 'AUTH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal cek email: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Register social account
  Future<ApiResult<void>> registerSocial({
    required String email,
    required String password,
    String? displayName,
  }) async {
    try {
      final requestData = {
        'email': email,
        'password': password,
        'name': displayName,
        'is_social': true,
      };

      final response = await _dioClient.post(
        '/auth/register',
        data: requestData,
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        AppLogger.s('Social registration successful for: $email', 'AUTH-REPO');
        return Success(null);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error in social registration for: $email', e, stackTrace, 'AUTH-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal registrasi social: ${e.toString()}',
        originalError: e,
      );
    }
  }

  // ==================== LOCAL STORAGE METHODS ====================

  /// Save login credentials locally
  Future<void> saveLoginCredentials({
    required String token,
    required User user,
    required bool shouldRemember,
  }) async {
    if (shouldRemember) {
      await _saveTokenLocally(token);
      await _saveUserLocally(user.toJson());
      AppLogger.s('Credentials saved (Remember Me enabled)', 'AUTH-REPO');
    } else {
      await _clearLocalToken();
      await clearLocalUser();
      AppLogger.i('Credentials not saved (Remember Me disabled)', 'AUTH-REPO');
    }
  }

  /// Save token to SharedPreferences
  Future<void> _saveTokenLocally(String token) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_authTokenKey, token);
      AppLogger.d('Token saved to SharedPreferences', 'AUTH-REPO');
    } catch (e) {
      AppLogger.e('Error saving token locally', e, null, 'AUTH-REPO');
    }
  }

  /// Get token from SharedPreferences
  Future<String?> getTokenLocally() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString(_authTokenKey);
      if (token == null) {
        AppLogger.d('No token found in SharedPreferences', 'AUTH-REPO');
        return null;
      }
      AppLogger.d('Token retrieved from SharedPreferences', 'AUTH-REPO');
      return token;
    } catch (e) {
      AppLogger.e('Error getting token locally', e, null, 'AUTH-REPO');
      return null;
    }
  }

  /// Clear token from SharedPreferences
  Future<void> _clearLocalToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_authTokenKey);
      AppLogger.d('Token cleared from SharedPreferences', 'AUTH-REPO');
    } catch (e) {
      AppLogger.e('Error clearing token', e, null, 'AUTH-REPO');
    }
  }

  /// Save user to SharedPreferences
  Future<void> _saveUserLocally(Map<String, dynamic> user) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userJson = jsonEncode(user);
      await prefs.setString(_userProfileKey, userJson);
      AppLogger.d('User data saved to SharedPreferences', 'AUTH-REPO');
    } catch (e) {
      AppLogger.e('Error saving user locally', e, null, 'AUTH-REPO');
    }
  }

  /// Get user from SharedPreferences
  Future<Map<String, dynamic>?> getUserLocally() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userJson = prefs.getString(_userProfileKey);

      if (userJson == null) {
        AppLogger.d('No user data found in SharedPreferences', 'AUTH-REPO');
        return null;
      }

      final user = jsonDecode(userJson) as Map<String, dynamic>;
      AppLogger.d('User data retrieved from SharedPreferences', 'AUTH-REPO');
      return user;
    } catch (e) {
      AppLogger.e('Error getting user locally', e, null, 'AUTH-REPO');
      return null;
    }
  }

  /// Update user profile locally
  Future<void> updateUserProfileLocally(Map<String, dynamic> updatedUser) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final userJson = jsonEncode(updatedUser);
      await prefs.setString(_userProfileKey, userJson);
      AppLogger.d('User profile updated in SharedPreferences', 'AUTH-REPO');
    } catch (e) {
      AppLogger.e('Error updating user profile locally', e, null, 'AUTH-REPO');
    }
  }

  /// Clear user from SharedPreferences
  Future<void> clearLocalUser() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_userProfileKey);
      AppLogger.d('User data cleared from SharedPreferences', 'AUTH-REPO');
    } catch (e) {
      AppLogger.e('Error clearing user data', e, null, 'AUTH-REPO');
    }
  }

  /// Logout - clear all local data and cookies
  Future<void> logout() async {
    await _clearLocalToken();
    await clearLocalUser();

    // Clear all cookies
    try {
      await _cookieJar.deleteAll();
      AppLogger.s('All cookies cleared for logout', 'AUTH-REPO');
      await _printCookiesInCookieJar();
    } catch (e) {
      AppLogger.e('Error clearing cookies', e, null, 'AUTH-REPO');
    }

    AppLogger.s('User logged out successfully', 'AUTH-REPO');
  }

  /// Print cookies in CookieJar for debugging
  Future<void> _printCookiesInCookieJar() async {
    try {
      final baseUrl = _dioClient.dio.options.baseUrl;
      final List<Cookie> cookies = await _cookieJar.loadForRequest(Uri.parse(baseUrl));

      if (cookies.isEmpty) {
        AppLogger.d('No cookies found in CookieJar for $baseUrl', 'AUTH-REPO');
      } else {
        AppLogger.d('Cookies stored in CookieJar for $baseUrl:', 'AUTH-REPO');
        for (var cookie in cookies) {
          AppLogger.d('  - ${cookie.name}: ${cookie.value} (expires: ${cookie.expires})', 'AUTH-REPO');
        }
      }
    } catch (e) {
      AppLogger.e('Error printing cookies from CookieJar', e, null, 'AUTH-REPO');
    }
  }

  // ==================== ERROR HANDLING ====================

  /// Handle error responses from API
  ApiResult<T> _handleErrorResponse<T>(Response response) {
    final statusCode = response.statusCode ?? 0;
    final data = response.data;
    final message = data is Map<String, dynamic> ? data['message'] ?? '' : '';

    AppLogger.w('API error response: Status $statusCode - $message', 'AUTH-REPO');

    return switch (statusCode) {
      400 => Failure(
          errorType: ApiErrorType.badRequest,
          message: message.isNotEmpty ? message : 'Permintaan tidak valid',
          statusCode: statusCode,
        ),
      401 => Failure(
          errorType: ApiErrorType.unauthorized,
          message: message.isNotEmpty ? message : 'Password atau email/no telpon kurang tepat',
          statusCode: statusCode,
        ),
      403 => Failure(
          errorType: ApiErrorType.forbidden,
          message: message.isNotEmpty ? message : 'Akses ditolak',
          statusCode: statusCode,
        ),
      404 => Failure(
          errorType: ApiErrorType.notFound,
          message: 'Endpoint tidak ditemukan',
          statusCode: statusCode,
        ),
      422 => _handleValidationError(data),
      500 => Failure(
          errorType: ApiErrorType.server,
          message: 'Terjadi kesalahan server',
          statusCode: statusCode,
        ),
      _ => Failure(
          errorType: ApiErrorType.unknown,
          message: message.isNotEmpty ? message : 'Terjadi kesalahan ($statusCode)',
          statusCode: statusCode,
        ),
    };
  }

  /// Handle 422 validation errors
  Failure<T> _handleValidationError<T>(dynamic data) {
    if (data is Map<String, dynamic> && data['errors'] != null) {
      final errors = data['errors'];
      if (errors is Map) {
        final errorMessages = errors.values
            .map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString())
            .join(', ');

        AppLogger.w('Validation error: $errorMessages', 'AUTH-REPO');

        return Failure(
          errorType: ApiErrorType.validation,
          message: errorMessages,
          statusCode: 422,
        );
      }
    }

    return Failure(
      errorType: ApiErrorType.validation,
      message: data is Map ? (data['message'] ?? 'Data tidak valid') : 'Data tidak valid',
      statusCode: 422,
    );
  }

  /// Handle DioException errors
  ApiResult<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in repository', e, e.stackTrace, 'AUTH-REPO');

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

/// Login response model
class LoginResponse {
  final String token;
  final User? user;

  LoginResponse({
    required this.token,
    this.user,
  });
}
