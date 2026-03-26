/// TicketMessageModel — a single message within a ticket conversation.
/// Supports text, image, and system message types with sender info and attachments.
class TicketMessageModel {
  final int id;
  final int ticketId;
  final int senderId;
  final String? messageText;
  final String messageType;
  final bool isEdited;
  final DateTime createdAt;
  final TicketSenderInfo? sender;
  final List<TicketAttachmentModel> attachments;

  TicketMessageModel({
    required this.id,
    required this.ticketId,
    required this.senderId,
    this.messageText,
    required this.messageType,
    this.isEdited = false,
    required this.createdAt,
    this.sender,
    this.attachments = const [],
  });

  /// Parse from API JSON response
  factory TicketMessageModel.fromJson(Map<String, dynamic> json) {
    return TicketMessageModel(
      id: json['id'] ?? 0,
      ticketId: json['ticket_id'] ?? 0,
      senderId: json['sender_id'] ?? 0,
      messageText: json['message_text'],
      messageType: json['message_type'] ?? 'text',
      isEdited: json['is_edited'] == true || json['is_edited'] == 1,
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      sender: json['sender'] != null ? TicketSenderInfo.fromJson(json['sender']) : null,
      attachments: json['attachments'] != null
          ? (json['attachments'] as List).map((a) => TicketAttachmentModel.fromJson(a)).toList()
          : [],
    );
  }

  /// Whether this message is a system-generated status change message
  bool get isSystem => messageType == 'system';

  /// Whether this message is an image message
  bool get isImage => messageType == 'image';

  /// Whether this message was sent by the given user
  bool isFromUser(int userId) => senderId == userId;

  /// Formatted time for display (HH:mm)
  String get formattedTime =>
      '${createdAt.hour.toString().padLeft(2, '0')}:${createdAt.minute.toString().padLeft(2, '0')}';

  /// Formatted date for grouping (dd MMM yyyy)
  String get formattedDate {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return '${createdAt.day} ${months[createdAt.month - 1]} ${createdAt.year}';
  }
}

/// Sender info nested in message response
class TicketSenderInfo {
  final int id;
  final String? firstName;
  final String? lastName;
  final String? email;
  final String? name;

  TicketSenderInfo({required this.id, this.firstName, this.lastName, this.email, this.name});

  factory TicketSenderInfo.fromJson(Map<String, dynamic> json) {
    return TicketSenderInfo(
      id: json['id'] ?? 0,
      firstName: json['first_name'],
      lastName: json['last_name'],
      email: json['email'],
      name: json['name'],
    );
  }

  /// Display name: first_name or name fallback
  String get displayName => firstName ?? name ?? 'User';
}

/// Attachment info nested in message response
class TicketAttachmentModel {
  final int id;
  final String fileName;
  final String? fileUrl;
  final String? thumbnailUrl;
  final String fileType;
  final int fileSize;

  TicketAttachmentModel({
    required this.id,
    required this.fileName,
    this.fileUrl,
    this.thumbnailUrl,
    required this.fileType,
    required this.fileSize,
  });

  factory TicketAttachmentModel.fromJson(Map<String, dynamic> json) {
    return TicketAttachmentModel(
      id: json['id'] ?? 0,
      fileName: json['file_name'] ?? '',
      fileUrl: json['file_url'],
      thumbnailUrl: json['thumbnail_url'],
      fileType: json['file_type'] ?? '',
      fileSize: json['file_size'] ?? 0,
    );
  }

  /// Whether this attachment is an image
  bool get isImage => fileType.startsWith('image/');
}
