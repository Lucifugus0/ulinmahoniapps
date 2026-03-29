import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/chat_repository.dart';
import '../model/conversation_model.dart';
import '../model/conversation_detail_model.dart';
import '../controller/chat_controller.dart';
import '../../../core/network/api_result.dart';

/// Chat repository provider — singleton instance of ChatRepository.
final chatRepositoryProvider = Provider<ChatRepository>((ref) {
  return ChatRepository();
});

/// Chat controller provider — Riverpod 3.x NotifierProvider.
/// Migrated from StateNotifierProvider to NotifierProvider.
final chatControllerProvider =
    NotifierProvider<ChatController, ChatState>(ChatController.new);

/// Conversation list provider.
/// Fetches all conversations for current user.
final conversationListProvider =
    FutureProvider.family<List<ConversationModel>, int>((ref, userId) async {
  final repository = ref.watch(chatRepositoryProvider);
  final result = await repository.getConversationList(userId);

  switch (result) {
    case Success(:final data):
      // Convert Map to ConversationModel
      return data.map((json) => ConversationModel.fromJson(json)).toList();
    case Failure(:final message):
      throw Exception(message);
  }
});

/// Conversation detail provider.
/// Fetches conversation detail with messages.
final conversationDetailProvider = FutureProvider.family<
    ConversationDetailModel,
    ConversationDetailParams>((ref, params) async {
  final repository = ref.watch(chatRepositoryProvider);
  final result = await repository.getConversationDetail(
    conversationId: params.conversationId,
    userId: params.userId,
    page: params.page,
  );

  switch (result) {
    case Success(:final data):
      try {
        return ConversationDetailModel.fromJson(data);
      } catch (e) {
        throw Exception('Failed to parse conversation detail: ${e.toString()}');
      }
    case Failure(:final message):
      throw Exception(message);
  }
});

/// Parameters for conversation detail.
class ConversationDetailParams {
  final int conversationId;
  final int userId;
  final int page;

  ConversationDetailParams({
    required this.conversationId,
    required this.userId,
    this.page = 1,
  });

  @override
  bool operator ==(Object other) {
    if (identical(this, other)) return true;

    return other is ConversationDetailParams &&
        other.conversationId == conversationId &&
        other.userId == userId &&
        other.page == page;
  }

  @override
  int get hashCode => conversationId.hashCode ^ userId.hashCode ^ page.hashCode;
}

/// Notifier for selected conversation — stores the current conversation being viewed.
/// Migrated from StateProvider to Notifier for Riverpod 3.x.
class SelectedConversationNotifier extends Notifier<int?> {
  @override
  int? build() => null;

  /// Update the selected conversation ID.
  void select(int? conversationId) {
    state = conversationId;
  }
}

/// Selected conversation provider.
final selectedConversationProvider =
    NotifierProvider<SelectedConversationNotifier, int?>(SelectedConversationNotifier.new);

/// Notifier for current page in message pagination — per conversation.
/// Migrated from StateProvider.family to a family Notifier for Riverpod 3.x.
/// The conversation ID is passed via constructor through the family factory.
class CurrentPageNotifier extends Notifier<int> {
  /// build() returns the initial page number (1).
  @override
  int build() => 1;

  /// Update the current page number.
  void setPage(int page) {
    state = page;
  }
}

/// Current page provider for message pagination.
final currentPageProvider =
    NotifierProvider.family<CurrentPageNotifier, int, int>(
  (conversationId) => CurrentPageNotifier(),
);
