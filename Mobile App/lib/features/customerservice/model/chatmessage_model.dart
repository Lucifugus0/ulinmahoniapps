import 'messageattachment_model.dart';

/// Model for a chat message
class ChatMessage {
  final int id;
  final int chatRoomId;
  final int senderId;
  final String senderName;
  final String senderType; // 'customer', 'fo', 'ho'
  final String message;
  final List<MessageAttachment> attachments;
  final DateTime createdAt;
  final bool isRead;

  ChatMessage({
    required this.id,
    required this.chatRoomId,
    required this.senderId,
    required this.senderName,
    required this.senderType,
    required this.message,
    required this.attachments,
    required this.createdAt,
    required this.isRead,
  });

  /// Factory constructor from JSON
  factory ChatMessage.fromJson(Map<String, dynamic> json) {
    return ChatMessage(
      id: json['id'] as int,
      chatRoomId: json['chat_room_id'] as int,
      senderId: json['sender_id'] as int,
      senderName: json['sender_name'] as String? ?? '',
      senderType: json['sender_type'] as String? ?? 'customer',
      message: json['message'] as String? ?? '',
      attachments: (json['attachments'] as List<dynamic>?)
              ?.map((e) => MessageAttachment.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      createdAt: DateTime.parse(json['created_at'] as String),
      isRead: json['is_read'] as bool? ?? false,
    );
  }

  /// Convert to JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'chat_room_id': chatRoomId,
      'sender_id': senderId,
      'sender_name': senderName,
      'sender_type': senderType,
      'message': message,
      'attachments': attachments.map((e) => e.toJson()).toList(),
      'created_at': createdAt.toIso8601String(),
      'is_read': isRead,
    };
  }

  /// Copy with method for immutability
  ChatMessage copyWith({
    int? id,
    int? chatRoomId,
    int? senderId,
    String? senderName,
    String? senderType,
    String? message,
    List<MessageAttachment>? attachments,
    DateTime? createdAt,
    bool? isRead,
  }) {
    return ChatMessage(
      id: id ?? this.id,
      chatRoomId: chatRoomId ?? this.chatRoomId,
      senderId: senderId ?? this.senderId,
      senderName: senderName ?? this.senderName,
      senderType: senderType ?? this.senderType,
      message: message ?? this.message,
      attachments: attachments ?? this.attachments,
      createdAt: createdAt ?? this.createdAt,
      isRead: isRead ?? this.isRead,
    );
  }

  /// Check if this message is from the current user
  bool isFromMe(int currentUserId) {
    return senderId == currentUserId && senderType == 'customer';
  }

  /// Check if message has attachments
  bool get hasAttachments => attachments.isNotEmpty;

  /// Get formatted time (HH:mm)
  String get formattedTime {
    return '${createdAt.hour.toString().padLeft(2, '0')}:${createdAt.minute.toString().padLeft(2, '0')}';
  }

  /// Get formatted date (dd/MM/yyyy)
  String get formattedDate {
    return '${createdAt.day.toString().padLeft(2, '0')}/${createdAt.month.toString().padLeft(2, '0')}/${createdAt.year}';
  }
}
