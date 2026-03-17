import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/api_constants.dart';
import '../../model/renew_booking_model.dart';

class RenewBookingRepository {
  static const String _authTokenKey = 'auth_token';

  Future<String?> _getToken() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      return prefs.getString(_authTokenKey);
    } catch (e) {
      AppLogger.e('Error getting token', e, null, 'RENEW-BOOKING-REPO');
      return null;
    }
  }

  Future<ApiResult<RenewBookingResponse>> renewBooking({
    required String orderId,
    required int userId,
    required String userName,
    required String userPhoneNumber,
    required String userEmail,
    required int propertyId,
    required String propertyName,
    required String propertyType,
    required int roomId,
    required String roomName,
    required String bookingType,
    required String checkIn,
    required String checkOut,
    double? monthlyPrice,
    int? bookingMonths,
    double? adminFees,
    required double serviceFees,
    required String transactionType,
    String? voucherCode,
    double? parkingFee,
    String? parkingType,
    int? parkingDuration,
  }) async {
    try {
      final token = await _getToken();

      if (token == null) {
        AppLogger.e('Token not found', null, null, 'RENEW-BOOKING-REPO');
        return Failure(
          errorType: ApiErrorType.unauthorized,
          message: 'Token tidak ditemukan. Silakan login kembali.',
        );
      }

      final request = RenewBookingRequest(
        userId: userId,
        userName: userName,
        userPhoneNumber: userPhoneNumber,
        userEmail: userEmail,
        propertyId: propertyId,
        propertyName: propertyName,
        propertyType: propertyType,
        roomId: roomId,
        roomName: roomName,
        bookingType: bookingType,
        checkIn: checkIn,
        checkOut: checkOut,
        monthlyPrice: monthlyPrice,
        bookingMonths: bookingMonths,
        adminFees: adminFees,
        serviceFees: serviceFees,
        transactionType: transactionType,
        voucherCode: voucherCode,
        parkingFee: parkingFee,
        parkingType: parkingType,
        parkingDuration: parkingDuration,
      );

      final url = Uri.parse('${ApiConfig.baseUrl}/booking/$orderId/renew');

      AppLogger.d('Renewing booking: $orderId', 'RENEW-BOOKING-REPO');
      AppLogger.d('Request URL: $url', 'RENEW-BOOKING-REPO');
      AppLogger.d('Request Body: ${jsonEncode(request.toJson())}', 'RENEW-BOOKING-REPO');
      AppLogger.d('API Key present: ${ApiConfig.apiKey.isNotEmpty}', 'RENEW-BOOKING-REPO');
      AppLogger.d('Token present: ${token.isNotEmpty}', 'RENEW-BOOKING-REPO');

      final headers = {
        'x-api-key': ApiConfig.apiKey,
        'Content-Type': 'application/json',
        'Accept': 'application/json',
        'Authorization': 'Bearer $token',
      };

      AppLogger.d('Request Headers: ${headers.keys.join(", ")}', 'RENEW-BOOKING-REPO');

      final response = await http.post(
        url,
        headers: headers,
        body: jsonEncode(request.toJson()),
      );

      AppLogger.d('Response Status: ${response.statusCode}', 'RENEW-BOOKING-REPO');
      AppLogger.d('Response Body: ${response.body}', 'RENEW-BOOKING-REPO');

      if (response.statusCode == 200 || response.statusCode == 201) {
        final Map<String, dynamic> jsonResponse = jsonDecode(response.body);
        final renewResponse = RenewBookingResponse.fromJson(jsonResponse);

        AppLogger.i('Booking renewed successfully: ${renewResponse.data?.orderId}', 'RENEW-BOOKING-REPO');
        return Success(renewResponse);
      } else {
        final Map<String, dynamic> errorResponse = jsonDecode(response.body);
        final errorMessage = errorResponse['message'] as String? ?? 'Gagal memperpanjang booking';

        AppLogger.e('Renew booking failed', errorMessage, null, 'RENEW-BOOKING-REPO');

        // Determine error type based on status code
        ApiErrorType errorType = ApiErrorType.unknown;
        if (response.statusCode == 400) {
          errorType = ApiErrorType.badRequest;
        } else if (response.statusCode == 404) {
          errorType = ApiErrorType.notFound;
        } else if (response.statusCode >= 500) {
          errorType = ApiErrorType.server;
        }

        return Failure(
          errorType: errorType,
          message: errorMessage,
          statusCode: response.statusCode,
        );
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error renewing booking', e, stackTrace, 'RENEW-BOOKING-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Terjadi kesalahan: $e',
        originalError: e,
      );
    }
  }
}
