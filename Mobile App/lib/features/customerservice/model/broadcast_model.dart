/// BroadcastModel — represents a one-way announcement from Front Desk or HQ.
/// Broadcasts appear as read-only items in the user's ticket list.
class BroadcastModel {
  final int id;
  final String broadcastNumber;
  final String senderType;
  final int? propertyId;
  final String title;
  final String messageText;
  final String audience;
  final int recipientCount;
  final DateTime sentAt;
  final bool isRead;
  final BroadcastSenderInfo? sender;
  final BroadcastPropertyInfo? property;

  BroadcastModel({
    required this.id,
    required this.broadcastNumber,
    required this.senderType,
    this.propertyId,
    required this.title,
    required this.messageText,
    required this.audience,
    required this.recipientCount,
    required this.sentAt,
    this.isRead = false,
    this.sender,
    this.property,
  });

  /// Parse from API JSON response
  factory BroadcastModel.fromJson(Map<String, dynamic> json) {
    return BroadcastModel(
      id: json['id'] ?? 0,
      broadcastNumber: json['broadcast_number'] ?? '',
      senderType: json['sender_type'] ?? 'hq',
      propertyId: json['property_id'],
      title: json['title'] ?? '',
      messageText: json['message_text'] ?? '',
      audience: json['audience'] ?? '',
      recipientCount: json['recipient_count'] ?? 0,
      sentAt: DateTime.tryParse(json['sent_at'] ?? '') ?? DateTime.now(),
      isRead: json['is_read'] == true,
      sender: json['sender'] != null ? BroadcastSenderInfo.fromJson(json['sender']) : null,
      property: json['property'] != null ? BroadcastPropertyInfo.fromJson(json['property']) : null,
    );
  }

  /// Whether this broadcast is from HQ
  bool get isHQ => senderType == 'hq';

  /// Formatted sent date
  String get formattedDate {
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
    return '${sentAt.day} ${months[sentAt.month - 1]} ${sentAt.year}';
  }
}

/// Sender info nested in broadcast response
class BroadcastSenderInfo {
  final int id;
  final String? firstName;
  final String? lastName;
  final String? name;

  BroadcastSenderInfo({required this.id, this.firstName, this.lastName, this.name});

  factory BroadcastSenderInfo.fromJson(Map<String, dynamic> json) {
    return BroadcastSenderInfo(
      id: json['id'] ?? 0,
      firstName: json['first_name'],
      lastName: json['last_name'],
      name: json['name'],
    );
  }

  String get displayName => firstName ?? name ?? 'Admin';
}

/// Property info nested in broadcast response
class BroadcastPropertyInfo {
  final int idrec;
  final String name;

  BroadcastPropertyInfo({required this.idrec, required this.name});

  factory BroadcastPropertyInfo.fromJson(Map<String, dynamic> json) {
    return BroadcastPropertyInfo(
      idrec: json['idrec'] ?? 0,
      name: json['name'] ?? '',
    );
  }
}
