import 'dart:async';
import 'dart:io';
import 'package:device_info_plus/device_info_plus.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'package:flutter/foundation.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/app_logger.dart';
import 'local_notification_service.dart';
import '../data/repositories/fcm_repository.dart';
import '../../router/router.dart';

/// Top-level function for background message handler.
/// MUST be top-level or static — cannot be inside a class.
@pragma('vm:entry-point')
Future<void> firebaseMessagingBackgroundHandler(RemoteMessage message) async {
  try {
    // Initialize Firebase in the background isolate
    await Firebase.initializeApp();

    // Initialize LocalNotificationService in background context
    final notificationService = LocalNotificationService();
    await notificationService.initialize(skipPermissionRequest: true);

    AppLogger.i('Background message received: ${message.messageId}', 'FCM-BG');

    final data = message.data;
    final title = message.notification?.title ?? data['title'] ?? 'Ulin Mahoni';
    final body = message.notification?.body ?? data['body'] ?? '';

    // Handle chat notifications
    if (data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(data['conversation_id'] ?? '');
      if (conversationId != null) {
        await notificationService.showChatNotification(
          conversationId: conversationId,
          title: title,
          body: body,
        );
      }
      return;
    }

    // Handle ticket creation notifications (admin-initiated chat)
    final type = data['type'] as String?;
    if (type == 'ticket_created' || type == 'ticket_message') {
      final ticketId = int.tryParse(data['ticket_id'] ?? '');
      if (ticketId != null) {
        await notificationService.showChatNotification(
          conversationId: ticketId,
          title: title,
          body: body,
        );
      }
      return;
    }

    // Handle booking-related notifications
    if (_isBookingType(type)) {
      await notificationService.showBookingNotification(
        type: type!,
        title: title,
        body: body,
        orderId: data['order_id'] as String?,
      );
    }
  } catch (e, stackTrace) {
    AppLogger.e('Error in background handler', e, stackTrace, 'FCM-BG');
    // Never crash in background handler
  }
}

/// Returns true if [type] is one of the known booking notification types.
bool _isBookingType(String? type) {
  const bookingTypes = {
    'booking_created',
    'check_in',
    'booking_renewed',
    'payment_received',
    'booking_expired',
  };
  return type != null && bookingTypes.contains(type);
}

/// Firebase Cloud Messaging Service
///
/// Lifecycle:
///   1. [initialize] — called on app startup/login, requests permission,
///      gets FCM token, registers it with backend, sets up listeners.
///   2. Token refresh — [onTokenRefresh] sends new token to backend automatically.
///   3. Foreground messages — shows local notification via [LocalNotificationService].
///   4. Notification tap — navigates to the correct page via [appRouter].
///   5. [deleteToken] — called on logout, removes token from Firebase and backend.
class FCMService {
  static final FCMService _instance = FCMService._internal();
  factory FCMService() => _instance;
  FCMService._internal();

  final FirebaseMessaging _firebaseMessaging = FirebaseMessaging.instance;
  final FCMRepository _fcmRepository = FCMRepository();
  StreamSubscription<String>? _tokenRefreshSubscription;
  StreamSubscription<RemoteMessage>? _foregroundMessageSubscription;

  /// Initialize FCM service.
  /// Should be called after login or on app startup when user is authenticated.
  Future<void> initialize() async {
    try {
      AppLogger.i('Initializing FCM Service', 'FCM');

      // Request notification permissions (prompts on iOS, no-op on Android 12-)
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

      // Get initial FCM token and register with backend
      final token = await getToken();
      if (token != null) {
        AppLogger.s('FCM Token obtained: ${token.substring(0, 20)}...', 'FCM');
        await _saveTokenLocally(token);
        await _sendTokenToBackend(token);
      }

      // Listen for token refresh — Firebase may rotate the token periodically
      _tokenRefreshSubscription = _firebaseMessaging.onTokenRefresh.listen(
        (newToken) async {
          AppLogger.i('FCM Token refreshed', 'FCM');
          await _saveTokenLocally(newToken);
          await _sendTokenToBackend(newToken);
        },
        onError: (error) {
          AppLogger.e('Token refresh error', error, null, 'FCM');
        },
      );

      // Show local notification when a message arrives while app is in foreground
      _foregroundMessageSubscription = FirebaseMessaging.onMessage.listen(
        _handleForegroundMessage,
        onError: (error) {
          AppLogger.e('Foreground message error', error, null, 'FCM');
        },
      );

      // Navigate when user taps a notification that opened the app from terminated state
      FirebaseMessaging.instance.getInitialMessage().then((message) {
        if (message != null) {
          _handleMessageOpenedApp(message);
        }
      });

      // Navigate when user taps a notification that opened the app from background state
      FirebaseMessaging.onMessageOpenedApp.listen(_handleMessageOpenedApp);

      AppLogger.s('FCM Service initialized successfully', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('FCM initialization failed', e, stackTrace, 'FCM');
    }
  }

  /// Get FCM token from Firebase SDK.
  /// Wrapped in a 10-second timeout to avoid hanging the auth flow if Firebase
  /// is unable to reach Google Play Services or APNs.
  Future<String?> getToken() async {
    try {
      final token = await _firebaseMessaging.getToken().timeout(
        const Duration(seconds: 10),
        onTimeout: () {
          AppLogger.w('FCM getToken timed out after 10s', 'FCM');
          return null;
        },
      );
      if (token != null) {
        AppLogger.d('FCM Token: ${token.substring(0, 30)}...', 'FCM');
      }
      return token;
    } catch (e, stackTrace) {
      AppLogger.e('Failed to get FCM token', e, stackTrace, 'FCM');
      return null;
    }
  }

  /// Get the locally saved FCM token from SharedPreferences.
  Future<String?> getSavedToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString('fcm_token');
    } catch (e) {
      AppLogger.e('Failed to get saved token', e, null, 'FCM');
      return null;
    }
  }

  /// Save token and timestamp to SharedPreferences.
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

  /// Send FCM token to backend.
  ///
  /// Detects device type (android/ios) and device name using [device_info_plus],
  /// then calls [FCMRepository.registerToken].
  /// Failures are logged but never propagated — FCM registration should not
  /// block the main app flow.
  Future<void> _sendTokenToBackend(String token) async {
    try {
      // Only send to backend when user is authenticated (token must be present)
      final prefs = await SharedPreferences.getInstance();
      final authToken = prefs.getString('auth_token');
      if (authToken == null || authToken.isEmpty) {
        AppLogger.w('No auth token — skipping FCM backend registration', 'FCM');
        return;
      }

      // Determine platform and device name
      final deviceType = Platform.isAndroid ? 'android' : 'ios';
      String? deviceName;

      try {
        final deviceInfo = DeviceInfoPlugin();
        if (Platform.isAndroid) {
          final androidInfo = await deviceInfo.androidInfo;
          // e.g. "Samsung Galaxy S24" or "Pixel 8"
          deviceName = androidInfo.model;
        } else if (Platform.isIOS) {
          final iosInfo = await deviceInfo.iosInfo;
          // e.g. "iPhone 15 Pro" or "iPad Air (5th generation)"
          deviceName = iosInfo.name;
        }
      } catch (e) {
        AppLogger.w('Could not read device info: $e', 'FCM');
        // device_name is optional — proceed without it
      }

      final success = await _fcmRepository.registerToken(
        token: token,
        deviceType: deviceType,
        deviceName: deviceName,
      );

      if (success) {
        AppLogger.s('FCM token registered with backend ($deviceType / $deviceName)', 'FCM');
      } else {
        AppLogger.w('Failed to register FCM token with backend', 'FCM');
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error sending FCM token to backend', e, stackTrace, 'FCM');
    }
  }

  /// Handle foreground messages (app is open and visible).
  ///
  /// FCM does not show a system notification when the app is in foreground,
  /// so we display one manually via [LocalNotificationService].
  void _handleForegroundMessage(RemoteMessage message) {
    AppLogger.i('Foreground message: ${message.messageId}', 'FCM');

    final data = message.data;
    final notification = message.notification;
    final title = notification?.title ?? data['title'] ?? 'Ulin Mahoni';
    final body = notification?.body ?? data['body'] ?? '';

    // Chat notification
    if (data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(data['conversation_id'] ?? '');
      if (conversationId != null) {
        LocalNotificationService().showChatNotification(
          conversationId: conversationId,
          title: title,
          body: body,
        );
      }
      return;
    }

    // Booking notification types
    final type = data['type'] as String?;
    if (_isBookingType(type)) {
      LocalNotificationService().showBookingNotification(
        type: type!,
        title: title,
        body: body,
        orderId: data['order_id'] as String?,
      );
    }
  }

  /// Handle notification tap that opened the app (from background or terminated).
  ///
  /// Navigation:
  ///   - Chat → /cs/chat/{conversationId}
  ///   - Booking types → /mybooking (full detail requires object; list is the
  ///     practical landing page from a cold notification tap)
  void _handleMessageOpenedApp(RemoteMessage message) {
    AppLogger.i('Message opened app: ${message.messageId}', 'FCM');

    final data = message.data;

    // Navigate to chat room
    if (data.containsKey('conversation_id')) {
      final conversationId = int.tryParse(data['conversation_id'] ?? '');
      if (conversationId != null) {
        AppLogger.d('Navigate to conversation: $conversationId', 'FCM');
        appRouter.go('/cs/chat/$conversationId');
      }
      return;
    }

    // Navigate to My Booking for all booking-related notification types
    final type = data['type'] as String?;
    if (_isBookingType(type)) {
      AppLogger.d('Navigate to My Booking — type: $type, order: ${data['order_id']}', 'FCM');
      appRouter.go('/mybooking');
    }
  }

  /// Public method to sync FCM token to backend after login.
  ///
  /// Called by [AuthNotifier] after successful login so the token registered
  /// during [initialize] (before login) is sent to the backend now that an
  /// auth token is available. Also re-requests notification permission if it
  /// was previously denied (e.g. Android 13+ first-launch denial).
  Future<void> syncTokenToBackend() async {
    final token = await getSavedToken();
    if (token != null) {
      await _sendTokenToBackend(token);
      return;
    }

    /// No saved token — try to fetch a fresh one. On Android FCM tokens are
    /// available even without notification permission, so we don't gate this
    /// on the permission status.
    final freshToken = await getToken();
    if (freshToken != null) {
      await _saveTokenLocally(freshToken);
      await _sendTokenToBackend(freshToken);
    } else {
      AppLogger.w('No FCM token available to sync', 'FCM');
    }
  }

  /// Delete FCM token on logout.
  ///
  /// Removes the token from Firebase SDK, local storage, and backend.
  /// After this call the device will no longer receive push notifications
  /// until [initialize] is called again after the next login.
  Future<void> deleteToken() async {
    try {
      // Read saved token before deleting from Firebase
      final savedToken = await getSavedToken();

      // Remove from Firebase SDK
      await _firebaseMessaging.deleteToken();

      // Remove from local storage
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove('fcm_token');
      await prefs.remove('fcm_token_timestamp');

      // Remove from backend so no more notifications reach this device
      if (savedToken != null && savedToken.isNotEmpty) {
        await _fcmRepository.deleteToken(token: savedToken);
      } else {
        AppLogger.w('No saved FCM token to delete from backend', 'FCM');
      }

      AppLogger.s('FCM token deleted', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to delete FCM token', e, stackTrace, 'FCM');
    }
  }

  /// Subscribe to a Firebase topic (optional — for broadcast messages).
  Future<void> subscribeToTopic(String topic) async {
    try {
      await _firebaseMessaging.subscribeToTopic(topic);
      AppLogger.s('Subscribed to topic: $topic', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to subscribe to topic', e, stackTrace, 'FCM');
    }
  }

  /// Unsubscribe from a Firebase topic.
  Future<void> unsubscribeFromTopic(String topic) async {
    try {
      await _firebaseMessaging.unsubscribeFromTopic(topic);
      AppLogger.s('Unsubscribed from topic: $topic', 'FCM');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to unsubscribe from topic', e, stackTrace, 'FCM');
    }
  }

  /// Dispose stream subscriptions when service is torn down.
  void dispose() {
    _tokenRefreshSubscription?.cancel();
    _foregroundMessageSubscription?.cancel();
  }
}
