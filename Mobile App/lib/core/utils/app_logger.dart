import 'dart:convert';
import 'package:flutter/foundation.dart';

/// Custom Logger untuk Ulin Mahoni Apps
/// Automatically disables debug logs in production mode
class AppLogger {
  // ANSI Color codes for terminal
  static const String _reset = '\x1B[0m';
  static const String _red = '\x1B[31m';
  static const String _green = '\x1B[32m';
  static const String _yellow = '\x1B[33m';
  static const String _blue = '\x1B[34m';
  static const String _magenta = '\x1B[35m';
  static const String _cyan = '\x1B[36m';

  /// Debug log - Only shows in debug mode
  static void d(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toString().substring(11, 23);
      final prefix = tag != null ? '[$tag]' : '';
      print('$_cyan[$timestamp] 🐛 DEBUG $prefix$_reset $message');
    }
  }

  /// Info log - Only shows in debug mode
  static void i(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toString().substring(11, 23);
      final prefix = tag != null ? '[$tag]' : '';
      print('$_blue[$timestamp] ℹ️  INFO $prefix$_reset $message');
    }
  }

  /// Warning log - Shows in both debug and production
  static void w(String message, [String? tag]) {
    final timestamp = DateTime.now().toString().substring(11, 23);
    final prefix = tag != null ? '[$tag]' : '';
    print('$_yellow[$timestamp] ⚠️  WARN $prefix$_reset $message');
  }

  /// Error log - Always shows (debug and production)
  static void e(String message, [dynamic error, StackTrace? stackTrace, String? tag]) {
    final timestamp = DateTime.now().toString().substring(11, 23);
    final prefix = tag != null ? '[$tag]' : '';
    print('$_red[$timestamp] ❌ ERROR $prefix$_reset $message');
    if (error != null) {
      print('$_red  └─ Error: $error$_reset');
    }
    if (stackTrace != null && kDebugMode) {
      print('$_red  └─ StackTrace:\n$stackTrace$_reset');
    }
  }

  /// Success log - Only shows in debug mode
  static void s(String message, [String? tag]) {
    if (kDebugMode) {
      final timestamp = DateTime.now().toString().substring(11, 23);
      final prefix = tag != null ? '[$tag]' : '';
      print('$_green[$timestamp] ✅ SUCCESS $prefix$_reset $message');
    }
  }

  /// API Request/Response logging - Only in debug mode
  static void api({
    required String serviceName,
    required String method,
    required String url,
    Map<String, dynamic>? headers,
    dynamic body,
    int? statusCode,
    dynamic response,
  }) {
    if (!kDebugMode) return;

    final timestamp = DateTime.now().toString().substring(11, 23);
    print('$_magenta[$timestamp] 📡 API [$serviceName]$_reset');
    print('  ├─ Method: $method');
    print('  ├─ URL: $url');

    if (headers != null) {
      final sanitizedHeaders = _sanitizeHeaders(headers);
      print('  ├─ Headers: $sanitizedHeaders');
    }

    if (body != null) {
      final sanitizedBody = _sanitizeBody(body);
      print('  ├─ Request Body: $sanitizedBody');
    }

    if (statusCode != null) {
      final statusColor = statusCode >= 200 && statusCode < 300 ? _green : _red;
      print('  ├─ ${statusColor}Status Code: $statusCode$_reset');
    }

    if (response != null) {
      final sanitizedResponse = _sanitizeBody(response);
      // Limit response size
      final responseStr = sanitizedResponse.toString();
      final displayResponse = responseStr.length > 500
          ? '${responseStr.substring(0, 500)}... [truncated]'
          : responseStr;
      print('  └─ Response: $displayResponse');
    }
  }

  /// Sanitize headers to hide sensitive data
  static Map<String, dynamic> _sanitizeHeaders(Map<String, dynamic> headers) {
    final sanitized = Map<String, dynamic>.from(headers);

    // Hide API keys
    if (sanitized.containsKey('x-api-key')) {
      final key = sanitized['x-api-key'].toString();
      sanitized['x-api-key'] = key.length > 10
          ? '${key.substring(0, 10)}...***'
          : '***';
    }

    // Hide Authorization tokens
    if (sanitized.containsKey('Authorization')) {
      sanitized['Authorization'] = 'Bearer ***';
    }

    return sanitized;
  }

  /// Sanitize body to hide sensitive data
  static dynamic _sanitizeBody(dynamic body) {
    if (body == null) return null;

    try {
      String bodyStr;

      // Convert to string if needed
      if (body is Map || body is List) {
        bodyStr = jsonEncode(body);
      } else {
        bodyStr = body.toString();
      }

      // Hide password fields
      bodyStr = bodyStr.replaceAllMapped(
        RegExp(r'"password"\s*:\s*"[^"]*"', caseSensitive: false),
        (match) => '"password":"***"',
      );

      // Hide token fields
      bodyStr = bodyStr.replaceAllMapped(
        RegExp(r'"token"\s*:\s*"[^"]*"', caseSensitive: false),
        (match) => '"token":"***"',
      );

      // Hide api_key fields
      bodyStr = bodyStr.replaceAllMapped(
        RegExp(r'"api_key"\s*:\s*"[^"]*"', caseSensitive: false),
        (match) => '"api_key":"***"',
      );

      return bodyStr;
    } catch (e) {
      return body.toString();
    }
  }

  /// Print separator line
  static void separator([String? label]) {
    if (kDebugMode) {
      if (label != null) {
        print('$_cyan════════ $label ════════$_reset');
      } else {
        print('$_cyan═══════════════════════════════════$_reset');
      }
    }
  }
}
