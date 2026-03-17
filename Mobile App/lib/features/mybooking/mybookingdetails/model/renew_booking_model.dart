class RenewBookingRequest {
  // User info (from auth)
  final int userId;
  final String userName;
  final String userPhoneNumber;
  final String userEmail;

  // Property info (from booking)
  final int propertyId;
  final String propertyName;
  final String propertyType;

  // Room info (from booking)
  final int roomId;
  final String roomName;

  // Booking details
  final String bookingType;
  final String checkIn;
  final String checkOut;

  // Pricing (from room details)
  final double? monthlyPrice;
  final int? bookingMonths;
  final double? adminFees;
  final double serviceFees;

  // Transaction
  final String transactionType;
  final int isRenewal; // Always 1 for renewal

  // Optional fields
  final String? voucherCode;
  final double? parkingFee;
  final String? parkingType;
  final int? parkingDuration;

  RenewBookingRequest({
    required this.userId,
    required this.userName,
    required this.userPhoneNumber,
    required this.userEmail,
    required this.propertyId,
    required this.propertyName,
    required this.propertyType,
    required this.roomId,
    required this.roomName,
    required this.bookingType,
    required this.checkIn,
    required this.checkOut,
    this.monthlyPrice,
    this.bookingMonths,
    this.adminFees,
    required this.serviceFees,
    required this.transactionType,
    this.isRenewal = 1, // Default to 1 for renewal
    this.voucherCode,
    this.parkingFee,
    this.parkingType,
    this.parkingDuration,
  });

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = {
      // User info
      'user_id': userId,
      'user_name': userName,
      'user_phone_number': userPhoneNumber,
      'user_email': userEmail,

      // Property info
      'property_id': propertyId,
      'property_name': propertyName,
      'property_type': propertyType,

      // Room info
      'room_id': roomId,
      'room_name': roomName,

      // Booking details
      'booking_type': bookingType,
      'check_in': checkIn,
      'check_out': checkOut,

      // Pricing
      if (monthlyPrice != null) 'monthly_price': monthlyPrice,
      if (bookingMonths != null) 'booking_months': bookingMonths,
      'admin_fees': adminFees ?? 0,
      'service_fees': serviceFees,

      // Transaction
      'transaction_type': transactionType,
      'is_renewal': isRenewal,
    };

    // Optional fields
    if (voucherCode != null && voucherCode!.isNotEmpty) {
      data['voucher_code'] = voucherCode;
    }

    if (parkingFee != null) {
      data['parking_fee'] = parkingFee;
    }

    if (parkingType != null && parkingType!.isNotEmpty) {
      data['parking_type'] = parkingType;
    }

    if (parkingDuration != null) {
      data['parking_duration'] = parkingDuration;
    }

    return data;
  }
}

class RenewBookingResponse {
  final String status;
  final String message;
  final RenewBookingData? data;

  RenewBookingResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory RenewBookingResponse.fromJson(Map<String, dynamic> json) {
    return RenewBookingResponse(
      status: json['status'] as String,
      message: json['message'] as String,
      data: json['data'] != null
          ? RenewBookingData.fromJson(json['data'] as Map<String, dynamic>)
          : null,
    );
  }
}

class RenewBookingData {
  final String orderId;
  final String? bookingId; // idrec for API calls
  final int? roomId;
  final int? propertyId;
  final int? userId;
  final String checkIn;
  final String checkOut;
  final String? bookingType;
  final int? bookingDays;
  final int? bookingMonths;
  final double? roomPrice;
  final double? serviceFees;
  final double? grandTotal;
  final String? transactionStatus;
  final String? transactionType;
  final String? voucherCode;
  final double? discountAmount;
  final double? subtotalBeforeDiscount;

  RenewBookingData({
    required this.orderId,
    this.bookingId,
    this.roomId,
    this.propertyId,
    this.userId,
    required this.checkIn,
    required this.checkOut,
    this.bookingType,
    this.bookingDays,
    this.bookingMonths,
    this.roomPrice,
    this.serviceFees,
    this.grandTotal,
    this.transactionStatus,
    this.transactionType,
    this.voucherCode,
    this.discountAmount,
    this.subtotalBeforeDiscount,
  });

  factory RenewBookingData.fromJson(Map<String, dynamic> json) {
    // API returns nested structure with new_order_id and transaction object
    final transaction = json['transaction'] as Map<String, dynamic>?;

    return RenewBookingData(
      orderId: json['new_order_id']?.toString() ?? transaction?['order_id']?.toString() ?? '',
      bookingId: json['new_booking_id']?.toString() ?? transaction?['idrec']?.toString(),
      roomId: transaction?['room_id'] != null ? int.tryParse(transaction!['room_id'].toString()) : null,
      propertyId: transaction?['property_id'] != null ? int.tryParse(transaction!['property_id'].toString()) : null,
      userId: transaction?['user_id'] != null ? int.tryParse(transaction!['user_id'].toString()) : null,
      checkIn: transaction?['check_in']?.toString() ?? json['check_in']?.toString() ?? '',
      checkOut: transaction?['check_out']?.toString() ?? json['check_out']?.toString() ?? '',
      bookingType: transaction?['booking_type']?.toString(),
      bookingDays: transaction?['booking_days'] != null ? int.tryParse(transaction!['booking_days'].toString()) : null,
      bookingMonths: transaction?['booking_months'] != null ? int.tryParse(transaction!['booking_months'].toString()) : null,
      roomPrice: transaction?['room_price'] != null ? double.tryParse(transaction!['room_price'].toString()) : null,
      serviceFees: transaction?['service_fees'] != null ? double.tryParse(transaction!['service_fees'].toString()) : null,
      grandTotal: transaction?['grandtotal_price'] != null ? double.tryParse(transaction!['grandtotal_price'].toString()) : null,
      transactionStatus: transaction?['transaction_status']?.toString(),
      transactionType: transaction?['transaction_type']?.toString(),
      voucherCode: transaction?['voucher_code']?.toString(),
      discountAmount: transaction?['discount_amount'] != null ? double.tryParse(transaction!['discount_amount'].toString()) : null,
      subtotalBeforeDiscount: transaction?['subtotal_before_discount'] != null ? double.tryParse(transaction!['subtotal_before_discount'].toString()) : null,
    );
  }

  @override
  String toString() {
    return 'RenewBookingData(orderId: $orderId, roomId: $roomId, propertyId: $propertyId, checkIn: $checkIn, checkOut: $checkOut, grandTotal: $grandTotal, transactionStatus: $transactionStatus, transactionType: $transactionType)';
  }
}
