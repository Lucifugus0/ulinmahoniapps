import '../../constants/api_constants.dart';
import '../../network/dio_client.dart';
import '../../utils/app_logger.dart';

/// Repository for registering and removing FCM device tokens from the backend.
///
/// API endpoints used:
///   POST   /device-token  → register or update device token (upsert)
///   DELETE /device-token  → remove device token on logout
///
/// Authentication is handled automatically by [DioClient]'s [AuthInterceptor]
/// which injects the Bearer token from SharedPreferences on every request.
class FCMRepository {
  final DioClient _dioClient;

  FCMRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Register or update a device token with the backend.
  ///
  /// Called on login/startup and whenever Firebase refreshes the token.
  /// The backend performs an upsert — if the same token already exists for
  /// the user it updates [deviceType] and [deviceName] instead of duplicating.
  ///
  /// [token]      - FCM token from Firebase SDK
  /// [deviceType] - "android" or "ios"
  /// [deviceName] - Human-readable device name (e.g. "Samsung Galaxy S24")
  Future<bool> registerToken({
    required String token,
    required String deviceType,
    String? deviceName,
  }) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.fcmToken.replaceFirst(ApiConfig.baseUrl, ''),
        data: {
          'token': token,
          'device_type': deviceType,
          if (deviceName != null) 'device_name': deviceName,
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        AppLogger.s('FCM token registered with backend', 'FCM-REPO');
        return true;
      }

      AppLogger.w('FCM token register failed: ${response.statusCode}', 'FCM-REPO');
      return false;
    } catch (e, stackTrace) {
      AppLogger.e('Failed to register FCM token', e, stackTrace, 'FCM-REPO');
      return false;
    }
  }

  /// Remove a device token from the backend on logout.
  ///
  /// Prevents push notifications from being sent to signed-out devices.
  ///
  /// [token] - FCM token to remove (same token that was registered)
  Future<bool> deleteToken({required String token}) async {
    try {
      final response = await _dioClient.delete(
        ApiConfig.fcmTokenDelete.replaceFirst(ApiConfig.baseUrl, ''),
        data: {'token': token},
      );

      if (response.statusCode == 200) {
        AppLogger.s('FCM token deleted from backend', 'FCM-REPO');
        return true;
      }

      AppLogger.w('FCM token delete failed: ${response.statusCode}', 'FCM-REPO');
      return false;
    } catch (e, stackTrace) {
      AppLogger.e('Failed to delete FCM token from backend', e, stackTrace, 'FCM-REPO');
      return false;
    }
  }
}
