import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../auth/login/provider/auth_provider.dart';
import '../presentation/widgets/mybooking_card.dart';
import '../../../../core/utils/app_logger.dart';

class BookingDisplayStatus {
  final String text;
  final Color color;
  BookingDisplayStatus(this.text, this.color);
}

BookingDisplayStatus _getBookingDisplayStatus(String? transactionStatus, String tabType) {
  final status = transactionStatus?.toLowerCase().trim() ?? '';
  final tab = tabType.toLowerCase();

  if (tab == 'all bookings') {
    // Tab Mendatang: Pending and Waiting only
    switch (status) {
      case 'pending':
        return BookingDisplayStatus('Pending', Colors.red);
      case 'waiting':
        return BookingDisplayStatus('Waiting', Colors.orange);
      default:
        return BookingDisplayStatus(transactionStatus ?? 'Unknown', Colors.grey);
    }
  }
  else if (tab == 'completed') {
    // Tab Selesai: Paid, Cancelled, Rejected, Expired
    switch (status) {
      case 'paid':
        return BookingDisplayStatus('Paid', Colors.green);
      case 'cancelled':
        return BookingDisplayStatus('Cancelled', Colors.red);
      case 'rejected':
        return BookingDisplayStatus('Rejected', Colors.red);
      case 'expired':
        return BookingDisplayStatus('Expired', Colors.grey);
      default:
        return BookingDisplayStatus(transactionStatus ?? 'Unknown', Colors.grey);
    }
  }

  return BookingDisplayStatus(transactionStatus ?? 'Unknown Status', Colors.grey);
}

List<Widget> buildBookingList(List bookings, String tabType) {
  final lowerTabType = tabType.toLowerCase();

  // Debug: Log all booking statuses
  AppLogger.d('=== BOOKING LIST DEBUG ===', 'MYBOOKING-CONTROLLER');
  AppLogger.d('Tab Type: $tabType', 'MYBOOKING-CONTROLLER');
  AppLogger.d('Total bookings: ${bookings.length}', 'MYBOOKING-CONTROLLER');
  for (var booking in bookings) {
    AppLogger.d('Booking ${booking.orderId}: Status = "${booking.transactionStatus}"', 'MYBOOKING-CONTROLLER');
  }

  final bookingCards = bookings
      .where((booking) {
    final bookingStatus = booking.transactionStatus?.toLowerCase().trim() ?? '';

    AppLogger.d('Filter check for tab "$lowerTabType": ${booking.orderId}, status="$bookingStatus" (length=${bookingStatus.length})', 'MYBOOKING-CONTROLLER');

    if (lowerTabType == 'all bookings') {
      // Tab Mendatang: Show Pending and Waiting only
      final isPending = bookingStatus == 'pending';
      final isWaiting = bookingStatus == 'waiting';
      final matches = isPending || isWaiting;
      AppLogger.d('  → Mendatang check: isPending=$isPending, isWaiting=$isWaiting, matches=$matches', 'MYBOOKING-CONTROLLER');
      if (matches) {
        AppLogger.d('  → ✅ MATCHED for Mendatang: $bookingStatus', 'MYBOOKING-CONTROLLER');
      }
      return matches;
    } else if (lowerTabType == 'completed') {
      // Tab Selesai: Show Paid, Cancelled, Rejected, and Expired
      final matches = bookingStatus == 'paid' ||
          bookingStatus == 'cancelled' ||
          bookingStatus == 'rejected' ||
          bookingStatus == 'expired';
      if (matches) {
        AppLogger.d('  → ✅ MATCHED for Selesai: $bookingStatus', 'MYBOOKING-CONTROLLER');
      }
      return matches;
    }
    return false;
  })
      .map((booking) {
    final displayStatus = _getBookingDisplayStatus(booking.transactionStatus, tabType);

    return BookingCard(
      id: booking.idrec,
      orderId: booking.orderId,
      title: booking.propertyName ?? '-',
      roomName: booking.roomName ?? '-',
      roomNo: booking.roomNo,
      checkIn: booking.checkIn ?? '-',
      checkOut: booking.checkOut ?? '-',
      status: displayStatus.text,
      propertyId: booking.propertyId,
      dataDetail: booking.toJson(),
    );
  })
      .toList();

  // Add bottom spacing to prevent overlap with bottom navigation bar
  return [
    ...bookingCards,
    const SizedBox(height: 120),
  ];
}

bool checkLoginAndRedirect(BuildContext context, WidgetRef ref) {
  final authState = ref.read(authProvider);
  final user = authState.user.value;

  if (user == null || user.id == null) {
    Future.microtask(() => context.push('/login'));
    return false;
  }

  return true;
}