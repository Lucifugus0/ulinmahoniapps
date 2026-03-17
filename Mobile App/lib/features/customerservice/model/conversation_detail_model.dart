import 'conversation_model.dart';
import 'message_model.dart';

/// Conversation detail model with paginated messages
class ConversationDetailModel {
  final ConversationInfo conversation;
  final List<MessageModel> messages;
  final MessagePaginationMeta meta;

  ConversationDetailModel({
    required this.conversation,
    required this.messages,
    required this.meta,
  });

  factory ConversationDetailModel.fromJson(Map<String, dynamic> json) {
    return ConversationDetailModel(
      conversation: ConversationInfo.fromJson(
        json['conversation'] as Map<String, dynamic>? ?? {},
      ),
      messages: (json['messages'] as List<dynamic>?)
              ?.map((e) => MessageModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      meta: MessagePaginationMeta.fromJson(
        json['meta'] as Map<String, dynamic>? ?? {},
      ),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'conversation': conversation.toJson(),
      'messages': messages.map((e) => e.toJson()).toList(),
      'meta': meta.toJson(),
    };
  }

  /// Check if there are more messages to load
  bool get hasMoreMessages => meta.currentPage < meta.lastPage;
}

/// Conversation information (without messages)
class ConversationInfo {
  final int id;
  final String orderId;
  final int propertyId;
  final String title;
  final String status;
  final List<ParticipantModel> participants;

  ConversationInfo({
    required this.id,
    required this.orderId,
    required this.propertyId,
    required this.title,
    required this.status,
    required this.participants,
  });

  factory ConversationInfo.fromJson(Map<String, dynamic> json) {
    return ConversationInfo(
      id: json['id'] as int? ?? 0,
      orderId: json['order_id'] as String? ?? '',
      propertyId: json['property_id'] as int? ?? 0,
      title: json['title'] as String? ?? '',
      status: json['status'] as String? ?? 'active',
      participants: (json['participants'] as List<dynamic>?)
              ?.map((e) => ParticipantModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_id': orderId,
      'property_id': propertyId,
      'title': title,
      'status': status,
      'participants': participants.map((e) => e.toJson()).toList(),
    };
  }

  /// Get participant by role
  ParticipantModel? getParticipantByRole(String role) {
    try {
      return participants.firstWhere((p) => p.role == role);
    } catch (e) {
      return null;
    }
  }

  /// Get customer participant
  ParticipantModel? get customer => getParticipantByRole('customer');

  /// Get staff participant
  ParticipantModel? get staff => getParticipantByRole('staff');

  /// Get admin participant
  ParticipantModel? get admin => getParticipantByRole('admin');
}

/// Pagination metadata for messages
class MessagePaginationMeta {
  final int currentPage;
  final int lastPage;
  final int perPage;
  final int total;

  MessagePaginationMeta({
    required this.currentPage,
    required this.lastPage,
    required this.perPage,
    required this.total,
  });

  factory MessagePaginationMeta.fromJson(Map<String, dynamic> json) {
    return MessagePaginationMeta(
      currentPage: json['current_page'] as int? ?? 1,
      lastPage: json['last_page'] as int? ?? 1,
      perPage: json['per_page'] as int? ?? 50,
      total: json['total'] as int? ?? 0,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'current_page': currentPage,
      'last_page': lastPage,
      'per_page': perPage,
      'total': total,
    };
  }
}
