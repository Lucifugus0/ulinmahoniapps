import 'dart:async';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
// TODO: Uncomment when backend FCM endpoints ready
// import 'package:dio/dio.dart';
import '../utils/app_logger.dart';
import 'local_notification_service.dart';
// TODO: Uncomment when backend FCM endpoints ready
// import '../data/repositories/fcm_repository.dart';
import '../../router/router.dart';

/// Top-level function for background message handler
/// MUST be top-level or static - cannot be inside class
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  try {
    // Initialize Firebase in background isolate
    await Firebase.initializeApp();

    // Initialize LocalNotificationService in background context
    final notificationService = LocalNotificationService();
    await notificationService.initialize(skipPermissionRequest: true);

    AppLogger.i('Background message received: ${message.messageId}', 'FCM-BG');

    // Handle the message
    if (message.data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(message.data['conversation_id'] ?? '');
      final title = message.notification?.title ?? message.data['title'] ?? 'New Message';
      final body = message.notification?.body ?? message.data['body'] ?? '';

      if (conversationId != null) {
        await notificationService.showChatNotification(
          conversationId: conversationId,
          title: title,
          body: body,
        );
      }
    }
  } catch (e, stackTrace) {
    AppLogger.e('Error in background handler', e, stackTrace, 'FCM-BG');
    // Don't crash - log error and continue
  }
}

/// Firebase Cloud Messaging Service
class FCMService {
  static final FCMService _instance = FCMService._internal();
  factory FCMService() => _instance;
  FCMService._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  // TODO: Uncomment when backend FCM endpoints ready
  // late final FCMRepository _fcmRepository;
  StreamSubscription<String>? _tokenRefreshSubscription;
  StreamSubscription<RemoteMessage>? _foregroundMessageSubscription;

  /// Initialize FCM service
  Future<void> initialize() async {
    try {
      AppLogger.i('Initializing FCM Service', 'FCM');

      // TODO: Uncomment when backend FCM endpoints ready
      // Initialize repository
      // final dio = Dio();
      // _fcmRepository = FCMRepository(dio);

      // Request notification permissions (iOS)
      final settings = await _firebaseMessaging.requestPermission(
        alert: true,
        badge: true,
        sound: true,
        provisional: false,
      );

      if (settings.authorizationStatus == AuthorizationStatus.authorized) {
        AppLogger.s('FCM permission granted', 'FCM');
      } else if (settings.authorizationStatus == AuthorizationStatus.provisional) {
        AppLogger.i('FCM provisional permission granted', 'FCM');
      } else {
        AppLogger.w('FCM permission denied', 'FCM');
        return;
      }

      // Get initial FCM token
      final token = await getToken();
      if (token != null) {
        AppLogger.s('FCM Token obtained: ${token.substring(0, 20)}...', 'FCM');
        await _saveTokenLocally(token);

        // TODO: Uncomment when backend FCM endpoints ready
        // Send token to backend
        // await _sendTokenToBackend(token);
      }

      // Listen for token refresh
      _tokenRefreshSubscription = _firebaseMessaging.onTokenRefresh.listen(
        (newToken) async {
          AppLogger.i('FCM Token refreshed', 'FCM');
          await _saveTokenLocally(newToken);

          // TODO: Uncomment when backend FCM endpoints ready
          // Send new token to backend
          // await _sendTokenToBackend(newToken);
        },
        onError: (error) {
          AppLogger.e('Token refresh error', error, null, 'FCM');
        },
      );

      // Handle foreground messages
      _foregroundMessageSubscription = FirebaseMessaging.onMessage.listen(
        _handleForegroundMessage,
        onError: (error) {
          AppLogger.e('Foreground message error', error, null, 'FCM');
        },
      );

      // Handle message opened app (from terminated state)
      FirebaseMessaging.instance.getInitialMessage().then((message) {
        if (message != null) {
          _handleMessageOpenedApp(message);
        }
      });

      // Handle message opened app (from background state)
      FirebaseMessaging.onMessageOpenedApp.listen(_handleMessageOpenedApp);

      AppLogger.s('FCM Service initialized successfully', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('FCM initialization failed', e, stackTrace, 'FCM');
    }
  }

  /// Get FCM token
  Future<String?> getToken() async {
    try {
      final token = await _firebaseMessaging.getToken();
      if (token != null) {
        AppLogger.d('FCM Token: ${token.substring(0, 30)}...', 'FCM');
      }
      return token;
    } catch (e, stackTrace) {
      AppLogger.e('Failed to get FCM token', e, stackTrace, 'FCM');
      return null;
    }
  }

  /// Get locally saved token
  Future<String?> getSavedToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString('fcm_token');
    } catch (e) {
      AppLogger.e('Failed to get saved token', e, null, 'FCM');
      return null;
    }
  }

  /// Save token locally
  Future<void> _saveTokenLocally(String token) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString('fcm_token', token);
      await prefs.setInt('fcm_token_timestamp', DateTime.now().millisecondsSinceEpoch);
      AppLogger.d('FCM token saved locally', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to save token locally', e, stackTrace, 'FCM');
    }
  }

  // TODO: Uncomment when backend FCM endpoints ready
  /// Send token to backend
  // Future<void> _sendTokenToBackend(String token) async {
  //   try {
  //     // Get user_id from SharedPreferences
  //     final prefs = await SharedPreferences.getInstance();
  //     final userId = prefs.getInt('user_id');
  //
  //     if (userId == null) {
  //       AppLogger.w('User ID not found, cannot send FCM token to backend', 'FCM');
  //       return;
  //     }
  //
  //     // Send to backend
  //     final success = await _fcmRepository.sendFCMToken(
  //       userId: userId,
  //       fcmToken: token,
  //     );
  //
  //     if (success) {
  //       AppLogger.s('FCM token registered with backend for user $userId', 'FCM');
  //     } else {
  //       AppLogger.w('Failed to register FCM token with backend', 'FCM');
  //     }
  //   } catch (e, stackTrace) {
  //     AppLogger.e('Error sending FCM token to backend', e, stackTrace, 'FCM');
  //   }
  // }

  /// Handle foreground messages (app is open)
  void _handleForegroundMessage(RemoteMessage message) {
    AppLogger.i('Foreground message: ${message.messageId}', 'FCM');

    final notification = message.notification;
    final data = message.data;

    // Show notification even when app is in foreground
    if (data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(data['conversation_id'] ?? '');
      final title = notification?.title ?? data['title'] ?? 'New Message';
      final body = notification?.body ?? data['body'] ?? '';

      if (conversationId != null) {
        LocalNotificationService().showChatNotification(
          conversationId: conversationId,
          title: title,
          body: body,
        );
      }
    }
  }

  /// Handle message that opened the app
  void _handleMessageOpenedApp(RemoteMessage message) {
    AppLogger.i('Message opened app: ${message.messageId}', 'FCM');

    final data = message.data;

    // Navigate to chat room if conversation_id is present
    if (data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(data['conversation_id'] ?? '');

      if (conversationId != null) {
        AppLogger.d('Navigate to conversation: $conversationId', 'FCM');

        // Navigate using GoRouter
        appRouter.go('/cs/chat/$conversationId');
      }
    }
  }

  /// Delete FCM token (on logout)
  Future<void> deleteToken() async {
    try {
      // TODO: Uncomment when backend FCM endpoints ready
      // Get user_id before deleting
      // final prefs = await SharedPreferences.getInstance();
      // final userId = prefs.getInt('user_id');

      final prefs = await SharedPreferences.getInstance();

      // Delete from Firebase
      await _firebaseMessaging.deleteToken();

      // Delete local storage
      await prefs.remove('fcm_token');
      await prefs.remove('fcm_token_timestamp');

      // TODO: Uncomment when backend FCM endpoints ready
      // Delete from backend
      // if (userId != null) {
      //   await _fcmRepository.deleteFCMToken(userId: userId);
      // }

      AppLogger.s('FCM token deleted', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to delete FCM token', e, stackTrace, 'FCM');
    }
  }

  /// Subscribe to topic (optional - for broadcast messages)
  Future<void> subscribeToTopic(String topic) async {
    try {
      await _firebaseMessaging.subscribeToTopic(topic);
      AppLogger.s('Subscribed to topic: $topic', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to subscribe to topic', e, stackTrace, 'FCM');
    }
  }

  /// Unsubscribe from topic
  Future<void> unsubscribeFromTopic(String topic) async {
    try {
      await _firebaseMessaging.unsubscribeFromTopic(topic);
      AppLogger.s('Unsubscribed from topic: $topic', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to unsubscribe from topic', e, stackTrace, 'FCM');
    }
  }

  /// Dispose subscriptions
  void dispose() {
    _tokenRefreshSubscription?.cancel();
    _foregroundMessageSubscription?.cancel();
  }
}
