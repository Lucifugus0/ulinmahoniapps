import 'package:flutter/foundation.dart';

class MyBookingModel {
  final int idrec;
  final int propertyId;
  final int roomId;
  final String orderId;
  final int userId;
  final String userName;
  final String userEmail;
  final String? userPhoneNumber;
  final String propertyName;
  final String? propertyType;
  final String? roomName;
  final String? roomNo;
  final String? bookingType;
  final int? bookingDays;
  final double? dailyPrice;
  final int? bookingMonths;
  final double? monthlyPrice;
  final String? checkIn;
  final String? checkOut;
  final double roomPrice;
  final double serviceFees;
  final double grandtotalPrice;
  final String? transactionDate;
  final String transactionType;
  final String transactionCode;
  final String transactionStatus;
  final int status;
  final String? paidAt;
  final String? attachment;
  final String? checked_in_at;
  final int? voucherId;
  final String? voucherCode;
  final double? discountAmount;
  final double? subtotalBeforeDiscount;
  final String? virtualaccountnumber;
  final String? virtualaccountbank;
  final double? depositFee;
  final double? parkingFee;
  final String? parkingType;
  final int? parkingDuration;
  final int? renewalStatus;
  final int? originalCheckinDay;

  MyBookingModel({
    required this.idrec,
    required this.propertyId,
    required this.roomId,
    required this.orderId,
    required this.userId,
    required this.userName,
    required this.userEmail,
    this.userPhoneNumber,
    required this.propertyName,
    this.propertyType,
    this.roomName,
    this.roomNo,
    this.bookingType,
    this.bookingDays,
    this.dailyPrice,
    this.bookingMonths,
    this.monthlyPrice,
    required this.checkIn,
    required this.checkOut,
    required this.roomPrice,
    required this.serviceFees,
    required this.grandtotalPrice,
    this.transactionDate,
    required this.transactionType,
    required this.transactionCode,
    required this.transactionStatus,
    required this.status,
    this.paidAt,
    this.attachment,
    this.checked_in_at,
    this.voucherId,
    this.voucherCode,
    this.discountAmount,
    this.subtotalBeforeDiscount,
    this.virtualaccountnumber,
    this.virtualaccountbank,
    this.depositFee,
    this.parkingFee,
    this.parkingType,
    this.parkingDuration,
    this.renewalStatus,
    this.originalCheckinDay,
  });

  factory MyBookingModel.fromJson(Map<String, dynamic> json) {
    debugPrint('Parsing MyBookingModel from JSON: $json');
    debugPrint('Virtual Account No: ${json['virtual_account_no']}');
    debugPrint('Payment Bank: ${json['payment_bank']}');

    int? _parseInt(dynamic value) {
      if (value == null) return null;
      if (value is int) return value;
      if (value is String) return int.tryParse(value);
      if (value is double) return value.toInt();
      return null;
    }

    double? _parseDouble(dynamic value) {
      if (value == null) return null;
      if (value is double) return value;
      if (value is int) return value.toDouble();
      if (value is String) return double.tryParse(value);
      return null;
    }

    return MyBookingModel(
      idrec: _parseInt(json['idrec']) ?? 0,
      propertyId: _parseInt(json['property_id']) ?? 0,
      roomId: _parseInt(json['room_id']) ?? 0,
      orderId: json['order_id'] as String? ?? '',
      userId: _parseInt(json['user_id']) ?? 0,
      userName: json['user_name'] as String? ?? '',
      userEmail: json['user_email'] as String? ?? '',
      userPhoneNumber: json['user_phone_number'] as String?,
      propertyName: json['property_name'] as String? ?? '',
      propertyType: json['property_type'] as String?,
      roomName: json['room_name'] as String?,
      roomNo: json['room_no'] as String?,
      bookingType: json['booking_type'] as String?,
      bookingDays: _parseInt(json['booking_days']),
      dailyPrice: _parseDouble(json['daily_price']),
      bookingMonths: _parseInt(json['booking_months']),
      monthlyPrice: _parseDouble(json['monthly_price']),
      checkIn: json['check_in'] as String? ?? '',
      checkOut: json['check_out'] as String? ?? '',
      roomPrice: _parseDouble(json['room_price']) ?? 0.0,
      serviceFees: _parseDouble(json['service_fees']) ?? 0.0,
      grandtotalPrice: _parseDouble(json['grandtotal_price']) ?? 0.0,
      transactionDate: json['transaction_date'] as String?,
      transactionType: json['transaction_type'] as String? ?? '',
      transactionCode: json['transaction_code'] as String? ?? '',
      transactionStatus: json['transaction_status'] as String? ?? '',
      status: _parseInt(json['status']) ?? 0,
      paidAt: json['paid_at'] as String?,
      attachment: json['attachment'] as String?,
      checked_in_at: json['check_in_at'] as String?,
      voucherId: _parseInt(json['voucher_id']),
      voucherCode: json['voucher_code'] as String?,
      discountAmount: _parseDouble(json['discount_amount']),
      subtotalBeforeDiscount: _parseDouble(json['subtotal_before_discount']),
      virtualaccountnumber: json['virtual_account_no'] as String?,
      virtualaccountbank: json['payment_bank'] as String?,
      depositFee: _parseDouble(json['deposit_fee']),
      parkingFee: _parseDouble(json['parking_fee']),
      parkingType: json['parking_type'] as String?,
      parkingDuration: _parseInt(json['parking_duration']),
      renewalStatus: _parseInt(json['renewal_status']),
      originalCheckinDay: _parseInt(json['original_checkin_day']),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'idrec': idrec,
      'property_id': propertyId,
      'room_id': roomId,
      'order_id': orderId,
      'user_id': userId,
      'user_name': userName,
      'user_email': userEmail,
      'user_phone_number': userPhoneNumber,
      'property_name': propertyName,
      'property_type': propertyType,
      'room_name': roomName,
      'room_no': roomNo,
      'booking_type': bookingType,
      'booking_days': bookingDays,
      'daily_price': dailyPrice,
      'booking_months': bookingMonths,
      'monthly_price': monthlyPrice,
      'check_in': checkIn,
      'check_out': checkOut,
      'room_price': roomPrice,
      'service_fees': serviceFees,
      'grandtotal_price': grandtotalPrice,
      'transaction_date': transactionDate,
      'transaction_type': transactionType,
      'transaction_code': transactionCode,
      'transaction_status': transactionStatus,
      'status': status,
      'paid_at': paidAt,
      'attachment': attachment,
      'voucher_id': voucherId,
      'voucher_code': voucherCode,
      'discount_amount': discountAmount,
      'subtotal_before_discount': subtotalBeforeDiscount,
      'virtualaccountnumber': virtualaccountnumber,
      'virtualaccountbank': virtualaccountbank,
      'deposit_fee': depositFee,
      'parking_fee': parkingFee,
      'parking_type': parkingType,
      'parking_duration': parkingDuration,
      'renewal_status': renewalStatus,
    };
  }
}
