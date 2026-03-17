import 'package:dio/dio.dart';
import '../../utils/app_logger.dart';

/// Dio interceptor for automatic retry on network failures
/// Retries up to 3 times with exponential backoff
class RetryInterceptor extends Interceptor {
  final int maxRetries;
  final Duration initialDelay;

  RetryInterceptor({
    this.maxRetries = 3,
    this.initialDelay = const Duration(milliseconds: 500),
  });

  @override
  void onError(DioException err, ErrorInterceptorHandler handler) async {
    // Only retry on connection errors
    if (!_shouldRetry(err)) {
      return handler.next(err);
    }

    final attempt = err.requestOptions.extra['retry_attempt'] as int? ?? 0;

    if (attempt >= maxRetries) {
      AppLogger.w(
        'Max retries ($maxRetries) reached for ${err.requestOptions.uri}',
        'RETRY',
      );
      return handler.next(err);
    }

    final nextAttempt = attempt + 1;
    final delay = _calculateDelay(nextAttempt);

    AppLogger.w(
      'Retrying request (attempt $nextAttempt/$maxRetries) after ${delay.inMilliseconds}ms',
      'RETRY',
    );

    await Future.delayed(delay);

    // Clone request with updated retry count
    final requestOptions = err.requestOptions;
    requestOptions.extra['retry_attempt'] = nextAttempt;

    try {
      final response = await Dio().fetch(requestOptions);
      return handler.resolve(response);
    } on DioException catch (e) {
      return handler.next(e);
    }
  }

  /// Check if error should trigger a retry
  bool _shouldRetry(DioException err) {
    // Retry on connection timeout, send timeout, or network errors
    return err.type == DioExceptionType.connectionTimeout ||
        err.type == DioExceptionType.sendTimeout ||
        err.type == DioExceptionType.receiveTimeout ||
        err.type == DioExceptionType.connectionError;
  }

  /// Calculate delay with exponential backoff
  Duration _calculateDelay(int attempt) {
    final milliseconds = initialDelay.inMilliseconds * (1 << (attempt - 1));
    return Duration(milliseconds: milliseconds);
  }
}
