import 'dart:io';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/chat_repository.dart';
import '../../../core/utils/app_logger.dart';
import '../../../core/network/api_result.dart';
import '../provider/chat_provider.dart';
import '../../../core/services/chat_background_service.dart';

/// Chat state — tracks loading, sending, uploading, error, and success states.
class ChatState {
  final bool isLoading;
  final bool isSendingMessage;
  final bool isUploadingImage;
  final String? error;
  final String? successMessage;

  ChatState({
    this.isLoading = false,
    this.isSendingMessage = false,
    this.isUploadingImage = false,
    this.error,
    this.successMessage,
  });

  ChatState copyWith({
    bool? isLoading,
    bool? isSendingMessage,
    bool? isUploadingImage,
    String? error,
    String? successMessage,
  }) {
    return ChatState(
      isLoading: isLoading ?? this.isLoading,
      isSendingMessage: isSendingMessage ?? this.isSendingMessage,
      isUploadingImage: isUploadingImage ?? this.isUploadingImage,
      error: error,
      successMessage: successMessage,
    );
  }
}

/// Chat controller — Riverpod 3.x Notifier managing chat actions.
/// Migrated from StateNotifier to Notifier (no constructor args, uses build()).
class ChatController extends Notifier<ChatState> {
  late final ChatRepository _repository;

  /// build() returns the initial state and sets up dependencies.
  @override
  ChatState build() {
    _repository = ref.watch(chatRepositoryProvider);
    return ChatState();
  }

  /// Create a new conversation.
  /// Returns conversation ID if successful, or existing conversation ID if duplicate.
  Future<int?> createConversation({
    required int userId,
    required String orderId,
    required String title,
    required String initialMessage,
  }) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final result = await _repository.createConversation(
        userId: userId,
        orderId: orderId,
        title: title,
        initialMessage: initialMessage,
      );

      switch (result) {
        case Success(:final data):
          // Handle both 'id' (201) and 'conversation_id' (409) response formats
          final conversationId = (data['id'] ?? data['conversation_id']) as int;
          AppLogger.s('Conversation created: $conversationId', 'CHAT-CTRL');

          state = state.copyWith(
            isLoading: false,
            successMessage: 'Conversation created successfully',
          );

          // Refresh conversation list
          ref.invalidate(conversationListProvider);

          return conversationId;

        case Failure(:final message, :final statusCode):
          // Handle invalid booking (403)
          if (statusCode == 403) {
            AppLogger.e('Invalid booking', null, null, 'CHAT-CTRL');
            state = state.copyWith(
              isLoading: false,
              error: 'You do not have a valid active booking for this order',
            );
            return null;
          }

          AppLogger.e('Failed to create conversation', message, null, 'CHAT-CTRL');
          state = state.copyWith(
            isLoading: false,
            error: message,
          );
          return null;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in createConversation', e, stackTrace, 'CHAT-CTRL');
      state = state.copyWith(
        isLoading: false,
        error: 'An unexpected error occurred',
      );
      return null;
    }
  }

  /// Send text message to a conversation.
  Future<bool> sendTextMessage({
    required int conversationId,
    required int userId,
    required String messageText,
  }) async {
    state = state.copyWith(isSendingMessage: true, error: null);

    try {
      final result = await _repository.sendTextMessage(
        conversationId: conversationId,
        userId: userId,
        messageText: messageText,
      );

      switch (result) {
        case Success(:final data):
          AppLogger.s('Message sent: ${data['id']}', 'CHAT-CTRL');

          state = state.copyWith(isSendingMessage: false);

          // Refresh conversation detail
          _refreshConversationDetail(conversationId, userId);

          return true;

        case Failure(:final message):
          AppLogger.e('Failed to send message', message, null, 'CHAT-CTRL');
          state = state.copyWith(
            isSendingMessage: false,
            error: message,
          );
          return false;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in sendTextMessage', e, stackTrace, 'CHAT-CTRL');
      state = state.copyWith(
        isSendingMessage: false,
        error: 'Failed to send message',
      );
      return false;
    }
  }

  /// Send image message to a conversation.
  Future<bool> sendImageMessage({
    required int conversationId,
    required int userId,
    required File image,
    String? messageText,
  }) async {
    state = state.copyWith(isUploadingImage: true, error: null);

    try {
      final result = await _repository.sendImageMessage(
        conversationId: conversationId,
        userId: userId,
        image: image,
        messageText: messageText,
      );

      switch (result) {
        case Success(:final data):
          AppLogger.s('Image message sent: ${data['id']}', 'CHAT-CTRL');

          state = state.copyWith(isUploadingImage: false);

          // Refresh conversation detail
          _refreshConversationDetail(conversationId, userId);

          return true;

        case Failure(:final message):
          AppLogger.e('Failed to send image', message, null, 'CHAT-CTRL');
          state = state.copyWith(
            isUploadingImage: false,
            error: message,
          );
          return false;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in sendImageMessage', e, stackTrace, 'CHAT-CTRL');
      state = state.copyWith(
        isUploadingImage: false,
        error: 'Failed to send image',
      );
      return false;
    }
  }

  /// Mark messages as read in a conversation.
  Future<void> markAsRead({
    required int conversationId,
    required int userId,
  }) async {
    try {
      final result = await _repository.markAsRead(
        conversationId: conversationId,
        userId: userId,
      );

      switch (result) {
        case Success():
          AppLogger.s('Marked as read: $conversationId', 'CHAT-CTRL');

          // Refresh conversation list to update unread count
          ref.invalidate(conversationListProvider);

        case Failure(:final message):
          AppLogger.e('Failed to mark as read', message, null, 'CHAT-CTRL');
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in markAsRead', e, stackTrace, 'CHAT-CTRL');
    }
  }

  /// Update last seen message for background notification tracking.
  Future<void> updateLastSeenMessage({
    required int conversationId,
    required int messageId,
  }) async {
    try {
      await ChatBackgroundService.updateLastSeenMessage(conversationId, messageId);
      AppLogger.d('Updated last seen message for conversation $conversationId: $messageId', 'CHAT-CTRL');
    } catch (e, stackTrace) {
      AppLogger.e('Error updating last seen message', e, stackTrace, 'CHAT-CTRL');
    }
  }

  /// Edit an existing message.
  Future<bool> editMessage({
    required int messageId,
    required int userId,
    required String messageText,
  }) async {
    state = state.copyWith(isLoading: true, error: null);

    try {
      final result = await _repository.editMessage(
        messageId: messageId,
        userId: userId,
        messageText: messageText,
      );

      switch (result) {
        case Success():
          AppLogger.s('Message edited: $messageId', 'CHAT-CTRL');

          state = state.copyWith(
            isLoading: false,
            successMessage: 'Message updated',
          );

          // Refresh conversation detail
          final conversationId = ref.read(selectedConversationProvider);
          if (conversationId != null) {
            _refreshConversationDetail(conversationId, userId);
          }

          return true;

        case Failure(:final message):
          AppLogger.e('Failed to edit message', message, null, 'CHAT-CTRL');
          state = state.copyWith(
            isLoading: false,
            error: message,
          );
          return false;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in editMessage', e, stackTrace, 'CHAT-CTRL');
      state = state.copyWith(
        isLoading: false,
        error: 'Failed to edit message',
      );
      return false;
    }
  }

  /// Refresh conversation list for a user.
  Future<void> refreshConversationList(int userId) async {
    ref.invalidate(conversationListProvider(userId));
  }

  /// Refresh conversation detail (messages) for a given conversation.
  void _refreshConversationDetail(int conversationId, int userId) {
    final currentPage = ref.read(currentPageProvider(conversationId));
    final params = ConversationDetailParams(
      conversationId: conversationId,
      userId: userId,
      page: currentPage,
    );
    ref.invalidate(conversationDetailProvider(params));
  }

  /// Clear error state.
  void clearError() {
    state = state.copyWith(error: null);
  }

  /// Clear success message state.
  void clearSuccessMessage() {
    state = state.copyWith(successMessage: null);
  }
}
