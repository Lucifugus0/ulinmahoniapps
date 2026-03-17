/// DOKU Virtual Account Models
///
/// Models for DOKU VA generation request and response

/// Request model for generating DOKU Virtual Account
class DokuVARequest {
  final String orderId;
  final String userName;
  final String userEmail;
  final String userPhone;
  final double amount;
  final String bank;

  DokuVARequest({
    required this.orderId,
    required this.userName,
    required this.userEmail,
    required this.userPhone,
    required this.amount,
    required this.bank,
  });

  Map<String, dynamic> toJson() {
    return {
      'order_id': orderId,
      'user_name': userName,
      'user_email': userEmail,
      'user_phone': userPhone,
      'amount': amount,
      'bank': bank,
    };
  }
}

/// Virtual Account Data nested in response
class VirtualAccountData {
  final String partnerServiceId;
  final String customerNo;
  final String virtualAccountNo;
  final String virtualAccountName;
  final String virtualAccountEmail;
  final String virtualAccountPhone;
  final String trxId;
  final TotalAmount totalAmount;
  final String virtualAccountTrxType;
  final String expiredDate;
  final AdditionalInfo additionalInfo;

  VirtualAccountData({
    required this.partnerServiceId,
    required this.customerNo,
    required this.virtualAccountNo,
    required this.virtualAccountName,
    required this.virtualAccountEmail,
    required this.virtualAccountPhone,
    required this.trxId,
    required this.totalAmount,
    required this.virtualAccountTrxType,
    required this.expiredDate,
    required this.additionalInfo,
  });

  factory VirtualAccountData.fromJson(Map<String, dynamic> json) {
    return VirtualAccountData(
      partnerServiceId: json['partnerServiceId'] as String? ?? '',
      customerNo: json['customerNo'] as String? ?? '',
      virtualAccountNo: json['virtualAccountNo'] as String? ?? '',
      virtualAccountName: json['virtualAccountName'] as String? ?? '',
      virtualAccountEmail: json['virtualAccountEmail'] as String? ?? '',
      virtualAccountPhone: json['virtualAccountPhone'] as String? ?? '',
      trxId: json['trxId'] as String? ?? '',
      totalAmount: TotalAmount.fromJson(json['totalAmount'] as Map<String, dynamic>? ?? {}),
      virtualAccountTrxType: json['virtualAccountTrxType'] as String? ?? '',
      expiredDate: json['expiredDate'] as String? ?? '',
      additionalInfo: AdditionalInfo.fromJson(json['additionalInfo'] as Map<String, dynamic>? ?? {}),
    );
  }
}

/// Total Amount nested in VirtualAccountData
class TotalAmount {
  final String value;
  final String currency;

  TotalAmount({
    required this.value,
    required this.currency,
  });

  factory TotalAmount.fromJson(Map<String, dynamic> json) {
    return TotalAmount(
      value: json['value'] as String? ?? '0',
      currency: json['currency'] as String? ?? 'IDR',
    );
  }
}

/// Additional Info nested in VirtualAccountData
class AdditionalInfo {
  final String howToPayPage;
  final String howToPayApi;

  AdditionalInfo({
    required this.howToPayPage,
    required this.howToPayApi,
  });

  factory AdditionalInfo.fromJson(Map<String, dynamic> json) {
    return AdditionalInfo(
      howToPayPage: json['howToPayPage'] as String? ?? '',
      howToPayApi: json['howToPayApi'] as String? ?? '',
    );
  }
}

/// Full response nested in data
class FullResponse {
  final String responseCode;
  final String responseMessage;
  final VirtualAccountData virtualAccountData;

  FullResponse({
    required this.responseCode,
    required this.responseMessage,
    required this.virtualAccountData,
  });

  factory FullResponse.fromJson(Map<String, dynamic> json) {
    return FullResponse(
      responseCode: json['responseCode'] as String? ?? '',
      responseMessage: json['responseMessage'] as String? ?? '',
      virtualAccountData: VirtualAccountData.fromJson(
        json['virtualAccountData'] as Map<String, dynamic>? ?? {},
      ),
    );
  }
}

/// Main Data object in response
class DokuVAData {
  final bool success;
  final String bank;
  final String channel;
  final String partnerServiceId;
  final String virtualAccountNo;
  final String virtualAccountName;
  final String totalAmount;
  final String expiredDate;
  final String howToPayPage;
  final String howToPayApi;
  final String responseCode;
  final String responseMessage;
  final FullResponse fullResponse;

  DokuVAData({
    required this.success,
    required this.bank,
    required this.channel,
    required this.partnerServiceId,
    required this.virtualAccountNo,
    required this.virtualAccountName,
    required this.totalAmount,
    required this.expiredDate,
    required this.howToPayPage,
    required this.howToPayApi,
    required this.responseCode,
    required this.responseMessage,
    required this.fullResponse,
  });

  factory DokuVAData.fromJson(Map<String, dynamic> json) {
    return DokuVAData(
      success: json['success'] as bool? ?? false,
      bank: json['bank'] as String? ?? '',
      channel: json['channel'] as String? ?? '',
      partnerServiceId: json['partner_service_id'] as String? ?? '',
      virtualAccountNo: json['virtual_account_no'] as String? ?? '',
      virtualAccountName: json['virtual_account_name'] as String? ?? '',
      totalAmount: json['total_amount'] as String? ?? '0',
      expiredDate: json['expired_date'] as String? ?? '',
      howToPayPage: json['how_to_pay_page'] as String? ?? '',
      howToPayApi: json['how_to_pay_api'] as String? ?? '',
      responseCode: json['response_code'] as String? ?? '',
      responseMessage: json['response_message'] as String? ?? '',
      fullResponse: FullResponse.fromJson(
        json['full_response'] as Map<String, dynamic>? ?? {},
      ),
    );
  }
}

/// Response model for DOKU Virtual Account generation
class DokuVAResponse {
  final String status;
  final String message;
  final DokuVAData? data;

  DokuVAResponse({
    required this.status,
    required this.message,
    this.data,
  });

  factory DokuVAResponse.fromJson(Map<String, dynamic> json) {
    return DokuVAResponse(
      status: json['status'] as String? ?? '',
      message: json['message'] as String? ?? '',
      data: json['data'] != null
          ? DokuVAData.fromJson(json['data'] as Map<String, dynamic>)
          : null,
    );
  }

  bool get isSuccess => status == 'success' && data?.success == true;
}
