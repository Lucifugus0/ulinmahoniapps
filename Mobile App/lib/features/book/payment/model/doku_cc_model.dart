/// DOKU Credit Card Models
///
/// Models for DOKU CC payment generation request and response

/// Request model for generating DOKU Credit Card payment
class DokuCCRequest {
  final String orderId;
  final String userName;
  final String userEmail;
  final String userPhone;
  final double amount;

  DokuCCRequest({
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

/// Credit Card Payment Data nested in response
class DokuCCData {
  final bool success;
  final String invoiceNumber;
  final String orderId;
  final String paymentUrl;
  final double amount;
  final Map<String, dynamic>? rawResponse;

  DokuCCData({
    required this.success,
    required this.invoiceNumber,
    required this.orderId,
    required this.paymentUrl,
    required this.amount,
    this.rawResponse,
  });

  factory DokuCCData.fromJson(Map<String, dynamic> json) {
    return DokuCCData(
      success: json['success'] as bool? ?? false,
      invoiceNumber: (json['invoice_number']?.toString()) ?? '',
      orderId: (json['order_id']?.toString()) ?? '',
      paymentUrl: json['payment_url'] as String? ?? '',
      amount: (json['amount'] as num?)?.toDouble() ?? 0.0,
      rawResponse: json['raw_response'] as Map<String, dynamic>?,
    );
  }
}

/// Response model for DOKU Credit Card payment generation
class DokuCCResponse {
  final String status;
  final String message;
  final DokuCCData? data;

  DokuCCResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory DokuCCResponse.fromJson(Map<String, dynamic> json) {
    return DokuCCResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      data: json['data'] != null
          ? DokuCCData.fromJson(json['data'] as Map<String, dynamic>)
          : null,
    );
  }

  bool get isSuccess => status == 'success' && data?.success == true;
}
