import 'package:dio/dio.dart';
import 'package:flutter/foundation.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import '../constants/api_constants.dart';
import 'interceptors/logging_interceptor.dart';
import 'interceptors/retry_interceptor.dart';
import 'interceptors/auth_interceptor.dart';
import '../utils/app_logger.dart';

/// Singleton Dio client for all HTTP requests
///
/// Features:
/// - Automatic API key injection
/// - Request/response logging via AppLogger
/// - Auto-retry on network failures (3x with exponential backoff)
/// - Global 401 handling
/// - Timeout configuration
class DioClient {
  static final DioClient _instance = DioClient._internal();
  factory DioClient() => _instance;

  late final Dio _dio;
  Dio get dio => _dio;

  DioClient._internal() {
    _dio = Dio(_buildBaseOptions());
    _setupInterceptors();
    AppLogger.i('DioClient initialized with baseUrl: ${ApiConfig.baseUrl}', 'DIO-CLIENT');
  }

  /// Build base options for Dio
  BaseOptions _buildBaseOptions() {
    return BaseOptions(
      baseUrl: ApiConfig.baseUrl,
      connectTimeout: const Duration(seconds: 30),
      receiveTimeout: const Duration(seconds: 60),
      sendTimeout: const Duration(seconds: 30),
      headers: {
        'x-api-key': ApiConfig.apiKey,
        'X-App-Version': dotenv.env['VERSION_NAME']?.replaceAll('Release ', '') ?? '0.0.0',
        'Accept': 'application/json',
        'Content-Type': 'application/json',
      },
      validateStatus: (status) {
        // Accept all status codes to handle them manually
        return status != null && status < 500;
      },
    );
  }

  /// Setup interceptors (order matters!)
  void _setupInterceptors() {
    _dio.interceptors.clear();

    // 1. Retry interceptor (first to retry failed requests)
    _dio.interceptors.add(RetryInterceptor(
      maxRetries: 3,
      initialDelay: const Duration(milliseconds: 500),
    ));

    // 2. Auth interceptor (handle 401 globally)
    _dio.interceptors.add(AuthInterceptor(
      onUnauthorized: _handleUnauthorized,
    ));

    // 3. Logging interceptor (last to log final request/response)
    if (kDebugMode) {
      _dio.interceptors.add(LoggingInterceptor());
    }
  }

  /// Handle unauthorized access (401)
  void _handleUnauthorized() {
    AppLogger.w('User session expired - redirection should be handled by app', 'DIO-CLIENT');
    // Note: Actual navigation will be handled by the app layer
    // This is just a placeholder for future implementation
  }

  /// GET request
  Future<Response> get(
    String path, {
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
  }) async {
    try {
      return await _dio.get(
        path,
        queryParameters: queryParameters,
        options: options,
        cancelToken: cancelToken,
      );
    } on DioException {
      rethrow;
    }
  }

  /// POST request
  Future<Response> post(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
  }) async {
    try {
      return await _dio.post(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
        cancelToken: cancelToken,
      );
    } on DioException {
      rethrow;
    }
  }

  /// PUT request
  Future<Response> put(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
  }) async {
    try {
      return await _dio.put(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
        cancelToken: cancelToken,
      );
    } on DioException {
      rethrow;
    }
  }

  /// DELETE request
  Future<Response> delete(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
  }) async {
    try {
      return await _dio.delete(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
        cancelToken: cancelToken,
      );
    } on DioException {
      rethrow;
    }
  }

  /// PATCH request
  Future<Response> patch(
    String path, {
    dynamic data,
    Map<String, dynamic>? queryParameters,
    Options? options,
    CancelToken? cancelToken,
  }) async {
    try {
      return await _dio.patch(
        path,
        data: data,
        queryParameters: queryParameters,
        options: options,
        cancelToken: cancelToken,
      );
    } on DioException {
      rethrow;
    }
  }
}
