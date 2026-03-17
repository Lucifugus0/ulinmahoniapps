import 'message_model.dart';

/// Conversation model for conversation list response
class ConversationModel {
  final int id;
  final String orderId;
  final int propertyId;
  final String title;
  final String status; // 'active', 'archived', 'closed'
  final DateTime? lastMessageAt;
  final int unreadCount;
  final List<MessageModel> messages; // Last few messages
  final List<ParticipantModel> participants;
  final PropertyInfo? property;
  final TransactionInfo? transaction;

  ConversationModel({
    required this.id,
    required this.orderId,
    required this.propertyId,
    required this.title,
    required this.status,
    this.lastMessageAt,
    required this.unreadCount,
    required this.messages,
    required this.participants,
    this.property,
    this.transaction,
  });

  factory ConversationModel.fromJson(Map<String, dynamic> json) {
    return ConversationModel(
      id: json['id'] as int,
      orderId: json['order_id'] as String,
      propertyId: json['property_id'] as int,
      title: json['title'] as String? ?? '',
      status: json['status'] as String? ?? 'active',
      lastMessageAt: json['last_message_at'] != null
          ? DateTime.parse(json['last_message_at'] as String)
          : null,
      unreadCount: json['unread_count'] as int? ?? 0,
      messages: (json['messages'] as List<dynamic>?)
              ?.map((e) => MessageModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      participants: (json['participants'] as List<dynamic>?)
              ?.map((e) => ParticipantModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      property: json['property'] != null
          ? PropertyInfo.fromJson(json['property'] as Map<String, dynamic>)
          : null,
      transaction: json['transaction'] != null
          ? TransactionInfo.fromJson(json['transaction'] as Map<String, dynamic>)
          : null,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'order_id': orderId,
      'property_id': propertyId,
      'title': title,
      'status': status,
      'last_message_at': lastMessageAt?.toIso8601String(),
      'unread_count': unreadCount,
      'messages': messages.map((e) => e.toJson()).toList(),
      'participants': participants.map((e) => e.toJson()).toList(),
      'property': property?.toJson(),
      'transaction': transaction?.toJson(),
    };
  }

  /// Check if there are unread messages
  bool get hasUnreadMessages => unreadCount > 0;

  /// Check if conversation is active
  bool get isActive => status == 'active';

  /// Get last message
  MessageModel? get lastMessage => messages.isNotEmpty ? messages.first : null;

  /// Get formatted last message time
  String get formattedLastMessageTime {
    if (lastMessageAt == null) return '';

    final now = DateTime.now();
    final difference = now.difference(lastMessageAt!);

    if (difference.inDays == 0) {
      // Today - show time
      return '${lastMessageAt!.hour.toString().padLeft(2, '0')}:${lastMessageAt!.minute.toString().padLeft(2, '0')}';
    } else if (difference.inDays == 1) {
      return 'Yesterday';
    } else if (difference.inDays < 7) {
      final days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
      return days[lastMessageAt!.weekday - 1];
    } else {
      return '${lastMessageAt!.day.toString().padLeft(2, '0')}/${lastMessageAt!.month.toString().padLeft(2, '0')}/${lastMessageAt!.year}';
    }
  }

  /// Get preview of last message
  String get lastMessagePreview {
    final msg = lastMessage;
    if (msg == null) return 'No messages yet';

    if (msg.messageText.isNotEmpty) {
      return msg.messageText.length > 50
          ? '${msg.messageText.substring(0, 50)}...'
          : msg.messageText;
    } else if (msg.hasAttachments) {
      final attachment = msg.attachments.first;
      if (attachment.isImage) return '📷 Image';
      if (attachment.isPdf) return '📄 PDF';
      return '📎 File';
    }

    return 'No messages yet';
  }
}

/// Participant model
class ParticipantModel {
  final int id;
  final int userId;
  final String role; // 'customer', 'staff', 'admin'
  final DateTime joinedAt;
  final SenderInfo user;

  ParticipantModel({
    required this.id,
    required this.userId,
    required this.role,
    required this.joinedAt,
    required this.user,
  });

  factory ParticipantModel.fromJson(Map<String, dynamic> json) {
    return ParticipantModel(
      id: json['id'] as int? ?? 0,
      userId: json['user_id'] as int? ?? 0,
      role: json['role'] as String? ?? 'customer',
      joinedAt: json['joined_at'] != null
          ? DateTime.parse(json['joined_at'] as String)
          : DateTime.now(),
      user: SenderInfo.fromJson(json['user'] as Map<String, dynamic>? ?? {}),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'user_id': userId,
      'role': role,
      'joined_at': joinedAt.toIso8601String(),
      'user': user.toJson(),
    };
  }

  bool get isCustomer => role == 'customer';
  bool get isStaff => role == 'staff';
  bool get isAdmin => role == 'admin';
}

/// Property information
class PropertyInfo {
  final int idrec;
  final String name;

  PropertyInfo({
    required this.idrec,
    required this.name,
  });

  factory PropertyInfo.fromJson(Map<String, dynamic> json) {
    return PropertyInfo(
      idrec: json['idrec'] as int? ?? 0,
      name: json['name'] as String? ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'idrec': idrec,
      'name': name,
    };
  }
}

/// Transaction information
class TransactionInfo {
  final int idrec;
  final String orderId;
  final String userName;
  final String userEmail;

  TransactionInfo({
    required this.idrec,
    required this.orderId,
    required this.userName,
    required this.userEmail,
  });

  factory TransactionInfo.fromJson(Map<String, dynamic> json) {
    return TransactionInfo(
      idrec: json['idrec'] as int? ?? 0,
      orderId: json['order_id'] as String? ?? '',
      userName: json['user_name'] as String? ?? '',
      userEmail: json['user_email'] as String? ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'idrec': idrec,
      'order_id': orderId,
      'user_name': userName,
      'user_email': userEmail,
    };
  }
}
