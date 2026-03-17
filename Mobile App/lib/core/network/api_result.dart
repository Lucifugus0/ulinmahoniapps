/// Type-safe wrapper for API responses
///
/// Usage:
/// ```dart
/// final result = await repository.fetchData();
/// switch (result) {
///   case Success(:final data):
///     // Handle success
///   case Failure(:final errorType, :final message):
///     // Handle error based on type
/// }
/// ```
sealed class ApiResult<T> {}

/// Successful API response with data
class Success<T> extends ApiResult<T> {
  final T data;
  Success(this.data);
}

/// Failed API response with error details
class Failure<T> extends ApiResult<T> {
  final ApiErrorType errorType;
  final String message;
  final int? statusCode;
  final dynamic originalError;

  Failure({
    required this.errorType,
    required this.message,
    this.statusCode,
    this.originalError,
  });

  @override
  String toString() => 'Failure(errorType: $errorType, message: $message, statusCode: $statusCode)';
}

/// Types of API errors for categorization
enum ApiErrorType {
  /// No internet connection
  network,

  /// Request timeout (connection or receive)
  timeout,

  /// 401 - Unauthorized (session expired)
  unauthorized,

  /// 403 - Forbidden (insufficient permissions)
  forbidden,

  /// 404 - Not Found
  notFound,

  /// 400 - Bad Request
  badRequest,

  /// 422 - Validation Error
  validation,

  /// 500+ - Server Error
  server,

  /// Response parsing error (invalid JSON, type mismatch)
  parsing,

  /// Unknown error
  unknown,
}

/// Extension to get user-friendly error messages in Indonesian
extension ApiErrorTypeExtension on ApiErrorType {
  String get defaultMessage {
    switch (this) {
      case ApiErrorType.network:
        return 'Tidak ada koneksi internet. Periksa koneksi Anda.';
      case ApiErrorType.timeout:
        return 'Koneksi timeout. Silakan coba lagi.';
      case ApiErrorType.unauthorized:
        return 'Sesi Anda telah berakhir. Silakan login kembali.';
      case ApiErrorType.forbidden:
        return 'Akses ditolak.';
      case ApiErrorType.notFound:
        return 'Data tidak ditemukan.';
      case ApiErrorType.badRequest:
        return 'Permintaan tidak valid.';
      case ApiErrorType.validation:
        return 'Data yang dikirim tidak valid.';
      case ApiErrorType.server:
        return 'Terjadi kesalahan server. Silakan coba lagi nanti.';
      case ApiErrorType.parsing:
        return 'Terjadi kesalahan saat memproses data.';
      case ApiErrorType.unknown:
        return 'Terjadi kesalahan tidak terduga.';
    }
  }
}
