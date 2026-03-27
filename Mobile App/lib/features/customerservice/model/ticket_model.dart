import 'ticket_category_model.dart';

/// TicketModel — represents a customer service ticket in the list view.
/// Contains ticket metadata, status, category, and unread count.
class TicketModel {
  final int id;
  final String ticketNumber;
  final String? orderId;
  final int? propertyId;
  final int userId;
  final int categoryId;
  final String recipientType;
  final String subject;
  final String ticketStatus;
  final String priority;
  final DateTime? closedAt;
  final DateTime? reopenDeadline;
  final DateTime? lastMessageAt;
  final DateTime createdAt;
  final int unreadCount;
  final TicketCategoryModel? category;
  final TicketPropertyInfo? property;
  final TicketTransactionInfo? transaction;

  TicketModel({
    required this.id,
    required this.ticketNumber,
    this.orderId,
    this.propertyId,
    required this.userId,
    required this.categoryId,
    required this.recipientType,
    required this.subject,
    required this.ticketStatus,
    required this.priority,
    this.closedAt,
    this.reopenDeadline,
    this.lastMessageAt,
    required this.createdAt,
    this.unreadCount = 0,
    this.category,
    this.property,
    this.transaction,
  });

  /// Parse from API JSON response
  factory TicketModel.fromJson(Map<String, dynamic> json) {
    return TicketModel(
      id: json['id'] ?? 0,
      ticketNumber: json['ticket_number'] ?? '',
      orderId: json['order_id'],
      propertyId: json['property_id'],
      userId: json['user_id'] ?? 0,
      categoryId: json['category_id'] ?? 0,
      recipientType: json['recipient_type'] ?? 'front_desk',
      subject: json['subject'] ?? '',
      ticketStatus: json['ticket_status'] ?? 'open',
      priority: json['priority'] ?? 'normal',
      closedAt: json['closed_at'] != null ? DateTime.tryParse(json['closed_at']) : null,
      reopenDeadline: json['reopen_deadline'] != null ? DateTime.tryParse(json['reopen_deadline']) : null,
      lastMessageAt: json['last_message_at'] != null ? DateTime.tryParse(json['last_message_at']) : null,
      createdAt: DateTime.tryParse(json['created_at'] ?? '') ?? DateTime.now(),
      unreadCount: json['unread_count'] ?? 0,
      category: json['category'] != null ? TicketCategoryModel.fromJson(json['category']) : null,
      property: json['property'] != null ? TicketPropertyInfo.fromJson(json['property']) : null,
      transaction: json['transaction'] != null ? TicketTransactionInfo.fromJson(json['transaction']) : null,
    );
  }

  /// Whether this ticket is in an active (non-closed) state
  bool get isActive => ['open', 'in_progress', 'reopened'].contains(ticketStatus);

  /// Whether this ticket can be reopened (closed + within deadline)
  bool get canReopen =>
      ticketStatus == 'closed' &&
      reopenDeadline != null &&
      DateTime.now().isBefore(reopenDeadline!);

  /// Whether this ticket has unread messages
  bool get hasUnread => unreadCount > 0;
}

/// Lightweight property info nested in ticket response
class TicketPropertyInfo {
  final int idrec;
  final String name;

  TicketPropertyInfo({required this.idrec, required this.name});

  factory TicketPropertyInfo.fromJson(Map<String, dynamic> json) {
    return TicketPropertyInfo(
      idrec: json['idrec'] ?? 0,
      name: json['name'] ?? '',
    );
  }
}

/// Lightweight transaction info nested in ticket response
class TicketTransactionInfo {
  final String? orderId;
  final String? roomName;
  final String? propertyName;

  TicketTransactionInfo({this.orderId, this.roomName, this.propertyName});

  factory TicketTransactionInfo.fromJson(Map<String, dynamic> json) {
    return TicketTransactionInfo(
      orderId: json['order_id'],
      roomName: json['room_name'],
      propertyName: json['property_name'],
    );
  }
}
