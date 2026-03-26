import 'dart:io';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/utils/app_logger.dart';
import '../data/repositories/ticket_repository.dart';
import '../model/ticket_message_model.dart';
import '../provider/ticket_provider.dart';

/// TicketController state — tracks loading/sending/error states.
class TicketControllerState {
  final bool isLoading;
  final bool isSendingMessage;
  final bool isUploadingImage;
  final String? error;
  final String? successMessage;

  const TicketControllerState({
    this.isLoading = false,
    this.isSendingMessage = false,
    this.isUploadingImage = false,
    this.error,
    this.successMessage,
  });

  TicketControllerState copyWith({
    bool? isLoading,
    bool? isSendingMessage,
    bool? isUploadingImage,
    String? error,
    String? successMessage,
  }) {
    return TicketControllerState(
      isLoading: isLoading ?? this.isLoading,
      isSendingMessage: isSendingMessage ?? this.isSendingMessage,
      isUploadingImage: isUploadingImage ?? this.isUploadingImage,
      error: error,
      successMessage: successMessage,
    );
  }
}

/// TicketController — manages ticket actions (create, send, close, reopen).
/// Uses StateNotifier pattern matching existing ChatController.
class TicketController extends StateNotifier<TicketControllerState> {
  final Ref ref;
  final TicketRepository _repository;

  TicketController(this.ref)
      : _repository = ref.read(ticketRepositoryProvider),
        super(const TicketControllerState());

  /// Create a new ticket — returns the ticket ID on success, null on failure.
  Future<int?> createTicket({
    required int userId,
    required int categoryId,
    String? orderId,
    required String subject,
    String? initialMessage,
  }) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final ticketId = await _repository.createTicket(
        userId: userId,
        categoryId: categoryId,
        orderId: orderId,
        subject: subject,
        initialMessage: initialMessage,
      );

      if (ticketId != null) {
        /// Invalidate ticket list to refresh after creation
        ref.invalidate(ticketListProvider);
        state = state.copyWith(isLoading: false, successMessage: 'Ticket created');
        return ticketId;
      } else {
        state = state.copyWith(isLoading: false, error: 'Failed to create ticket');
        return null;
      }
    } catch (e, s) {
      AppLogger.e('Create ticket failed', e, s, 'TICKET-CTRL');
      state = state.copyWith(isLoading: false, error: e.toString());
      return null;
    }
  }

  /// Send a text message to a ticket — returns the message on success.
  Future<TicketMessageModel?> sendTextMessage(int ticketId, int userId, String text) async {
    state = state.copyWith(isSendingMessage: true, error: null);
    try {
      final message = await _repository.sendTextMessage(ticketId, userId, text);
      state = state.copyWith(isSendingMessage: false);
      return message;
    } catch (e, s) {
      AppLogger.e('Send text failed', e, s, 'TICKET-CTRL');
      state = state.copyWith(isSendingMessage: false, error: e.toString());
      return null;
    }
  }

  /// Send an image message to a ticket — returns the message on success.
  Future<TicketMessageModel?> sendImageMessage(int ticketId, int userId, File image, {String? caption}) async {
    state = state.copyWith(isUploadingImage: true, error: null);
    try {
      final message = await _repository.sendImageMessage(ticketId, userId, image, caption: caption);
      state = state.copyWith(isUploadingImage: false);
      return message;
    } catch (e, s) {
      AppLogger.e('Send image failed', e, s, 'TICKET-CTRL');
      state = state.copyWith(isUploadingImage: false, error: e.toString());
      return null;
    }
  }

  /// Close a ticket.
  Future<bool> closeTicket(int ticketId, int userId) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final success = await _repository.closeTicket(ticketId, userId);
      ref.invalidate(ticketListProvider);
      state = state.copyWith(isLoading: false);
      return success;
    } catch (e, s) {
      AppLogger.e('Close ticket failed', e, s, 'TICKET-CTRL');
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  /// Reopen a ticket.
  Future<bool> reopenTicket(int ticketId, int userId) async {
    state = state.copyWith(isLoading: true, error: null);
    try {
      final success = await _repository.reopenTicket(ticketId, userId);
      ref.invalidate(ticketListProvider);
      state = state.copyWith(isLoading: false);
      return success;
    } catch (e, s) {
      AppLogger.e('Reopen ticket failed', e, s, 'TICKET-CTRL');
      state = state.copyWith(isLoading: false, error: e.toString());
      return false;
    }
  }

  /// Mark ticket as read.
  Future<void> markAsRead(int ticketId, int userId) async {
    await _repository.markAsRead(ticketId, userId);
    ref.invalidate(ticketListProvider);
  }
}

/// Provider for the TicketController
final ticketControllerProvider =
    StateNotifierProvider<TicketController, TicketControllerState>((ref) {
  return TicketController(ref);
});
