/// DOKU QRIS Models
///
/// Models for DOKU QRIS generation request and response

/// Request model for generating DOKU QRIS
class DokuQRISRequest {
  final String orderId;
  final String userName;
  final String userEmail;
  final String userPhone;
  final double amount;

  DokuQRISRequest({
    required this.orderId,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    required this.amount,
  });

  Map<String, dynamic> toJson() {
    return {
      'order_id': orderId,
      'customer_name': userName,
      'customer_email': userEmail,
      'customer_phone': userPhone,
      'amount': amount,
    };
  }
}

/// QRIS Data nested in response
class DokuQRISData {
  final bool success;
  final String referenceNo;
  final String partnerReferenceNo;
  final String qrContent;
  final String terminalId;
  final String validityPeriod;
  final String responseCode;
  final String responseMessage;
  final Map<String, dynamic>? rawResponse;

  DokuQRISData({
    required this.success,
    required this.referenceNo,
    required this.partnerReferenceNo,
    required this.qrContent,
    required this.terminalId,
    required this.validityPeriod,
    required this.responseCode,
    required this.responseMessage,
    this.rawResponse,
  });

  factory DokuQRISData.fromJson(Map<String, dynamic> json) {
    return DokuQRISData(
      success: json['success'] as bool? ?? false,
      referenceNo: json['reference_no'] as String? ?? '',
      partnerReferenceNo: json['partner_reference_no'] as String? ?? '',
      qrContent: json['qr_content'] as String? ?? '',
      terminalId: json['terminal_id'] as String? ?? '',
      validityPeriod: json['validity_period'] as String? ?? '',
      responseCode: json['response_code'] as String? ?? '',
      responseMessage: json['response_message'] as String? ?? '',
      rawResponse: json['raw_response'] as Map<String, dynamic>?,
    );
  }
}

/// Response model for DOKU QRIS generation
class DokuQRISResponse {
  final String status;
  final String message;
  final DokuQRISData? data;

  DokuQRISResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory DokuQRISResponse.fromJson(Map<String, dynamic> json) {
    return DokuQRISResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      data: json['data'] != null
          ? DokuQRISData.fromJson(json['data'] as Map<String, dynamic>)
          : null,
    );
  }

  bool get isSuccess => status == 'success' && data?.success == true;
}
