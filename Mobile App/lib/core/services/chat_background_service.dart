import 'dart:convert';
import 'package:workmanager/workmanager.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import '../network/dio_client.dart';
import '../constants/api_constants.dart';
import '../utils/app_logger.dart';
import 'local_notification_service.dart';

/// Background service for polling new chat messages
class ChatBackgroundService {
  static const String _taskName = 'chatPollingTask';
  static const String _lastSeenKey = 'chat_last_seen_messages';
  static const String _isEnabledKey = 'chat_background_polling_enabled';

  static final ChatBackgroundService _instance = ChatBackgroundService._internal();
  factory ChatBackgroundService() => _instance;
  ChatBackgroundService._internal();

  /// Initialize the background service
  Future<void> initialize() async {
    // TEMPORARY DISABLED: BGTask causing SIGABRT crash
    // TODO: Re-enable after fixing Bundle ID mismatch in Info.plist
    // See: feature_guide/BGTASK_TEMPORARY_DISABLE.md

    AppLogger.w(
      '⚠️ ChatBackgroundService temporarily disabled due to BGTask crash issue. '
      'Background chat sync will not work until re-enabled.',
      'CHAT-BG-SERVICE'
    );

    return; // Early return - skip all Workmanager initialization

    /* COMMENTED OUT - Original implementation
    try {
      await Workmanager().initialize(
        callbackDispatcher,
      );
      AppLogger.s('ChatBackgroundService initialized', 'CHAT-BG-SERVICE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to initialize ChatBackgroundService', e, stackTrace, 'CHAT-BG-SERVICE');
    }
    */
  }

  /// Start background polling — DISABLED: using FCM push notifications instead
  Future<void> startPolling() async {
    AppLogger.w('ChatBackgroundService.startPolling() disabled — using FCM push instead', 'CHAT-BG-SERVICE');
    return;

  }

  /// Stop background polling — DISABLED: using FCM push notifications instead
  Future<void> stopPolling() async {
    return;
  }

  /// Check if polling is enabled
  Future<bool> isPollingEnabled() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getBool(_isEnabledKey) ?? false;
    } catch (e) {
      return false;
    }
  }

  /// Save last seen message IDs for all conversations
  static Future<void> saveLastSeenMessages(Map<int, int> lastSeenMap) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final jsonString = jsonEncode(lastSeenMap.map((key, value) => MapEntry(key.toString(), value)));
      await prefs.setString(_lastSeenKey, jsonString);
    } catch (e, stackTrace) {
      AppLogger.e('Failed to save last seen messages', e, stackTrace, 'CHAT-BG-SERVICE');
    }
  }

  /// Get last seen message IDs for all conversations
  static Future<Map<int, int>> getLastSeenMessages() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final jsonString = prefs.getString(_lastSeenKey);
      if (jsonString == null) return {};

      final Map<String, dynamic> decoded = jsonDecode(jsonString);
      return decoded.map((key, value) => MapEntry(int.parse(key), value as int));
    } catch (e, stackTrace) {
      AppLogger.e('Failed to get last seen messages', e, stackTrace, 'CHAT-BG-SERVICE');
      return {};
    }
  }

  /// Update last seen message for a conversation
  static Future<void> updateLastSeenMessage(int conversationId, int messageId) async {
    try {
      final lastSeenMap = await getLastSeenMessages();
      lastSeenMap[conversationId] = messageId;
      await saveLastSeenMessages(lastSeenMap);
    } catch (e, stackTrace) {
      AppLogger.e('Failed to update last seen message', e, stackTrace, 'CHAT-BG-SERVICE');
    }
  }
}

/// Background task callback dispatcher
/// This function runs in a separate isolate
@pragma('vm:entry-point')
void callbackDispatcher() {
  Workmanager().executeTask((task, inputData) async {
    try {
      // CRITICAL: Load .env in background isolate
      await dotenv.load(fileName: ".env");

      AppLogger.d('Background task started: $task', 'CHAT-BG-WORKER');

      // Check if polling is enabled
      final prefs = await SharedPreferences.getInstance();
      final isEnabled = prefs.getBool('chat_background_polling_enabled') ?? false;
      if (!isEnabled) {
        AppLogger.d('Polling disabled, skipping', 'CHAT-BG-WORKER');
        return Future.value(true);
      }

      // Get user ID from shared preferences
      final userId = prefs.getInt('user_id');
      if (userId == null) {
        AppLogger.w('No user ID found, skipping polling', 'CHAT-BG-WORKER');
        return Future.value(true);
      }

      AppLogger.d('Fetching conversations for user $userId', 'CHAT-BG-WORKER');

      // Fetch conversations from API
      final dioClient = DioClient();
      final response = await dioClient.get(
        ApiConfig.chatConversations,
        queryParameters: {'user_id': userId},
      );

      AppLogger.d('API response status: ${response.statusCode}', 'CHAT-BG-WORKER');

      if (response.statusCode == 200) {
        final body = response.data;
        if (body is Map && body['status'] == 'success' && body['data'] is List) {
          final conversations = body['data'] as List;
          AppLogger.d('Found ${conversations.length} conversations', 'CHAT-BG-WORKER');

          // Get last seen messages
          final lastSeenMap = await ChatBackgroundService.getLastSeenMessages();
          AppLogger.d('Last seen map: $lastSeenMap', 'CHAT-BG-WORKER');

          final notificationService = LocalNotificationService();
          await notificationService.initialize(skipPermissionRequest: true);

          // Check each conversation for new messages
          AppLogger.d('Starting to process ${conversations.length} conversations', 'CHAT-BG-WORKER');

          for (int i = 0; i < conversations.length; i++) {
            try {
              final conv = conversations[i];
              AppLogger.d('Processing conversation index $i: ${conv.toString()}', 'CHAT-BG-WORKER');

              final conversationId = conv['id'] as int;

              // Get latest message from messages array (not latest_message field)
              final messages = conv['messages'] as List?;
              final latestMessage = (messages != null && messages.isNotEmpty)
                  ? messages.last
                  : null;

              AppLogger.d('Conv $conversationId, messages count: ${messages?.length ?? 0}, has latestMessage: ${latestMessage != null}', 'CHAT-BG-WORKER');

            if (latestMessage != null) {
              final messageId = latestMessage['id'] as int;
              final lastSeenId = lastSeenMap[conversationId] ?? 0;

              AppLogger.d('Conv $conversationId: messageId=$messageId, lastSeenId=$lastSeenId', 'CHAT-BG-WORKER');

              // Check if this is a new message from staff (not from customer)
              if (messageId > lastSeenId) {
                final senderId = latestMessage['sender_id'] as int;
                final sender = latestMessage['sender'];

                AppLogger.d('New message in conv $conversationId, senderId: $senderId, currentUserId: $userId', 'CHAT-BG-WORKER');

                // Only notify for messages NOT from current user (i.e., from HO or FO)
                if (senderId != userId) {
                  final senderName = sender?['name'] as String? ??
                                     '${sender?['first_name'] ?? ''} ${sender?['last_name'] ?? ''}'.trim();
                  final messageText = latestMessage['message_text'] as String? ?? '';
                  final bookingCode = conv['order_id'] as String? ?? '';

                  // Show notification
                  await notificationService.showChatNotification(
                    conversationId: conversationId,
                    title: '$senderName - $bookingCode',
                    body: messageText.isNotEmpty ? messageText : 'Mengirim gambar',
                  );

                  AppLogger.d('Notification sent for conversation $conversationId', 'CHAT-BG-WORKER');
                } else {
                  AppLogger.d('Skipped notification - message from current user', 'CHAT-BG-WORKER');
                }

                // Update last seen message
                lastSeenMap[conversationId] = messageId;
              }
            }
            } catch (e, stackTrace) {
              AppLogger.e('Error processing conversation index $i', e, stackTrace, 'CHAT-BG-WORKER');
            }
          }

          // Save updated last seen messages
          await ChatBackgroundService.saveLastSeenMessages(lastSeenMap);
        }
      }

      AppLogger.s('Background task completed successfully', 'CHAT-BG-WORKER');
      return Future.value(true);
    } catch (e, stackTrace) {
      AppLogger.e('Background task failed', e, stackTrace, 'CHAT-BG-WORKER');
      return Future.value(false);
    }
  });
}
