import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/ticket_repository.dart';
import '../model/broadcast_model.dart';
import '../model/eligible_booking_model.dart';
import '../model/ticket_category_model.dart';
import '../model/ticket_model.dart';

/// Provider for the TicketRepository singleton
final ticketRepositoryProvider = Provider<TicketRepository>((ref) {
  return TicketRepository();
});

/// Provider for ticket categories — fetched once and cached.
/// Returns Map<String, List<TicketCategoryModel>> grouped by ticket_type.
final ticketCategoriesProvider = FutureProvider<Map<String, List<TicketCategoryModel>>>((ref) async {
  final repo = ref.read(ticketRepositoryProvider);
  return repo.getCategories();
});

/// Provider for eligible bookings — parameterized by userId.
final eligibleBookingsProvider = FutureProvider.family<List<EligibleBookingModel>, int>((ref, userId) async {
  final repo = ref.read(ticketRepositoryProvider);
  return repo.getEligibleBookings(userId);
});

/// Provider for user's ticket list — parameterized by userId.
/// Invalidate this provider to refresh the list after ticket creation/close/reopen.
final ticketListProvider = FutureProvider.family<List<TicketModel>, int>((ref, userId) async {
  final repo = ref.read(ticketRepositoryProvider);
  return repo.listTickets(userId);
});

/// Provider for ticket detail — parameterized by (ticketId, userId) pair.
final ticketDetailProvider = FutureProvider.family<TicketDetailResult?, TicketDetailParams>((ref, params) async {
  final repo = ref.read(ticketRepositoryProvider);
  return repo.getTicket(params.ticketId, params.userId);
});

/// Provider for broadcast list — parameterized by userId.
final broadcastListProvider = FutureProvider.family<List<BroadcastModel>, int>((ref, userId) async {
  final repo = ref.read(ticketRepositoryProvider);
  return repo.listBroadcasts(userId);
});

/// Provider to track currently selected ticket ID
final selectedTicketProvider = StateProvider<int?>((ref) => null);

/// Total unread chat count across all tickets — used for bottom nav badge.
/// Returns 0 on any error (network, auth, first install) to prevent crashes.
final chatUnreadCountProvider = FutureProvider.family<int, int>((ref, userId) async {
  try {
    final repo = ref.read(ticketRepositoryProvider);
    final tickets = await repo.listTickets(userId);
    return tickets.fold<int>(0, (sum, t) => sum + t.unreadCount);
  } catch (_) {
    return 0;
  }
});

/// Parameters for ticket detail provider
class TicketDetailParams {
  final int ticketId;
  final int userId;

  TicketDetailParams({required this.ticketId, required this.userId});

  @override
  bool operator ==(Object other) =>
      identical(this, other) ||
      other is TicketDetailParams && ticketId == other.ticketId && userId == other.userId;

  @override
  int get hashCode => ticketId.hashCode ^ userId.hashCode;
}
