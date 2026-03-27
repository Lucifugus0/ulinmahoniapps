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
    return EligibleBookingModel(
      orderId: json['order_id'] ?? '',
      propertyId: json['property_id'],
      propertyName: json['property_name'],
      roomName: json['room_name'],
      checkIn: json['check_in'],
      checkOut: json['check_out'],
      transactionStatus: json['transaction_status'],
    );
  }
}
