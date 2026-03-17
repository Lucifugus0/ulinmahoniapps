import 'package:dio/dio.dart';
import '../../utils/app_logger.dart';

/// Dio interceptor for automatic request/response logging
/// Uses AppLogger for production-safe logging
class LoggingInterceptor extends Interceptor {
  @override
  void onRequest(RequestOptions options, RequestInterceptorHandler handler) {
    AppLogger.separator('API REQUEST');
    AppLogger.i('${options.method} ${options.uri}', 'DIO');

    if (options.headers.isNotEmpty) {
      AppLogger.d('Headers: ${_sanitizeHeaders(options.headers)}', 'DIO');
    }

    if (options.data != null) {
      final data = options.data;
      if (data is FormData) {
        AppLogger.d('Body: [FormData with ${data.fields.length} fields]', 'DIO');
      } else {
        AppLogger.d('Body: $data', 'DIO');
      }
    }

    handler.next(options);
  }

  @override
  void onResponse(Response response, ResponseInterceptorHandler handler) {
    AppLogger.separator('API RESPONSE');
    AppLogger.s('${response.requestOptions.method} ${response.requestOptions.uri}', 'DIO');
    AppLogger.i('Status: ${response.statusCode}', 'DIO');

    if (response.data != null) {
      final dataStr = response.data.toString();
      if (dataStr.length > 1000) {
        AppLogger.d('Response: ${dataStr.substring(0, 1000)}... (truncated)', 'DIO');
      } else {
        AppLogger.d('Response: $dataStr', 'DIO');
      }
    }

    handler.next(response);
  }

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) {
    AppLogger.separator('API ERROR');
    AppLogger.e(
      '${err.requestOptions.method} ${err.requestOptions.uri}',
      err.error,
      err.stackTrace,
      'DIO',
    );
    AppLogger.w('Error Type: ${err.type}', 'DIO');

    if (err.response != null) {
      AppLogger.w('Status: ${err.response?.statusCode}', 'DIO');
      AppLogger.w('Response: ${err.response?.data}', 'DIO');
    }

    handler.next(err);
  }

  /// Sanitize headers to hide sensitive data
  Map<String, dynamic> _sanitizeHeaders(Map<String, dynamic> headers) {
    final sanitized = Map<String, dynamic>.from(headers);

    final sensitiveKeys = [
      'x-api-key',
      'authorization',
      'cookie',
      'set-cookie',
    ];

    for (final key in sensitiveKeys) {
      final lowerKey = key.toLowerCase();
      for (final headerKey in headers.keys) {
        if (headerKey.toLowerCase() == lowerKey) {
          final value = headers[headerKey]?.toString() ?? '';
          if (value.length > 10) {
            sanitized[headerKey] = '${value.substring(0, 10)}...***';
          } else {
            sanitized[headerKey] = '***';
          }
        }
      }
    }

    return sanitized;
  }
}
