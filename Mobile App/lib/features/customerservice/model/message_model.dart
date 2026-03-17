/// Simplified message model matching backend API response
class MessageModel {
  final int id;
  final int conversationId;
  final int senderId;
  final String messageText;
  final String messageType; // 'text', 'image', 'system'
  final bool isEdited;
  final DateTime createdAt;
  final DateTime? editedAt;
  final SenderInfo sender;
  final List<AttachmentInfo> attachments;

  MessageModel({
    required this.id,
    required this.conversationId,
    required this.senderId,
    required this.messageText,
    required this.messageType,
    required this.isEdited,
    required this.createdAt,
    this.editedAt,
    required this.sender,
    required this.attachments,
  });

  factory MessageModel.fromJson(Map<String, dynamic> json) {
    return MessageModel(
      id: json['id'] as int,
      conversationId: json['conversation_id'] as int? ?? 0,
      senderId: json['sender_id'] as int,
      messageText: json['message_text'] as String? ?? '',
      messageType: json['message_type'] as String? ?? 'text',
      isEdited: json['is_edited'] as bool? ?? false,
      createdAt: DateTime.parse(json['created_at'] as String).toLocal(),
      editedAt: json['edited_at'] != null
          ? DateTime.parse(json['edited_at'] as String).toLocal()
          : null,
      sender: SenderInfo.fromJson(json['sender'] as Map<String, dynamic>? ?? {}),
      attachments: (json['attachments'] as List<dynamic>?)
              ?.map((e) => AttachmentInfo.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'conversation_id': conversationId,
      'sender_id': senderId,
      'message_text': messageText,
      'message_type': messageType,
      'is_edited': isEdited,
      'created_at': createdAt.toIso8601String(),
      'edited_at': editedAt?.toIso8601String(),
      'sender': sender.toJson(),
      'attachments': attachments.map((e) => e.toJson()).toList(),
    };
  }

  /// Check if message has attachments
  bool get hasAttachments => attachments.isNotEmpty;

  /// Check if message is an image
  bool get isImage => messageType == 'image';

  /// Check if message is a system message
  bool get isSystem => messageType == 'system';

  /// Get formatted time (HH:mm)
  String get formattedTime {
    return '${createdAt.hour.toString().padLeft(2, '0')}:${createdAt.minute.toString().padLeft(2, '0')}';
  }

  /// Get formatted date (dd/MM/yyyy)
  String get formattedDate {
    return '${createdAt.day.toString().padLeft(2, '0')}/${createdAt.month.toString().padLeft(2, '0')}/${createdAt.year}';
  }

  /// Check if this message is from the current user
  bool isFromUser(int userId) {
    return senderId == userId;
  }
}

/// Sender information
class SenderInfo {
  final int id;
  final String firstName;
  final String lastName;
  final String? email;

  SenderInfo({
    required this.id,
    required this.firstName,
    required this.lastName,
    this.email,
  });

  factory SenderInfo.fromJson(Map<String, dynamic> json) {
    return SenderInfo(
      id: json['id'] as int? ?? 0,
      firstName: json['first_name'] as String? ?? '',
      lastName: json['last_name'] as String? ?? '',
      email: json['email'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'first_name': firstName,
      'last_name': lastName,
      'email': email,
    };
  }

  String get fullName => '$firstName $lastName'.trim();
}

/// Attachment information
class AttachmentInfo {
  final int id;
  final String fileName;
  final String fileUrl;
  final String fileType;
  final int? fileSize;

  AttachmentInfo({
    required this.id,
    required this.fileName,
    required this.fileUrl,
    required this.fileType,
    this.fileSize,
  });

  factory AttachmentInfo.fromJson(Map<String, dynamic> json) {
    return AttachmentInfo(
      id: json['id'] as int? ?? 0,
      fileName: json['file_name'] as String? ?? '',
      fileUrl: json['file_url'] as String? ?? '',
      fileType: json['file_type'] as String? ?? 'unknown',
      fileSize: json['file_size'] as int?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'file_name': fileName,
      'file_url': fileUrl,
      'file_type': fileType,
      'file_size': fileSize,
    };
  }

  bool get isImage => fileType.toLowerCase().startsWith('image');
  bool get isPdf => fileType.toLowerCase() == 'pdf' || fileType.toLowerCase() == 'application/pdf';
}
