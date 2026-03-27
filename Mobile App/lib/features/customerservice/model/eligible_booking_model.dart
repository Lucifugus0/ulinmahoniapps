/// EligibleBookingModel — a booking that is eligible for ticket creation.
/// Returned by the /tickets/eligible-bookings endpoint.
class EligibleBookingModel {
  final String orderId;
  final int? propertyId;
  final String? propertyName;
  final String? roomName;
  final String? checkIn;
  final String? checkOut;
  final String? transactionStatus;

  EligibleBookingModel({
    required this.orderId,
    this.propertyId,
    this.propertyName,
    this.roomName,
    this.checkIn,
    this.checkOut,
    this.transactionStatus,
  });

  factory EligibleBookingModel.fromJson(Map<String, dynamic> json) {
    /* property_id may come as String from API — parse safely to int */
    final rawId = json['property_id'];
    return EligibleBookingModel(
      orderId: json['order_id'] ?? '',
      propertyId: rawId is int ? rawId : int.tryParse(rawId?.toString() ?? ''),
      propertyName: json['property_name'],
      roomName: json['room_name'],
      checkIn: json['check_in'],
      checkOut: json['check_out'],
      transactionStatus: json['transaction_status'],
    );
  }
}
