

import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:crypto/crypto.dart';
import 'package:intl/intl.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/utils/app_logger.dart';



String generateTimestamp() {
  final now = DateTime.now().toUtc();
  return DateFormat("yyyy-MM-dd'T'HH:mm:ss'Z'").format(now);
}


String generateBodyHash(String jsonBody) {
  var bytes = utf8.encode(jsonBody);
  var digest = sha256.convert(bytes);

  return base64Encode(digest.bytes);
}


String generateDokuSignature({
  required String clientId,
  required String requestId,
  required String timestamp,
  required String bodyHash,
  required String secretKey,
  required String urlPath,
}) {
  final requestTarget = urlPath;

  final stringToSign = [
    'Client-Id:$clientId',
    'Request-Id:$requestId',
    'Request-Timestamp:$timestamp',
    'Request-Target:$requestTarget',
    'Digest:$bodyHash',
  ].join('\n');

  AppLogger.d('String to Sign Lengkap: \n$stringToSign', 'DOKU_SIGNATURE');

  final key = utf8.encode(secretKey);
  final bytesToSign = utf8.encode(stringToSign);

  final hmacSha256 = Hmac(sha256, key);
  final digest = hmacSha256.convert(bytesToSign);

  final base64Signature = base64Encode(digest.bytes);

  return 'HMACSHA256=$base64Signature';
}



class PaymentGatewayService {
  static Future<http.Response> initiatePayment({
    required Map<String, dynamic> paymentData,
  }) async {
    final url = Uri.parse(ApiConfig.dokuPayment);

    final jsonBody = jsonEncode(paymentData);

    final timestamp = generateTimestamp();
    final bodyHash = generateBodyHash(jsonBody);

    final signature = generateDokuSignature(
      clientId: ApiConfig.dokuClientId,
      requestId:ApiConfig.dokuRequestId,
      timestamp: timestamp,
      bodyHash: bodyHash,
      secretKey: ApiConfig.dokuSecretKey,
      urlPath: ApiConfig.dokuPaymentPath,
    );

    AppLogger.d('Client-Id: ${ApiConfig.dokuClientId}', 'DOKU_AUTH');
    AppLogger.d('Request-Id: ${ApiConfig.dokuRequestId}', 'DOKU_AUTH');
    AppLogger.d('Request-Timestamp: $timestamp', 'DOKU_AUTH');
    AppLogger.d('Digest (Base64 Hash): $bodyHash', 'DOKU_AUTH');
    AppLogger.d('Signature: $signature', 'DOKU_AUTH');

    http.Response response;

    try {
      response = await http.post(
        url,
        headers: {
          'Accept': 'application/json',
          'Content-Type': 'application/json',
          'Client-Id': ApiConfig.dokuClientId,
          'Request-Id': ApiConfig.dokuRequestId,
          'Request-Timestamp': timestamp,
          'Signature': signature,
          'Digest': 'SHA-256=$bodyHash',
        },
        body: jsonBody,
      );
    } catch (e) {
      AppLogger.e('Network error or server unreachable', e, StackTrace.current, 'DOKU_PAYMENT');
      throw Exception('Tidak dapat terhubung ke server DOKU: $e');
    }

    AppLogger.api(
      serviceName: 'DokuPayment',
      method: 'POST',
      url: url.toString(),
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Client-Id': ApiConfig.dokuClientId,
        'Request-Id': ApiConfig.dokuRequestId,
        'Request-Timestamp': timestamp,
        'Signature': signature,
        'Digest': 'SHA-256=$bodyHash',
      },
      body: paymentData,
      statusCode: response.statusCode,
      response: response.body,
    );

    final statusCode = response.statusCode;
    dynamic body;
    String errorMessage;

    if (response.body.isNotEmpty) {
      try {
        body = json.decode(response.body);

        if (body is Map<String, dynamic>) {
          errorMessage = body['error']?['message'] ?? body['message'] ?? 'Kesalahan respons objek standar.';
        } else if (body is List<dynamic>) {
          if (body.isNotEmpty && body.first is Map<String, dynamic>) {
            final firstError = body.first as Map<String, dynamic>;
            errorMessage = firstError['error']?['message'] ?? firstError['message'] ?? 'Kesalahan respons array (item pertama).';
          } else {
            errorMessage = 'Respons berupa array kosong atau tidak terformat dengan baik.';
          }
        } else {
          errorMessage = 'Format respons DOKU tidak dikenal.';
        }
      } catch (e) {
        AppLogger.e('Gagal parsing JSON response', e, StackTrace.current, 'DOKU_PAYMENT');
        errorMessage = 'Gagal memproses JSON. Isi respons: ${response.body.substring(0, response.body.length.clamp(0, 100))}...';
      }
    } else {
      errorMessage = 'Server merespons tanpa isi (body kosong).';
    }

    switch (statusCode) {
      case 200:
      case 201:
        AppLogger.i('Payment request successful!', 'DOKU_PAYMENT');
        return response;

      case 400:
        AppLogger.w('Error 400 - Bad Request: $errorMessage', 'DOKU_PAYMENT');
        throw Exception('Permintaan tidak valid (400): $errorMessage');

      case 401:
        AppLogger.w('Error 401 - Unauthorized: $errorMessage', 'DOKU_PAYMENT');
        throw Exception('Otentikasi Gagal (401): Cek Secret Key dan Signature. $errorMessage');



      default:
        AppLogger.w('Unexpected error - ${response.statusCode}: $errorMessage', 'DOKU_PAYMENT');
        throw Exception('Kesalahan tidak terduga (${response.statusCode}): $errorMessage');
    }
  }
}
