/// Model for cancel/refund preview data returned by the API.
class CancelRefundPreview {
  final double roomRefund;
  final double depositRefund;
  final double otherRefund;
  final double totalRefund;
  final int daysBeforeCheckin;
  final int refundPercentage;
  final bool requiresBankAccount;

  CancelRefundPreview({
    required this.roomRefund,
    required this.depositRefund,
    required this.otherRefund,
    required this.totalRefund,
    required this.daysBeforeCheckin,
    required this.refundPercentage,
    required this.requiresBankAccount,
  });

  factory CancelRefundPreview.fromJson(Map<String, dynamic> json) {
    return CancelRefundPreview(
      roomRefund: (json['room_refund'] as num?)?.toDouble() ?? 0,
      depositRefund: (json['deposit_refund'] as num?)?.toDouble() ?? 0,
      otherRefund: (json['other_refund'] as num?)?.toDouble() ?? 0,
      totalRefund: (json['total_refund'] as num?)?.toDouble() ?? 0,
      daysBeforeCheckin: (json['days_before_checkin'] as num?)?.toInt() ?? 0,
      refundPercentage: (json['refund_percentage'] as num?)?.toInt() ?? 0,
      requiresBankAccount: json['requires_bank_account'] as bool? ?? false,
    );
  }
}

/// Response wrapper for cancel preview API.
class CancelPreviewResponse {
  final String status;
  final String message;
  final String orderId;
  final String transactionStatus;
  final CancelRefundPreview? refund;

  CancelPreviewResponse({
    required this.status,
    required this.message,
    required this.orderId,
    required this.transactionStatus,
    this.refund,
  });

  factory CancelPreviewResponse.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    return CancelPreviewResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      orderId: data['order_id'] as String? ?? '',
      transactionStatus: data['transaction_status'] as String? ?? '',
      refund: data['refund'] != null
          ? CancelRefundPreview.fromJson(data['refund'] as Map<String, dynamic>)
          : null,
    );
  }
}

/// Response wrapper for cancel booking API.
class CancelBookingResponse {
  final String status;
  final String message;
  final String orderId;
  final CancelRefundPreview? refund;

  CancelBookingResponse({
    required this.status,
    required this.message,
    required this.orderId,
    this.refund,
  });

  factory CancelBookingResponse.fromJson(Map<String, dynamic> json) {
    final data = json['data'] as Map<String, dynamic>? ?? {};
    return CancelBookingResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      orderId: data['order_id'] as String? ?? '',
      refund: data['refund'] != null
          ? CancelRefundPreview.fromJson(data['refund'] as Map<String, dynamic>)
          : null,
    );
  }
}
