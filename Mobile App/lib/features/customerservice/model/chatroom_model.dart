import 'chatmessage_model.dart';

/// Model for a chat room
class ChatRoom {
  final int id;
  final int bookingId;
  final String bookingOrderId;
  final int customerId;
  final String customerName;
  final String recipientType; // 'fo' or 'ho'
  final String recipientName; // e.g., "Front Office - Property Name" or "Head Office Finance"
  final int? recipientId;
  final ChatMessage? lastMessage;
  final int unreadCount;
  final DateTime createdAt;
  final DateTime? updatedAt;
  final bool isActive;

  ChatRoom({
    required this.id,
    required this.bookingId,
    required this.bookingOrderId,
    required this.customerId,
    required this.customerName,
    required this.recipientType,
    required this.recipientName,
    this.recipientId,
    this.lastMessage,
    required this.unreadCount,
    required this.createdAt,
    this.updatedAt,
    required this.isActive,
  });

  /// Factory constructor from JSON
  factory ChatRoom.fromJson(Map<String, dynamic> json) {
    return ChatRoom(
      id: json['id'] as int,
      bookingId: json['booking_id'] as int,
      bookingOrderId: json['booking_order_id'] as String,
      customerId: json['customer_id'] as int,
      customerName: json['customer_name'] as String? ?? '',
      recipientType: json['recipient_type'] as String,
      recipientName: json['recipient_name'] as String? ?? '',
      recipientId: json['recipient_id'] as int?,
      lastMessage: json['last_message'] != null
          ? ChatMessage.fromJson(json['last_message'] as Map<String, dynamic>)
          : null,
      unreadCount: json['unread_count'] as int? ?? 0,
      createdAt: DateTime.parse(json['created_at'] as String),
      updatedAt: json['updated_at'] != null
          ? DateTime.parse(json['updated_at'] as String)
          : null,
      isActive: json['is_active'] as bool? ?? true,
    );
  }

  /// Convert to JSON
  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'booking_id': bookingId,
      'booking_order_id': bookingOrderId,
      'customer_id': customerId,
      'customer_name': customerName,
      'recipient_type': recipientType,
      'recipient_name': recipientName,
      'recipient_id': recipientId,
      'last_message': lastMessage?.toJson(),
      'unread_count': unreadCount,
      'created_at': createdAt.toIso8601String(),
      'updated_at': updatedAt?.toIso8601String(),
      'is_active': isActive,
    };
  }

  /// Copy with method for immutability
  ChatRoom copyWith({
    int? id,
    int? bookingId,
    String? bookingOrderId,
    int? customerId,
    String? customerName,
    String? recipientType,
    String? recipientName,
    int? recipientId,
    ChatMessage? lastMessage,
    int? unreadCount,
    DateTime? createdAt,
    DateTime? updatedAt,
    bool? isActive,
  }) {
    return ChatRoom(
      id: id ?? this.id,
      bookingId: bookingId ?? this.bookingId,
      bookingOrderId: bookingOrderId ?? this.bookingOrderId,
      customerId: customerId ?? this.customerId,
      customerName: customerName ?? this.customerName,
      recipientType: recipientType ?? this.recipientType,
      recipientName: recipientName ?? this.recipientName,
      recipientId: recipientId ?? this.recipientId,
      lastMessage: lastMessage ?? this.lastMessage,
      unreadCount: unreadCount ?? this.unreadCount,
      createdAt: createdAt ?? this.createdAt,
      updatedAt: updatedAt ?? this.updatedAt,
      isActive: isActive ?? this.isActive,
    );
  }

  /// Check if chat room is with Front Office
  bool get isFrontOffice => recipientType.toLowerCase() == 'fo';

  /// Check if chat room is with Head Office
  bool get isHeadOffice => recipientType.toLowerCase() == 'ho';

  /// Check if there are unread messages
  bool get hasUnreadMessages => unreadCount > 0;

  /// Get formatted last message time
  String get formattedLastMessageTime {
    if (lastMessage == null) return '';

    final now = DateTime.now();
    final messageDate = lastMessage!.createdAt;
    final difference = now.difference(messageDate);

    if (difference.inDays == 0) {
      // Today - show time
      return lastMessage!.formattedTime;
    } else if (difference.inDays == 1) {
      // Yesterday
      return 'Yesterday';
    } else if (difference.inDays < 7) {
      // This week - show day name
      final days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
      return days[messageDate.weekday - 1];
    } else {
      // Older - show date
      return lastMessage!.formattedDate;
    }
  }

  /// Get preview of last message (truncated)
  String get lastMessagePreview {
    if (lastMessage == null) return 'No messages yet';

    if (lastMessage!.message.isNotEmpty) {
      return lastMessage!.message.length > 50
          ? '${lastMessage!.message.substring(0, 50)}...'
          : lastMessage!.message;
    } else if (lastMessage!.hasAttachments) {
      final attachment = lastMessage!.attachments.first;
      if (attachment.isImage) {
        return '📷 Image';
      } else if (attachment.isPdf) {
        return '📄 PDF';
      } else {
        return '📎 File';
      }
    }

    return 'No messages yet';
  }
}
