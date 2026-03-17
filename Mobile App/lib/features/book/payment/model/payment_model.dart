class BookingRequest {
  final int userId;
  final String? userName;
  final String userPhoneNumber;
  final String propertyId;
  final String propertyName;
  final String checkIn;
  final String checkOut;
  final String roomName;
  final String roomId;
  final String userEmail;
  final String bookingType;
  final double? dailyPrice;
  final double? monthlyPrice;
  // Multi-Tier Pricing: annual price and duration
  final double? annualPrice;
  final int? bookingYears;
  final double? serviceFee;
  final String propertyType;
  final int? bookingDays;
  final int? bookingMonths;
  final String transactionType;
  final String? voucherCode;
  final double? depositFee;
  final double? parkingFee;
  final String? parkingType;
  final int? parkingDuration;
  final int isRenewal;

  BookingRequest({
    required this.userId,
    required this.userName,
    required this.userPhoneNumber,
    required this.propertyId,
    required this.propertyName,
    required this.checkIn,
    required this.checkOut,
    required this.roomName,
    required this.roomId,
    required this.userEmail,
    this.dailyPrice,
    this.monthlyPrice,
    this.annualPrice,
    this.bookingYears,
    required this.serviceFee,
    required this.propertyType,
    required this.bookingType,
    this.bookingDays,
    this.bookingMonths,
    required this.transactionType,
    this.voucherCode,
    this.depositFee,
    this.parkingFee,
    this.parkingType,
    this.parkingDuration,
    this.isRenewal = 0,
  });

  Map<String, dynamic> toJson() {
    return {
      'user_id': userId,
      'user_name': userName,
      'user_phone_number': userPhoneNumber,
      'property_id': propertyId,
      'property_name': propertyName,
      'check_in': checkIn,
      'check_out': checkOut,
      'room_name': roomName,
      'room_id': roomId,
      'user_email': userEmail,
      'daily_price': dailyPrice,
      'monthly_price': monthlyPrice,
      // Multi-Tier Pricing: annual fields
      if (annualPrice != null) 'annual_price': annualPrice,
      if (bookingYears != null) 'booking_years': bookingYears,
      'service_fees': serviceFee,
      'property_type': propertyType,
      'booking_type': bookingType,
      'booking_days': bookingDays,
      'booking_months': bookingMonths,
      'transaction_type': transactionType,
      if (voucherCode != null) 'voucher_code': voucherCode,
      if (depositFee != null) 'deposit_fee': depositFee,
      if (parkingFee != null) 'parking_fee': parkingFee,
      if (parkingType != null) 'parking_type': parkingType,
      if (parkingDuration != null) 'parking_duration': parkingDuration,
      'is_renewal': isRenewal,
    };
  }

  factory BookingRequest.fromJson(Map<String, dynamic> json) {
    return BookingRequest(
      userId: json['user_id'],
      userName: json['user_name'],
      userPhoneNumber: json['user_phone_number'],
      propertyId: json['property_id'],
      propertyName: json['property_name'],
      checkIn: json['check_in'],
      checkOut: json['check_out'],
      roomName: json['room_name'],
      roomId: json['room_id'],
      userEmail: json['user_email'],
      dailyPrice: json['daily_price'],
      monthlyPrice: json['monthly_price'],
      annualPrice: json['annual_price'] != null ? double.tryParse(json['annual_price'].toString()) : null,
      bookingYears: json['booking_years'],
      serviceFee: json['service_fees'],
      propertyType: json['property_type'],
      bookingType: json['booking_type'],
      bookingDays: json['booking_days'],
      bookingMonths: json['booking_months'],
      transactionType: json['transaction_type'],
      voucherCode: json['voucher_code'],
      depositFee: json['deposit_fee'],
      parkingFee: json['parking_fee'],
      parkingType: json['parking_type'],
      parkingDuration: json['parking_duration'],
      isRenewal: json['is_renewal'] ?? 0,
    );
  }
}
