/// Voucher Models for validation and application
///
/// Handles voucher validation requests and responses

/// Request model for voucher validation
class VoucherValidationRequest {
  final String voucherCode;
  final int userId;
  final double transactionAmount;
  final int propertyId;
  final int roomId;

  VoucherValidationRequest({
    required this.voucherCode,
    required this.userId,
    required this.transactionAmount,
    required this.propertyId,
    required this.roomId,
  });

  Map<String, dynamic> toJson() {
    return {
      'voucher_code': voucherCode,
      'user_id': userId,
      'transaction_amount': transactionAmount,
      'property_id': propertyId,
      'room_id': roomId,
    };
  }
}

/// Voucher details from validation response
class VoucherDetails {
  final String? voucherCode;
  final String name;
  final String description;
  final String discountPercentage;
  final String maxDiscountAmount;

  VoucherDetails({
    this.voucherCode,
    required this.name,
    required this.description,
    required this.discountPercentage,
    required this.maxDiscountAmount,
  });

  factory VoucherDetails.fromJson(Map<String, dynamic> json) {
    return VoucherDetails(
      voucherCode: json['voucher_code'],
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      discountPercentage: json['discount_percentage']?.toString() ?? '0',
      maxDiscountAmount: json['max_discount_amount']?.toString() ?? '0',
    );
  }

  double get discountPercentageValue {
    return double.tryParse(discountPercentage) ?? 0.0;
  }

  double get maxDiscountAmountValue {
    return double.tryParse(maxDiscountAmount) ?? 0.0;
  }
}

/// Calculation details from validation response
class VoucherCalculation {
  final double originalAmount;
  final double discountAmount;
  final double finalAmount;
  final String discountPercentage;
  final String maxDiscountCap;

  VoucherCalculation({
    required this.originalAmount,
    required this.discountAmount,
    required this.finalAmount,
    required this.discountPercentage,
    required this.maxDiscountCap,
  });

  factory VoucherCalculation.fromJson(Map<String, dynamic> json) {
    return VoucherCalculation(
      originalAmount: _parseToDouble(json['original_amount']),
      discountAmount: _parseToDouble(json['discount_amount']),
      finalAmount: _parseToDouble(json['final_amount']),
      discountPercentage: json['discount_percentage']?.toString() ?? '0',
      maxDiscountCap: json['max_discount_cap']?.toString() ?? '0',
    );
  }

  static double _parseToDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    return double.tryParse(value.toString()) ?? 0.0;
  }
}

/// Eligibility details from validation response
class VoucherEligibility {
  final bool eligible;
  final int usageCount;
  final int maxAllowed;
  final String message;

  VoucherEligibility({
    required this.eligible,
    required this.usageCount,
    required this.maxAllowed,
    required this.message,
  });

  factory VoucherEligibility.fromJson(Map<String, dynamic> json) {
    return VoucherEligibility(
      eligible: json['eligible'] ?? false,
      usageCount: json['usage_count'] ?? 0,
      maxAllowed: json['max_allowed'] ?? 0,
      message: json['message'] ?? '',
    );
  }

  bool get canUse => eligible && usageCount < maxAllowed;
}

/// Complete voucher validation response
class VoucherValidationResponse {
  final String status;
  final String message;
  final VoucherDetails voucher;
  final VoucherCalculation calculation;
  final VoucherEligibility eligibility;

  VoucherValidationResponse({
    required this.status,
    required this.message,
    required this.voucher,
    required this.calculation,
    required this.eligibility,
  });

  factory VoucherValidationResponse.fromJson(Map<String, dynamic> json) {
    return VoucherValidationResponse(
      status: json['status'] ?? 'error',
      message: json['message'] ?? '',
      voucher: VoucherDetails.fromJson(json['data']?['voucher'] ?? {}),
      calculation: VoucherCalculation.fromJson(json['data']?['calculation'] ?? {}),
      eligibility: VoucherEligibility.fromJson(json['data']?['eligibility'] ?? {}),
    );
  }

  bool get isValid => status == 'success' && eligibility.eligible;
}

/// Request model for applying voucher
class VoucherApplicationRequest {
  final String voucherCode;
  final int userId;
  final double transactionAmount;
  final int propertyId;
  final int roomId;

  VoucherApplicationRequest({
    required this.voucherCode,
    required this.userId,
    required this.transactionAmount,
    required this.propertyId,
    required this.roomId,
  });

  Map<String, dynamic> toJson() {
    return {
      'voucher_code': voucherCode,
      'user_id': userId,
      'transaction_amount': transactionAmount,
      'property_id': propertyId,
      'room_id': roomId,
    };
  }
}

/// Applied voucher data from application response
class AppliedVoucherData {
  final int voucherId;
  final String voucherCode;
  final String voucherName;
  final double originalAmount;
  final double discountAmount;
  final double finalAmount;
  final String discountPercentage;
  final String maxDiscountCap;

  AppliedVoucherData({
    required this.voucherId,
    required this.voucherCode,
    required this.voucherName,
    required this.originalAmount,
    required this.discountAmount,
    required this.finalAmount,
    required this.discountPercentage,
    required this.maxDiscountCap,
  });

  factory AppliedVoucherData.fromJson(Map<String, dynamic> json) {
    return AppliedVoucherData(
      voucherId: json['voucher_id'] ?? 0,
      voucherCode: json['voucher_code'] ?? '',
      voucherName: json['voucher_name'] ?? '',
      originalAmount: _parseToDouble(json['original_amount']),
      discountAmount: _parseToDouble(json['discount_amount']),
      finalAmount: _parseToDouble(json['final_amount']),
      discountPercentage: json['discount_percentage']?.toString() ?? '0',
      maxDiscountCap: json['max_discount_cap']?.toString() ?? '0',
    );
  }

  static double _parseToDouble(dynamic value) {
    if (value == null) return 0.0;
    if (value is double) return value;
    if (value is int) return value.toDouble();
    return double.tryParse(value.toString()) ?? 0.0;
  }

  double get discountPercentageValue {
    return double.tryParse(discountPercentage) ?? 0.0;
  }

  double get maxDiscountCapValue {
    return double.tryParse(maxDiscountCap) ?? 0.0;
  }
}

/// Response model for voucher application
class VoucherApplicationResponse {
  final String status;
  final String message;
  final AppliedVoucherData? data;

  VoucherApplicationResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory VoucherApplicationResponse.fromJson(Map<String, dynamic> json) {
    final dataJson = json['data'];
    return VoucherApplicationResponse(
      status: json['status'] ?? 'error',
      message: json['message'] ?? '',
      data: dataJson != null ? AppliedVoucherData.fromJson(dataJson) : null,
    );
  }

  bool get isSuccess => status == 'success';

  // Convenience getters
  String? get voucherCode => data?.voucherCode;
  double? get discountApplied => data?.discountAmount;
  double? get finalAmount => data?.finalAmount;
  String? get voucherName => data?.voucherName;
}
