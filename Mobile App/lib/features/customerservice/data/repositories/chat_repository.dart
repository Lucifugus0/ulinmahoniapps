import 'dart:io';
import 'package:dio/dio.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/constants/api_constants.dart';

/// Repository for chat operations
/// Handles all chat-related API calls following backend documentation
class ChatRepository {
  final DioClient _dioClient;

  ChatRepository({DioClient? dioClient}) : _dioClient = dioClient ?? DioClient();

  /// Create a new conversation
  /// POST /chat/conversations
  /// Returns conversation object with ID and participants
  Future<ApiResult<Map<String, dynamic>>> createConversation({
    required int userId,
    required String orderId,
    required String title,
    required String initialMessage,
  }) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.chatConversations,
        data: {
          'user_id': userId,
          'order_id': orderId,
          'title': title,
          'initial_message': initialMessage,
        },
      );

      if (response.statusCode == 201) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Conversation created: ${body['data']['id']}', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 201,
        );
      }

      // Handle duplicate conversation (409 Conflict)
      // Return the existing conversation data as success
      if (response.statusCode == 409) {
        final body = response.data;
        if (body is Map && body['data'] != null) {
          AppLogger.w('Conversation already exists, returning existing data', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error creating conversation', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to create conversation: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Send text message
  /// POST /chat/conversations/{id}/messages
  Future<ApiResult<Map<String, dynamic>>> sendTextMessage({
    required int conversationId,
    required int userId,
    required String messageText,
  }) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.chatConversationMessages(conversationId),
        data: {
          'user_id': userId,
          'message_text': messageText,
        },
      );

      if (response.statusCode == 201) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Message sent: ${body['data']['id']}', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 201,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error sending message', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to send message: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Send image message with optional caption
  /// POST /chat/conversations/{id}/messages (multipart/form-data)
  Future<ApiResult<Map<String, dynamic>>> sendImageMessage({
    required int conversationId,
    required int userId,
    required File image,
    String? messageText,
  }) async {
    try {
      // Create multipart form data
      final formData = FormData.fromMap({
        'user_id': userId,
        'image': await MultipartFile.fromFile(
          image.path,
          filename: image.path.split('/').last,
        ),
        if (messageText != null && messageText.isNotEmpty) 'message_text': messageText,
      });

      final response = await _dioClient.post(
        ApiConfig.chatConversationMessages(conversationId),
        data: formData,
      );

      if (response.statusCode == 201) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Image message sent: ${body['data']['id']}', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 201,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error sending image message', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to send image: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Get conversation list
  /// GET /chat/conversations?user_id={id}
  Future<ApiResult<List<Map<String, dynamic>>>> getConversationList(int userId) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.chatConversations,
        queryParameters: {'user_id': userId},
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] is List) {
          final conversations = (body['data'] as List)
              .map((e) => e as Map<String, dynamic>)
              .toList();

          AppLogger.s('Fetched ${conversations.length} conversations', 'CHAT-REPO');
          return Success(conversations);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      // Return empty list for 404
      if (e.response?.statusCode == 404) {
        AppLogger.i('No conversations found (404)', 'CHAT-REPO');
        return Success([]);
      }
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching conversations', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch conversations: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Get conversation detail with messages
  /// GET /chat/conversations/{id}?user_id={userId}&page={page}
  Future<ApiResult<Map<String, dynamic>>> getConversationDetail({
    required int conversationId,
    required int userId,
    int page = 1,
  }) async {
    try {
      final response = await _dioClient.get(
        ApiConfig.chatConversationById(conversationId),
        queryParameters: {
          'user_id': userId,
          'page': page,
        },
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Fetched conversation detail: $conversationId', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      AppLogger.e('DioException in getConversationDetail', e, e.stackTrace, 'CHAT-REPO');
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error fetching conversation detail', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch conversation: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Mark messages as read
  /// POST /chat/conversations/{id}/read
  Future<ApiResult<void>> markAsRead({
    required int conversationId,
    required int userId,
  }) async {
    try {
      final response = await _dioClient.post(
        ApiConfig.chatConversationRead(conversationId),
        data: {'user_id': userId},
      );

      if (response.statusCode == 200) {
        AppLogger.s('Marked conversation as read: $conversationId', 'CHAT-REPO');
        return Success(null);
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error marking as read', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to mark as read: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Edit message
  /// PUT /chat/messages/{id}
  Future<ApiResult<Map<String, dynamic>>> editMessage({
    required int messageId,
    required int userId,
    required String messageText,
  }) async {
    try {
      final response = await _dioClient.put(
        ApiConfig.chatMessageById(messageId),
        data: {
          'user_id': userId,
          'message_text': messageText,
        },
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success' && body['data'] != null) {
          AppLogger.s('Message edited: $messageId', 'CHAT-REPO');
          return Success(body['data'] as Map<String, dynamic>);
        }

        return Failure(
          errorType: ApiErrorType.parsing,
          message: 'Invalid response format',
          statusCode: 200,
        );
      }

      return _handleErrorResponse(response);
    } on DioException catch (e) {
      return _handleDioException(e);
    } catch (e, stackTrace) {
      AppLogger.e('Error editing message', e, stackTrace, 'CHAT-REPO');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to edit message: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Handle error response
  Failure<T> _handleErrorResponse<T>(Response response) {
    final body = response.data;
    String message = 'An error occurred';

    if (body is Map) {
      message = body['message'] ?? message;
    }

    // Handle specific status codes
    if (response.statusCode == 403) {
      return Failure(
        errorType: ApiErrorType.unauthorized,
        message: message,
        statusCode: 403,
      );
    }

    if (response.statusCode == 404) {
      return Failure(
        errorType: ApiErrorType.notFound,
        message: message,
        statusCode: 404,
      );
    }

    if (response.statusCode == 422) {
      // Include validation errors in message if available
      final validationMessage = body is Map && body['errors'] != null
          ? '$message: ${body['errors']}'
          : message;
      return Failure(
        errorType: ApiErrorType.validation,
        message: validationMessage,
        statusCode: 422,
      );
    }

    return Failure(
      errorType: ApiErrorType.server,
      message: message,
      statusCode: response.statusCode,
    );
  }

  /// Handle Dio exceptions
  Failure<T> _handleDioException<T>(DioException e) {
    AppLogger.e('DioException in ChatRepository', e, e.stackTrace, 'CHAT-REPO');

    if (e.type == DioExceptionType.connectionTimeout ||
        e.type == DioExceptionType.receiveTimeout ||
        e.type == DioExceptionType.sendTimeout) {
      return Failure(
        errorType: ApiErrorType.timeout,
        message: 'Request timeout. Please check your connection.',
        originalError: e,
      );
    }

    if (e.type == DioExceptionType.connectionError) {
      return Failure(
        errorType: ApiErrorType.network,
        message: 'No internet connection',
        originalError: e,
      );
    }

    if (e.response != null) {
      return _handleErrorResponse(e.response!);
    }

    return Failure(
      errorType: ApiErrorType.unknown,
      message: e.message ?? 'Unknown error occurred',
      originalError: e,
    );
  }
}
