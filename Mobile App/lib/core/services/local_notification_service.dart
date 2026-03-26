import 'package:flutter_local_notifications/flutter_local_notifications.dart';
import 'package:permission_handler/permission_handler.dart';
import '../utils/app_logger.dart';

/// Service for managing local notifications
class LocalNotificationService {
  static final LocalNotificationService _instance = LocalNotificationService._internal();
  factory LocalNotificationService() => _instance;
  LocalNotificationService._internal();

  final FlutterLocalNotificationsPlugin _notificationsPlugin = FlutterLocalNotificationsPlugin();
  bool _initialized = false;

  /// Initialize the notification service
  ///
  /// Set [skipPermissionRequest] to true when initializing from background isolate
  Future<void> initialize({bool skipPermissionRequest = false}) async {
    if (_initialized) return;

    try {
      // Android initialization settings
      const androidSettings = AndroidInitializationSettings('@mipmap/ic_launcher');

      // iOS initialization settings
      const iosSettings = DarwinInitializationSettings(
        requestAlertPermission: true,
        requestBadgePermission: true,
        requestSoundPermission: true,
      );

      const initSettings = InitializationSettings(
        android: androidSettings,
        iOS: iosSettings,
      );

      // Initialize plugin
      await _notificationsPlugin.initialize(
        initSettings,
        onDidReceiveNotificationResponse: _onNotificationTapped,
      );

      // Request permissions (skip in background isolate)
      if (!skipPermissionRequest) {
        await _requestPermissions();
      }

      _initialized = true;
      AppLogger.s('LocalNotificationService initialized', 'NOTIFICATION-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to initialize LocalNotificationService', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Request notification permissions
  Future<void> _requestPermissions() async {
    try {
      // Request notification permission using permission_handler
      final status = await Permission.notification.request();

      if (status.isGranted) {
        AppLogger.d('Notification permission granted', 'NOTIFICATION-SERVICE');
      } else if (status.isDenied) {
        AppLogger.w('Notification permission denied', 'NOTIFICATION-SERVICE');
      } else if (status.isPermanentlyDenied) {
        AppLogger.w('Notification permission permanently denied', 'NOTIFICATION-SERVICE');
      }

      // iOS-specific permission request
      await _notificationsPlugin
          .resolvePlatformSpecificImplementation<IOSFlutterLocalNotificationsPlugin>()
          ?.requestPermissions(
            alert: true,
            badge: true,
            sound: true,
          );
    } catch (e, stackTrace) {
      AppLogger.e('Error requesting notification permissions', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Handle notification tap
  void _onNotificationTapped(NotificationResponse response) {
    final payload = response.payload;
    if (payload != null) {
      AppLogger.d('Notification tapped with payload: $payload', 'NOTIFICATION-SERVICE');
      // Deep linking will be handled by the app router
      // Payload format: "chat:{conversationId}"
    }
  }

  /// Show a chat notification
  Future<void> showChatNotification({
    required int conversationId,
    required String title,
    required String body,
  }) async {
    if (!_initialized) {
      AppLogger.w('LocalNotificationService not initialized', 'NOTIFICATION-SERVICE');
      return;
    }

    try {
      const androidDetails = AndroidNotificationDetails(
        'chat_channel',
        'Chat Messages',
        channelDescription: 'Notifications for new chat messages',
        importance: Importance.high,
        priority: Priority.high,
        showWhen: true,
        icon: '@mipmap/ic_launcher',
      );

      const iosDetails = DarwinNotificationDetails(
        presentAlert: true,
        presentBadge: true,
        presentSound: true,
      );

      const notificationDetails = NotificationDetails(
        android: androidDetails,
        iOS: iosDetails,
      );

      // Use conversationId as notification ID to replace old notification with new one
      await _notificationsPlugin.show(
        conversationId,
        title,
        body,
        notificationDetails,
        payload: 'chat:$conversationId',
      );

      AppLogger.d('Chat notification shown for conversation $conversationId', 'NOTIFICATION-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to show chat notification', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Show a booking-related push notification (foreground display).
  ///
  /// Used for booking_created, check_in, booking_renewed,
  /// payment_received, and booking_expired notification types.
  ///
  /// [type]    - notification type string (e.g. "booking_created")
  /// [title]   - notification title from FCM payload
  /// [body]    - notification body text from FCM payload
  /// [orderId] - booking order ID for identification (used as notification ID)
  Future<void> showBookingNotification({
    required String type,
    required String title,
    required String body,
    String? orderId,
  }) async {
    if (!_initialized) {
      AppLogger.w('LocalNotificationService not initialized', 'NOTIFICATION-SERVICE');
      return;
    }

    try {
      const androidDetails = AndroidNotificationDetails(
        'booking_channel',
        'Booking Updates',
        channelDescription: 'Notifications for booking status updates, payments, and check-in',
        importance: Importance.high,
        priority: Priority.high,
        showWhen: true,
        icon: '@mipmap/ic_launcher',
      );

      const iosDetails = DarwinNotificationDetails(
        presentAlert: true,
        presentBadge: true,
        presentSound: true,
      );

      const notificationDetails = NotificationDetails(
        android: androidDetails,
        iOS: iosDetails,
      );

      // Use hashCode of type+orderId as a stable notification ID
      final notifId = (type + (orderId ?? '')).hashCode.abs();

      await _notificationsPlugin.show(
        notifId,
        title,
        body,
        notificationDetails,
        payload: 'booking:$type:${orderId ?? ''}',
      );

      AppLogger.d('Booking notification shown — type: $type, order: $orderId', 'NOTIFICATION-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to show booking notification', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Cancel a specific notification by conversation ID
  Future<void> cancelNotification(int conversationId) async {
    try {
      await _notificationsPlugin.cancel(conversationId);
      AppLogger.d('Cancelled notification for conversation $conversationId', 'NOTIFICATION-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to cancel notification', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Cancel all notifications
  Future<void> cancelAllNotifications() async {
    try {
      await _notificationsPlugin.cancelAll();
      AppLogger.d('Cancelled all notifications', 'NOTIFICATION-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to cancel all notifications', e, stackTrace, 'NOTIFICATION-SERVICE');
    }
  }

  /// Check if notifications are enabled
  Future<bool> areNotificationsEnabled() async {
    try {
      final status = await Permission.notification.status;
      return status.isGranted;
    } catch (e) {
      AppLogger.e('Error checking notification status', e, null, 'NOTIFICATION-SERVICE');
      return false;
    }
  }
}
