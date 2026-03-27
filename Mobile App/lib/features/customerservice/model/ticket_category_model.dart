/// TicketCategory model — represents a ticket type + category combination.
/// Used in the create-ticket flow for the user to select their issue type.
class TicketCategoryModel {
  final int id;
  final String ticketType;
  final String category;
  final String labelEn;
  final String labelId;
  final String labelZh;
  final String recipientType;
  final bool requiresBooking;
  final int sortOrder;

  TicketCategoryModel({
    required this.id,
    required this.ticketType,
    required this.category,
    required this.labelEn,
    required this.labelId,
    required this.labelZh,
    required this.recipientType,
    required this.requiresBooking,
    required this.sortOrder,
  });

  /// Parse from API JSON response
  factory TicketCategoryModel.fromJson(Map<String, dynamic> json) {
    return TicketCategoryModel(
      id: json['id'] ?? 0,
      ticketType: json['ticket_type'] ?? '',
      category: json['category'] ?? '',
      labelEn: json['label_en'] ?? '',
      labelId: json['label_id'] ?? '',
      labelZh: json['label_zh'] ?? '',
      recipientType: json['recipient_type'] ?? 'front_desk',
      requiresBooking: json['requires_booking'] == true || json['requires_booking'] == 1,
      sortOrder: json['sort_order'] ?? 0,
    );
  }

  /// Get localized label based on locale code
  String getLabel(String locale) {
    switch (locale) {
      case 'id':
        return labelId;
      case 'zh':
        return labelZh;
      default:
        return labelEn;
    }
  }
}
