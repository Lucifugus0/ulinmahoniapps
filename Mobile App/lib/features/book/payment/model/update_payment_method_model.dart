/// Update Payment Method Models
///
/// Models for updating payment method after payment generation

/// Request model for updating payment method
class UpdatePaymentMethodRequest {
  final String paymentMethod;
  final String? virtualAccountNo;
  final String? paymentBank;
  final String? qrisReferenceNo;
  final String? creditCardInvoice;
  final double? depositFee;
  final double? parkingFee;
  final String? parkingType;
  final int? parkingDuration;
  final String? vehiclePlate;
  final String? ownerName;
  final String? ownerPhone;
  final String? voucherCode;
  final double? discountAmount;

  UpdatePaymentMethodRequest({
    required this.paymentMethod,
    this.virtualAccountNo,
    this.paymentBank,
    this.qrisReferenceNo,
    this.creditCardInvoice,
    this.depositFee,
    this.parkingFee,
    this.parkingType,
    this.parkingDuration,
    this.vehiclePlate,
    this.ownerName,
    this.ownerPhone,
    this.voucherCode,
    this.discountAmount,
  });

  Map<String, dynamic> toJson() {
    return {
      'payment_method': paymentMethod,
      if (virtualAccountNo != null) 'virtual_account_no': virtualAccountNo,
      if (paymentBank != null) 'payment_bank': paymentBank,
      if (qrisReferenceNo != null) 'qris_reference_no': qrisReferenceNo,
      if (creditCardInvoice != null) 'credit_card_invoice': creditCardInvoice,
      if (depositFee != null) 'deposit_fee': depositFee,
      if (parkingFee != null) 'parking_fee': parkingFee,
      if (parkingType != null) 'parking_type': parkingType,
      if (parkingDuration != null) 'parking_duration': parkingDuration,
      if (vehiclePlate != null && vehiclePlate!.isNotEmpty) 'vehicle_plate': vehiclePlate,
      if (ownerName != null && ownerName!.isNotEmpty) 'owner_name': ownerName,
      if (ownerPhone != null && ownerPhone!.isNotEmpty) 'owner_phone': ownerPhone,
      if (voucherCode != null && voucherCode!.isNotEmpty) 'voucher_code': voucherCode,
      if (discountAmount != null) 'discount_amount': discountAmount,
    };
  }
}

/// Response model for update payment method
class UpdatePaymentMethodResponse {
  final String status;
  final String message;
  final UpdatePaymentMethodData? data;

  UpdatePaymentMethodResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory UpdatePaymentMethodResponse.fromJson(Map<String, dynamic> json) {
    return UpdatePaymentMethodResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      data: json['data'] != null
          ? UpdatePaymentMethodData.fromJson(json['data'] as Map<String, dynamic>)
          : null,
    );
  }

  bool get isSuccess => status == 'success';
}

/// Data object in update payment method response
class UpdatePaymentMethodData {
  final String bookingId;
  final String paymentMethod;
  final String transactionStatus;

  UpdatePaymentMethodData({
    required this.bookingId,
    required this.paymentMethod,
    required this.transactionStatus,
  });

  factory UpdatePaymentMethodData.fromJson(Map<String, dynamic> json) {
    return UpdatePaymentMethodData(
      bookingId: json['booking_id']?.toString() ?? '',
      paymentMethod: json['payment_method'] as String? ?? '',
      transactionStatus: json['transaction_status'] as String? ?? '',
    );
  }
}
