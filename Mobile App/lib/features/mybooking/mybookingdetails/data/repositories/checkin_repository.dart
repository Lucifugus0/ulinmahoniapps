import 'dart:convert';
import 'package:http/http.dart' as http;
import '../../../../../core/constants/api_constants.dart';
import '../../../../../core/utils/app_logger.dart';

class CheckInRepository {
  /// Check-in booking with ID card image
  ///
  /// Parameters:
  /// - orderId: The order ID of the booking
  /// - idCardBase64: Base64 encoded ID card image
  ///
  /// Returns a Map containing the response from the server
  Future<Map<String, dynamic>> checkIn({
    required String orderId,
    required String idCardBase64,
  }) async {
    final url = Uri.parse(
      ApiConfig.checkIn.replaceFirst('{orderid}', orderId),
    );

    final requestBody = json.encode({
      'doc_type': 'ktp',
      'document': idCardBase64,
    });

    AppLogger.i(
      "Requesting check-in for order: $orderId, URL: $url",
      "CHECK-IN",
    );
    AppLogger.d(
      "Request body (partial): ${requestBody.substring(0, requestBody.length > 100 ? 100 : requestBody.length)}...",
      "CHECK-IN",
    );

    http.Response response;

    try {
      response = await http.post(
        url,
        headers: {
          'x-api-key': ApiConfig.apiKey,
          'Content-Type': 'application/json',
          'Accept': 'application/json',
          'Cache-Control': 'no-cache',
        },
        body: requestBody,
      );
    } catch (e) {
      AppLogger.e(
        'Network error or server unreachable for order: $orderId',
        e,
        null,
        "CHECK-IN",
      );
      throw Exception('Tidak dapat terhubung ke server untuk check-in: $e');
    }

    AppLogger.api(
      serviceName: 'CHECK-IN',
      method: 'POST',
      url: url.toString(),
      headers: {
        'x-api-key': ApiConfig.apiKey,
        'Content-Type': 'application/json'
      },
      body: '[ID Card Base64 Data - Hidden]',
      statusCode: response.statusCode,
      response: response.body,
    );

    final statusCode = response.statusCode;
    dynamic body;

    if (response.body.isNotEmpty) {
      try {
        body = json.decode(response.body);
      } catch (e) {
        AppLogger.e(
          'Failed to parse JSON response for order: $orderId',
          e,
          null,
          "CHECK-IN",
        );
        if (statusCode >= 200 && statusCode < 300) {
          throw Exception('Format respons server tidak valid untuk check-in.');
        }
      }
    }

    switch (statusCode) {
      case 200:
      case 201:
        if (body is Map) {
          AppLogger.s(
            "Successfully checked in for order: $orderId",
            "CHECK-IN",
          );
          return body as Map<String, dynamic>;
        } else {
          AppLogger.w(
            'Success but invalid data format for order: $orderId',
            "CHECK-IN",
          );
          throw Exception('Check-in berhasil, namun format data tidak valid.');
        }

      case 400:
        AppLogger.e(
          'Error 400 - Bad Request for order: $orderId - ${body?['message']}',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception(body?['message'] ?? 'Permintaan tidak valid (400)');

      case 401:
        AppLogger.e(
          'Error 401 - Unauthorized for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception('Sesi Anda telah berakhir. Mohon login ulang (401).');

      case 403:
        AppLogger.e(
          'Error 403 - Forbidden for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception('Akses ditolak (403).');

      case 404:
        AppLogger.e(
          'Error 404 - Not Found for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception(
            'Booking tidak ditemukan atau endpoint API salah (404).');

      case 422:
        final errors = body?['errors'];
        if (errors != null && errors is Map) {
          final errorMessages = errors.values
              .map((e) => (e is List && e.isNotEmpty) ? e[0] : e.toString())
              .join(', ');
          AppLogger.e(
            'Error 422 - Validation Failed for order: $orderId - $errorMessages',
            null,
            null,
            "CHECK-IN",
          );
          throw Exception(errorMessages);
        }
        AppLogger.e(
          'Error 422 - Unprocessable Entity for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception(
            body?['message'] ?? 'Data yang dikirim tidak valid (422).');

      case 500:
        AppLogger.e(
          'Error 500 - Internal Server Error for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception('Terjadi kesalahan server internal (500).');

      default:
        AppLogger.e(
          'Unexpected Error - Status: $statusCode for order: $orderId',
          null,
          null,
          "CHECK-IN",
        );
        throw Exception(
            'Terjadi kesalahan tidak terduga (${statusCode}): ${body?['message'] ?? 'Tidak diketahui'}.');
    }
  }
}
