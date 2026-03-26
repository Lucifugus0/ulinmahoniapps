import 'dart:io';
import '../../constants/api_constants.dart';
import '../../network/dio_client.dart';
import '../../utils/app_logger.dart';

/// Repository for syncing FCM device tokens with the backend.
/// Uses DioClient singleton which auto-attaches Bearer token via AuthInterceptor.
/// Backend identifies user from the auth token — no user_id in request body.
class FCMRepository {
  final _dio = DioClient().dio;

  /// Register or update FCM device token on backend.
  /// POST /api/v1/device-token
  /// Body: { "token": "...", "device_type": "ios|android", "device_name": "..." }
  Future<bool> sendFCMToken({
    required String fcmToken,
    String? deviceName,
  }) async {
    try {
      final response = await _dio.post(
        ApiConfig.fcmToken,
        data: {
          'token': fcmToken,
          'device_type': Platform.isIOS ? 'ios' : 'android',
          'device_name': deviceName ?? '${Platform.isIOS ? 'iOS' : 'Android'} Device',
        },
      );

      if (response.statusCode == 200 || response.statusCode == 201) {
        AppLogger.s('FCM token sent to backend successfully', 'FCM-REPO');
        return true;
      } else {
        AppLogger.w('FCM token send failed: ${response.statusCode}', 'FCM-REPO');
        return false;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to send FCM token to backend', e, stackTrace, 'FCM-REPO');
      return false;
    }
  }

  /// Delete FCM device token from backend (called on logout).
  /// DELETE /api/v1/device-token
  /// Body: { "token": "..." }
  Future<bool> deleteFCMToken({
    required String fcmToken,
  }) async {
    try {
      final response = await _dio.delete(
        ApiConfig.fcmTokenDelete,
        data: {
          'token': fcmToken,
        },
      );

      if (response.statusCode == 200) {
        AppLogger.s('FCM token deleted from backend', 'FCM-REPO');
        return true;
      } else {
        AppLogger.w('FCM token delete failed: ${response.statusCode}', 'FCM-REPO');
        return false;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to delete FCM token from backend', e, stackTrace, 'FCM-REPO');
      return false;
    }
  }
}
