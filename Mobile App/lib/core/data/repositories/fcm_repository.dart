import 'package:dio/dio.dart';
import '../../constants/api_constants.dart';
import '../../utils/app_logger.dart';

class FCMRepository {
  final Dio _dio;

  FCMRepository(this._dio);

  /// Send FCM token to backend
  /// POST /api/users/fcm-token
  /// Body: { "user_id": 123, "fcm_token": "xyz..." }
  Future<bool> sendFCMToken({
    required int userId,
    required String fcmToken,
  }) async {
    try {
      final response = await _dio.post(
        '${ApiConfig.baseUrl}/users/fcm-token',
        data: {
          'user_id': userId,
          'fcm_token': fcmToken,
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

  /// Delete FCM token from backend (on logout)
  /// DELETE /api/users/fcm-token
  /// Body: { "user_id": 123 }
  Future<bool> deleteFCMToken({
    required int userId,
  }) async {
    try {
      final response = await _dio.delete(
        '${ApiConfig.baseUrl}/users/fcm-token',
        data: {
          'user_id': userId,
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
