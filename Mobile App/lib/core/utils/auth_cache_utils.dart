import 'package:shared_preferences/shared_preferences.dart';
import 'app_logger.dart';

/// Utility class for managing authentication cache with expiry
///
/// Handles "Remember Me" login session with 1-week expiry
class AuthCacheUtils {
  // Cache duration: 1 week (7 days)
  static const Duration _sessionDuration = Duration(days: 7);

  // Keys
  static const String _rememberMeKey = 'auth_remember_me';
  static const String _sessionExpiryKey = 'auth_session_expiry';
  static const String _userTokenKey = 'auth_user_token';
  static const String _userIdKey = 'auth_user_id';
  static const String _userEmailKey = 'auth_user_email';

  /// Save "Remember Me" session with 1-week expiry
  static Future<void> saveRememberMeSession({
    required String token,
    required String userId,
    required String email,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final expiryTime = DateTime.now().add(_sessionDuration);

      await prefs.setBool(_rememberMeKey, true);
      await prefs.setString(_sessionExpiryKey, expiryTime.toIso8601String());
      await prefs.setString(_userTokenKey, token);
      await prefs.setString(_userIdKey, userId);
      await prefs.setString(_userEmailKey, email);

      AppLogger.d(
        'Remember Me session saved, expires at: $expiryTime (7 days from now)',
        'AUTH-CACHE',
      );
    } catch (e) {
      AppLogger.e('Failed to save Remember Me session', e, StackTrace.current, 'AUTH-CACHE');
    }
  }

  /// Get "Remember Me" session if not expired
  /// Returns null if expired or not found
  static Future<Map<String, String>?> getRememberMeSession() async {
    try {
      final prefs = await SharedPreferences.getInstance();

      final rememberMe = prefs.getBool(_rememberMeKey) ?? false;
      if (!rememberMe) {
        AppLogger.d('Remember Me not enabled', 'AUTH-CACHE');
        return null;
      }

      final expiryString = prefs.getString(_sessionExpiryKey);
      final token = prefs.getString(_userTokenKey);
      final userId = prefs.getString(_userIdKey);
      final email = prefs.getString(_userEmailKey);

      if (expiryString == null || token == null || userId == null || email == null) {
        AppLogger.w('Remember Me session incomplete', 'AUTH-CACHE');
        await clearRememberMeSession();
        return null;
      }

      // Check expiry
      final expiryTime = DateTime.parse(expiryString);
      if (DateTime.now().isAfter(expiryTime)) {
        AppLogger.w(
          'Remember Me session expired (expired at: $expiryTime)',
          'AUTH-CACHE',
        );
        // Delete expired session
        await clearRememberMeSession();
        return null;
      }

      final daysRemaining = expiryTime.difference(DateTime.now()).inDays;
      AppLogger.d(
        'Remember Me session valid (expires in $daysRemaining days)',
        'AUTH-CACHE',
      );

      return {
        'token': token,
        'userId': userId,
        'email': email,
        'expiryTime': expiryString,
      };
    } catch (e) {
      AppLogger.e('Failed to get Remember Me session', e, StackTrace.current, 'AUTH-CACHE');
      return null;
    }
  }

  /// Check if Remember Me session is still valid
  static Future<bool> isRememberMeSessionValid() async {
    final session = await getRememberMeSession();
    return session != null;
  }

  /// Get remaining days until session expiry
  static Future<int?> getSessionRemainingDays() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final expiryString = prefs.getString(_sessionExpiryKey);

      if (expiryString == null) return null;

      final expiryTime = DateTime.parse(expiryString);
      if (DateTime.now().isAfter(expiryTime)) return 0;

      return expiryTime.difference(DateTime.now()).inDays;
    } catch (e) {
      AppLogger.e('Failed to get session remaining days', e, StackTrace.current, 'AUTH-CACHE');
      return null;
    }
  }

  /// Extend Remember Me session by 1 week from now
  /// Call this when user actively uses the app
  static Future<void> extendRememberMeSession() async {
    try {
      final session = await getRememberMeSession();
      if (session == null) {
        AppLogger.d('No active session to extend', 'AUTH-CACHE');
        return;
      }

      final prefs = await SharedPreferences.getInstance();
      final newExpiryTime = DateTime.now().add(_sessionDuration);
      await prefs.setString(_sessionExpiryKey, newExpiryTime.toIso8601String());

      AppLogger.d(
        'Remember Me session extended to: $newExpiryTime',
        'AUTH-CACHE',
      );
    } catch (e) {
      AppLogger.e('Failed to extend Remember Me session', e, StackTrace.current, 'AUTH-CACHE');
    }
  }

  /// Clear Remember Me session (logout)
  static Future<void> clearRememberMeSession() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_rememberMeKey);
      await prefs.remove(_sessionExpiryKey);
      await prefs.remove(_userTokenKey);
      await prefs.remove(_userIdKey);
      await prefs.remove(_userEmailKey);

      AppLogger.d('Remember Me session cleared', 'AUTH-CACHE');
    } catch (e) {
      AppLogger.e('Failed to clear Remember Me session', e, StackTrace.current, 'AUTH-CACHE');
    }
  }

  /// Disable Remember Me but keep other auth data
  static Future<void> disableRememberMe() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setBool(_rememberMeKey, false);

      AppLogger.d('Remember Me disabled', 'AUTH-CACHE');
    } catch (e) {
      AppLogger.e('Failed to disable Remember Me', e, StackTrace.current, 'AUTH-CACHE');
    }
  }
}
