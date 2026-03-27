import 'dart:io';
import 'package:dio/dio.dart';
import '../../../../core/constants/api_constants.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/utils/app_logger.dart';
import '../../model/broadcast_model.dart';
import '../../model/eligible_booking_model.dart';
import '../../model/ticket_category_model.dart';
import '../../model/ticket_message_model.dart';
import '../../model/ticket_model.dart';

/// TicketRepository — handles all ticket/broadcast API calls via DioClient.
/// Uses the authenticated DioClient singleton which auto-attaches Bearer token.
class TicketRepository {
  final _dio = DioClient().dio;

  /// GET /tickets/categories — fetch all active ticket categories grouped by type.
  Future<Map<String, List<TicketCategoryModel>>> getCategories() async {
    try {
      final response = await _dio.get(ApiConfig.ticketCategories);
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        final data = response.data['data'] as Map<String, dynamic>;
        final result = <String, List<TicketCategoryModel>>{};
        data.forEach((type, items) {
          result[type] = (items as List).map((c) => TicketCategoryModel.fromJson(c)).toList();
        });
        return result;
      }
      return {};
    } catch (e, s) {
      AppLogger.e('Failed to fetch ticket categories', e, s, 'TICKET-REPO');
      return {};
    }
  }

  /// GET /tickets/eligible-bookings — fetch bookings eligible for ticket creation.
  Future<List<EligibleBookingModel>> getEligibleBookings(int userId) async {
    try {
      final response = await _dio.get(
        ApiConfig.ticketEligibleBookings,
        queryParameters: {'user_id': userId},
      );
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        return (response.data['data'] as List)
            .map((b) => EligibleBookingModel.fromJson(b))
            .toList();
      }
      return [];
    } catch (e, s) {
      AppLogger.e('Failed to fetch eligible bookings', e, s, 'TICKET-REPO');
      return [];
    }
  }

  /// GET /tickets — fetch user's tickets with optional status filter.
  Future<List<TicketModel>> listTickets(int userId, {String? status}) async {
    try {
      final params = <String, dynamic>{'user_id': userId};
      if (status != null) params['status'] = status;

      final response = await _dio.get(ApiConfig.tickets, queryParameters: params);
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        return (response.data['data'] as List)
            .map((t) => TicketModel.fromJson(t))
            .toList();
      }
      return [];
    } catch (e, s) {
      AppLogger.e('Failed to fetch tickets', e, s, 'TICKET-REPO');
      return [];
    }
  }

  /// POST /tickets — create a new ticket with initial message.
  /// Returns the created ticket ID or null on failure.
  Future<int?> createTicket({
    required int userId,
    required int categoryId,
    String? orderId,
    required String subject,
    String? initialMessage,
  }) async {
    try {
      final response = await _dio.post(
        ApiConfig.tickets,
        data: {
          'user_id': userId,
          'category_id': categoryId,
          'order_id': orderId,
          'subject': subject,
          'initial_message': initialMessage,
        },
      );
      if ((response.statusCode == 200 || response.statusCode == 201) &&
          response.data['status'] == 'success') {
        AppLogger.s('Ticket created: ${response.data['data']['ticket_number']}', 'TICKET-REPO');
        return response.data['data']['id'];
      }
      AppLogger.w('Ticket creation failed: ${response.data['message']}', 'TICKET-REPO');
      return null;
    } catch (e, s) {
      AppLogger.e('Failed to create ticket', e, s, 'TICKET-REPO');
      return null;
    }
  }

  /// GET /tickets/{id} — fetch ticket detail with paginated messages.
  Future<TicketDetailResult?> getTicket(int ticketId, int userId, {int page = 1}) async {
    try {
      final response = await _dio.get(
        ApiConfig.ticketById(ticketId),
        queryParameters: {'user_id': userId, 'page': page},
      );
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        final data = response.data['data'];
        return TicketDetailResult(
          ticket: TicketModel.fromJson(data['ticket']),
          messages: (data['messages'] as List).map((m) => TicketMessageModel.fromJson(m)).toList(),
          canReopen: data['can_reopen'] == true,
          currentPage: response.data['meta']?['current_page'] ?? 1,
          lastPage: response.data['meta']?['last_page'] ?? 1,
        );
      }
      return null;
    } catch (e, s) {
      AppLogger.e('Failed to fetch ticket detail', e, s, 'TICKET-REPO');
      return null;
    }
  }

  /// POST /tickets/{id}/messages — send a text message.
  Future<TicketMessageModel?> sendTextMessage(int ticketId, int userId, String text) async {
    try {
      final response = await _dio.post(
        ApiConfig.ticketMessages(ticketId),
        data: {'user_id': userId, 'message_text': text},
      );
      if ((response.statusCode == 200 || response.statusCode == 201) &&
          response.data['status'] == 'success') {
        return TicketMessageModel.fromJson(response.data['data']);
      }
      return null;
    } catch (e, s) {
      AppLogger.e('Failed to send text message', e, s, 'TICKET-REPO');
      return null;
    }
  }

  /// POST /tickets/{id}/messages — send an image message with optional caption.
  Future<TicketMessageModel?> sendImageMessage(int ticketId, int userId, File image, {String? caption}) async {
    try {
      final formData = FormData.fromMap({
        'user_id': userId,
        'image': await MultipartFile.fromFile(image.path, filename: image.path.split('/').last),
        if (caption != null) 'caption': caption,
      });

      final response = await _dio.post(
        ApiConfig.ticketMessages(ticketId),
        data: formData,
        options: Options(contentType: 'multipart/form-data'),
      );
      if ((response.statusCode == 200 || response.statusCode == 201) &&
          response.data['status'] == 'success') {
        return TicketMessageModel.fromJson(response.data['data']);
      }
      return null;
    } catch (e, s) {
      AppLogger.e('Failed to send image message', e, s, 'TICKET-REPO');
      return null;
    }
  }

  /// POST /tickets/{id}/read — mark ticket as read.
  Future<bool> markAsRead(int ticketId, int userId) async {
    try {
      final response = await _dio.post(
        ApiConfig.ticketRead(ticketId),
        data: {'user_id': userId},
      );
      return response.statusCode == 200;
    } catch (e) {
      AppLogger.w('Failed to mark ticket as read: $e', 'TICKET-REPO');
      return false;
    }
  }

  /// POST /tickets/{id}/close — close a ticket.
  Future<bool> closeTicket(int ticketId, int userId) async {
    try {
      final response = await _dio.post(
        ApiConfig.ticketClose(ticketId),
        data: {'user_id': userId},
      );
      return response.statusCode == 200 && response.data['status'] == 'success';
    } catch (e, s) {
      AppLogger.e('Failed to close ticket', e, s, 'TICKET-REPO');
      return false;
    }
  }

  /// POST /tickets/{id}/reopen — reopen a closed ticket.
  Future<bool> reopenTicket(int ticketId, int userId) async {
    try {
      final response = await _dio.post(
        ApiConfig.ticketReopen(ticketId),
        data: {'user_id': userId},
      );
      return response.statusCode == 200 && response.data['status'] == 'success';
    } catch (e, s) {
      AppLogger.e('Failed to reopen ticket', e, s, 'TICKET-REPO');
      return false;
    }
  }

  /// GET /broadcasts — fetch broadcasts visible to the user.
  Future<List<BroadcastModel>> listBroadcasts(int userId) async {
    try {
      final response = await _dio.get(
        ApiConfig.broadcasts,
        queryParameters: {'user_id': userId},
      );
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        return (response.data['data'] as List)
            .map((b) => BroadcastModel.fromJson(b))
            .toList();
      }
      return [];
    } catch (e, s) {
      AppLogger.e('Failed to fetch broadcasts', e, s, 'TICKET-REPO');
      return [];
    }
  }

  /// GET /broadcasts/{id} — fetch broadcast detail and mark as read.
  Future<BroadcastModel?> getBroadcast(int broadcastId, int userId) async {
    try {
      final response = await _dio.get(
        ApiConfig.broadcastById(broadcastId),
        queryParameters: {'user_id': userId},
      );
      if (response.statusCode == 200 && response.data['status'] == 'success') {
        return BroadcastModel.fromJson(response.data['data']);
      }
      return null;
    } catch (e, s) {
      AppLogger.e('Failed to fetch broadcast', e, s, 'TICKET-REPO');
      return null;
    }
  }
}

/// Result container for ticket detail API response
class TicketDetailResult {
  final TicketModel ticket;
  final List<TicketMessageModel> messages;
  final bool canReopen;
  final int currentPage;
  final int lastPage;

  TicketDetailResult({
    required this.ticket,
    required this.messages,
    required this.canReopen,
    required this.currentPage,
    required this.lastPage,
  });
}
