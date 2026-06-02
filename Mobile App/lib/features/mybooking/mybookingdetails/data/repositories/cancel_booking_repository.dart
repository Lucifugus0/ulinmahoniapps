import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/cancel_refund_model.dart';

/// Repository for cancel booking and refund preview API calls.
class CancelBookingRepository {
  static const String _authTokenKey = 'auth_token';

  Future<String?> _getToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString(_authTokenKey);
    } catch (e) {
      AppLogger.e('Error getting token', e, null, 'CANCEL-BOOKING-REPO');
      return null;
    }
  }

  Map<String, String> _headers(String token) => {
        'x-api-key': ApiConfig.apiKey,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };

  /// Fetch refund preview for a booking (without cancelling).
  Future<ApiResult<CancelPreviewResponse>> previewCancelRefund({
    required String orderId,
  }) async {
    try {
      final token = await _getToken();
      if (token == null) {
        return Failure(
          errorType: ApiErrorType.unauthorized,
          message: 'Token tidak ditemukan. Silakan login kembali.',
        );
      }

      final url = Uri.parse('${ApiConfig.baseUrl}/booking/$orderId/cancel-preview');
      AppLogger.d('Cancel preview: $url', 'CANCEL-BOOKING-REPO');

      final response = await http.get(url, headers: _headers(token));
      AppLogger.d('Preview status: ${response.statusCode}', 'CANCEL-BOOKING-REPO');

      if (response.statusCode == 200) {
        final json = jsonDecode(response.body) as Map<String, dynamic>;
        return Success(CancelPreviewResponse.fromJson(json));
      } else {
        final json = jsonDecode(response.body) as Map<String, dynamic>;
        final msg = json['message'] as String? ?? 'Gagal memuat preview refund';
        return Failure(
          errorType: _errorType(response.statusCode),
          message: msg,
          statusCode: response.statusCode,
        );
      }
    } catch (e, st) {
      AppLogger.e('Error fetching cancel preview', e, st, 'CANCEL-BOOKING-REPO');
      return Failure(errorType: ApiErrorType.unknown, message: 'Terjadi kesalahan: $e', originalError: e);
    }
  }

  /// Cancel a booking. For QRIS/VA refunds, provide bank account details.
  Future<ApiResult<CancelBookingResponse>> cancelBooking({
    required String orderId,
    String? reason,
    String? bankName,
    String? accountNo,
    String? accountHolder,
  }) async {
    try {
      final token = await _getToken();
      if (token == null) {
        return Failure(
          errorType: ApiErrorType.unauthorized,
          message: 'Token tidak ditemukan. Silakan login kembali.',
        );
      }

      final url = Uri.parse('${ApiConfig.baseUrl}/booking/$orderId/cancel');
      final body = <String, dynamic>{};
      if (reason != null) body['reason'] = reason;
      if (bankName != null) body['bank_name'] = bankName;
      if (accountNo != null) body['account_no'] = accountNo;
      if (accountHolder != null) body['account_holder'] = accountHolder;

      AppLogger.d('Cancelling booking: $orderId', 'CANCEL-BOOKING-REPO');

      final response = await http.post(url, headers: _headers(token), body: jsonEncode(body));
      // Log full response for debugging — remove once cancel bug is resolved
      AppLogger.d('Cancel status: ${response.statusCode} | body: ${response.body}', 'CANCEL-BOOKING-REPO');

      if (response.statusCode == 200 || response.statusCode == 201) {
        try {
          final json = jsonDecode(response.body) as Map<String, dynamic>;
          final parsed = CancelBookingResponse.fromJson(json);
          AppLogger.d('Cancel parsed OK — orderId: ${parsed.orderId}, status: ${parsed.status}', 'CANCEL-BOOKING-REPO');
          return Success(parsed);
        } catch (parseErr, parseSt) {
          // Parsing failed even though HTTP was 200 — log raw body so we can debug
          AppLogger.e('Cancel parse error (HTTP 200 but JSON parse failed)', parseErr, parseSt, 'CANCEL-BOOKING-REPO');
          return Failure(errorType: ApiErrorType.unknown, message: 'Gagal memproses respons server: $parseErr', originalError: parseErr);
        }
      } else {
        final json = jsonDecode(response.body) as Map<String, dynamic>;
        final msg = json['message'] as String? ?? 'Gagal membatalkan booking';
        AppLogger.w('Cancel rejected — HTTP ${response.statusCode}: $msg', 'CANCEL-BOOKING-REPO');
        return Failure(
          errorType: _errorType(response.statusCode),
          message: msg,
          statusCode: response.statusCode,
        );
      }
    } catch (e, st) {
      AppLogger.e('Error cancelling booking', e, st, 'CANCEL-BOOKING-REPO');
      return Failure(errorType: ApiErrorType.unknown, message: 'Terjadi kesalahan: $e', originalError: e);
    }
  }

  ApiErrorType _errorType(int statusCode) {
    if (statusCode == 400) return ApiErrorType.badRequest;
    if (statusCode == 401) return ApiErrorType.unauthorized;
    if (statusCode == 404) return ApiErrorType.notFound;
    if (statusCode == 422) return ApiErrorType.badRequest;
    if (statusCode >= 500) return ApiErrorType.server;
    return ApiErrorType.unknown;
  }
}
