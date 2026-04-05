import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/widgets/appbar.dart';
import '../../../../l10n/app_localizations.dart';
import '../../../../features/auth/login/provider/auth_provider.dart';
import '../../model/ticket_model.dart';
import '../../model/broadcast_model.dart';
import '../../provider/ticket_provider.dart';
import '../../../../core/utils/app_logger.dart';

/// TicketListPage — Main customer service page with two tabs: "My Tickets" and "Broadcasts".
/// Shows a list of user's support tickets and broadcast announcements.
/// Uses ConsumerStatefulWidget with Riverpod for state management.
class TicketListPage extends ConsumerStatefulWidget {
  const TicketListPage({super.key});

  @override
  ConsumerState<TicketListPage> createState() => _TicketListPageState();
}

class _TicketListPageState extends ConsumerState<TicketListPage>
    with SingleTickerProviderStateMixin {
  /// Tab controller for switching between "My Tickets" and "Broadcasts"
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  /// Refresh ticket list by invalidating the provider
  Future<void> _onRefreshTickets(int userId) async {
    ref.invalidate(ticketListProvider);
    await ref.read(ticketListProvider(userId).future).catchError((_) {
      return <TicketModel>[];
    });
    AppLogger.s('Ticket list refreshed', 'TICKET-LIST');
  }

  /// Refresh broadcast list by invalidating the provider
  Future<void> _onRefreshBroadcasts(int userId) async {
    ref.invalidate(broadcastListProvider);
    await ref.read(broadcastListProvider(userId).future).catchError((_) {
      return <BroadcastModel>[];
    });
    AppLogger.s('Broadcast list refreshed', 'TICKET-LIST');
  }

  /// Get color for ticket status badge
  Color _getStatusColor(String status) {
    switch (status) {
      case 'open':
        return AppColors.green;
      case 'in_progress':
        return AppColors.blue;
      case 'closed':
        return Colors.grey;
      case 'reopened':
        return AppColors.amber;
      default:
        return Colors.grey;
    }
  }

  /// Get color for ticket category type badge
  Color _getCategoryTypeColor(String? ticketType) {
    switch (ticketType) {
      case 'booking':
        return AppColors.purple;
      case 'complaint':
        return AppColors.red;
      case 'suggestion':
        return AppColors.teal;
      default:
        return Colors.grey;
    }
  }

  /// Format relative time from a DateTime (e.g., "2h ago", "3d ago")
  String _formatRelativeTime(DateTime? dateTime) {
    if (dateTime == null) return '';
    final now = DateTime.now();
    final diff = now.difference(dateTime);

    if (diff.inMinutes < 1) return 'now';
    if (diff.inMinutes < 60) return '${diff.inMinutes}m ago';
    if (diff.inHours < 24) return '${diff.inHours}h ago';
    if (diff.inDays < 7) return '${diff.inDays}d ago';
    return '${dateTime.day}/${dateTime.month}/${dateTime.year}';
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final user = ref.watch(authProvider).user.value;

    /// Show loading spinner if user is not yet available
    if (user == null) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final userId = user.id;

    return MainLayout(
      showNavBar: false,
      showBottomNav: false,
      currentIndex: 3,
      child: Column(
        children: [
          /// Custom app bar with title "Customer Service"
          CustomAppBar(
            title: localizations.csTitle,
            showBackButton: true,
          ),

          /// Tab bar for switching between "My Tickets" and "Broadcasts"
          Container(
            color: isDark ? AppColors.surfaceDark : Colors.white,
            child: TabBar(
              controller: _tabController,
              labelColor: AppColors.primaryColor,
              unselectedLabelColor: isDark ? Colors.grey[400] : Colors.grey[600],
              indicatorColor: AppColors.primaryColor,
              indicatorWeight: 3,
              tabs: const [
                Tab(text: 'My Chats'),
                Tab(text: 'Broadcasts'),
              ],
            ),
          ),

          /// Tab bar view content area
          Expanded(
            child: TabBarView(
              controller: _tabController,
              children: [
                /// "My Tickets" tab content
                _buildTicketsTab(userId, isDark, localizations),

                /// "Broadcasts" tab content
                _buildBroadcastsTab(userId, isDark, localizations),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Build the "My Tickets" tab with FAB for creating new tickets.
  /// Shows a list of TicketModel cards sorted by last message time.
  Widget _buildTicketsTab(
      int userId, bool isDark, AppLocalizations localizations) {
    final ticketsAsync = ref.watch(ticketListProvider(userId));

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: RefreshIndicator(
        onRefresh: () => _onRefreshTickets(userId),
        color: AppColors.primaryColor,
        child: ticketsAsync.when(
          data: (tickets) {
            if (tickets.isEmpty) {
              return _buildEmptyState(
                icon: Icons.confirmation_number_outlined,
                title: 'No Chats',
                subtitle: 'You have no chats yet.',
                isDark: isDark,
              );
            }

            /* Bottom padding clears FAB + outer BottomNavBar */
            return ListView.builder(
              padding: EdgeInsets.only(
                left: 16, right: 16, top: 16,
                bottom: 20 + MediaQuery.of(context).padding.bottom,
              ),
              itemCount: tickets.length,
              itemBuilder: (context, index) {
                return _buildTicketCard(tickets[index], isDark);
              },
            );
          },
          loading: () => _buildSkeletonList(isDark),
          error: (error, stack) {
            AppLogger.e('Error loading tickets', error, stack, 'TICKET-LIST');
            return _buildEmptyState(
              icon: Icons.error_outline,
              title: 'Error',
              subtitle: 'Failed to load tickets. Pull to retry.',
              isDark: isDark,
            );
          },
        ),
      ),

      /// FAB button to navigate to create ticket page.
      /// Bottom margin uses MediaQuery safe area + fixed offset to clear the
      /// outer ShellRoute's BottomNavBar (extendBody: true on parent scaffold).
      floatingActionButton: Padding(
        padding: EdgeInsets.only(
          bottom: 5 + MediaQuery.of(context).padding.bottom,
        ),
        child: FloatingActionButton.extended(
          onPressed: () => context.push('/cs/create'),
          backgroundColor: AppColors.primaryColor,
          foregroundColor: Colors.white,
          icon: const Icon(Icons.add),
          label: const Text('New Chat'),
        ),
      ),
    );
  }

  /// Build the "Broadcasts" tab showing read-only announcements.
  Widget _buildBroadcastsTab(
      int userId, bool isDark, AppLocalizations localizations) {
    final broadcastsAsync = ref.watch(broadcastListProvider(userId));

    return RefreshIndicator(
      onRefresh: () => _onRefreshBroadcasts(userId),
      color: AppColors.primaryColor,
      child: broadcastsAsync.when(
        data: (broadcasts) {
          if (broadcasts.isEmpty) {
            return _buildEmptyState(
              icon: Icons.campaign_outlined,
              title: 'No Broadcasts',
              subtitle: 'No announcements to show.',
              isDark: isDark,
            );
          }

          /* Bottom padding clears outer BottomNavBar */
          return ListView.builder(
            padding: EdgeInsets.only(
              left: 16, right: 16, top: 16,
              bottom: 20 + MediaQuery.of(context).padding.bottom,
            ),
            itemCount: broadcasts.length,
            itemBuilder: (context, index) {
              return _buildBroadcastCard(broadcasts[index], isDark);
            },
          );
        },
        loading: () => _buildSkeletonList(isDark),
        error: (error, stack) {
          AppLogger.e('Error loading broadcasts', error, stack, 'TICKET-LIST');
          return _buildEmptyState(
            icon: Icons.error_outline,
            title: 'Error',
            subtitle: 'Failed to load broadcasts. Pull to retry.',
            isDark: isDark,
          );
        },
      ),
    );
  }

  /// Build a single ticket card displaying ticket metadata.
  /// Includes: ticket number, status badge, subject, category type badge,
  /// property name, order ID, last message time, and unread count.
  Widget _buildTicketCard(TicketModel ticket, bool isDark) {
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;
    final textColor = isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final mutedColor =
        isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted;

    return GestureDetector(
      /// Navigate to ticket chat page on tap
      onTap: () => context.push('/cs/ticket/${ticket.id}'),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(12),
          border: ticket.hasUnread
              ? Border.all(color: AppColors.primaryColor, width: 1.5)
              : null,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            /// Top row: ticket number + status badge
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  ticket.ticketNumber,
                  style: TextStyle(
                    fontSize: 13,
                    fontWeight: FontWeight.w600,
                    color: mutedColor,
                  ),
                ),
                _buildBadge(
                  ticket.ticketStatus.replaceAll('_', ' ').toUpperCase(),
                  _getStatusColor(ticket.ticketStatus),
                ),
              ],
            ),
            const SizedBox(height: 8),

            /// Subject text
            Text(
              ticket.subject,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 8),

            /// Category type badge + property name
            Row(
              children: [
                if (ticket.category != null)
                  _buildBadge(
                    ticket.category!.ticketType.toUpperCase(),
                    _getCategoryTypeColor(ticket.category?.ticketType),
                  ),
                const SizedBox(width: 8),
                if (ticket.property != null)
                  Expanded(
                    child: Text(
                      ticket.property!.name,
                      style: TextStyle(fontSize: 13, color: mutedColor),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 8),

            /// Bottom row: order ID + last message time + unread count
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                if (ticket.orderId != null)
                  Text(
                    ticket.orderId!,
                    style: TextStyle(fontSize: 12, color: mutedColor),
                  ),
                const Spacer(),
                Text(
                  _formatRelativeTime(ticket.lastMessageAt ?? ticket.createdAt),
                  style: TextStyle(fontSize: 12, color: mutedColor),
                ),
                if (ticket.hasUnread) ...[
                  const SizedBox(width: 8),

                  /// Unread count badge
                  Container(
                    padding:
                        const EdgeInsets.symmetric(horizontal: 8, vertical: 2),
                    decoration: BoxDecoration(
                      color: AppColors.secondaryColor,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Text(
                      '${ticket.unreadCount}',
                      style: const TextStyle(
                        color: Colors.white,
                        fontSize: 11,
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                  ),
                ],
              ],
            ),
          ],
        ),
      ),
    );
  }

  /// Build a single broadcast card displaying broadcast metadata.
  /// Includes: title, sender type badge, property name, message preview,
  /// sent date, and read/unread indicator.
  Widget _buildBroadcastCard(BroadcastModel broadcast, bool isDark) {
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;
    final textColor = isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final mutedColor =
        isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted;

    return GestureDetector(
      /// Navigate to broadcast detail page on tap
      onTap: () => context.push('/cs/broadcast/${broadcast.id}'),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(12),
          border: !broadcast.isRead
              ? Border.all(color: AppColors.primaryColor, width: 1.5)
              : null,
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            /// Top row: sender type badge + property name
            Row(
              children: [
                _buildBadge(
                  broadcast.isHQ ? 'HQ' : 'FRONT DESK',
                  broadcast.isHQ ? AppColors.indigo : AppColors.teal,
                ),
                const SizedBox(width: 8),
                if (broadcast.property != null)
                  Expanded(
                    child: Text(
                      broadcast.property!.name,
                      style: TextStyle(fontSize: 13, color: mutedColor),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 8),

            /// Broadcast title
            Text(
              broadcast.title,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 6),

            /// Message preview truncated to 100 characters
            Text(
              broadcast.messageText.length > 100
                  ? '${broadcast.messageText.substring(0, 100)}...'
                  : broadcast.messageText,
              style: TextStyle(fontSize: 14, color: mutedColor, height: 1.4),
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
            const SizedBox(height: 8),

            /// Bottom row: sent date + read/unread indicator
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  broadcast.formattedDate,
                  style: TextStyle(fontSize: 12, color: mutedColor),
                ),
                Icon(
                  broadcast.isRead
                      ? Icons.done_all
                      : Icons.mark_email_unread_outlined,
                  size: 18,
                  color: broadcast.isRead ? AppColors.green : AppColors.blue,
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  /// Build a small colored badge with text label
  Widget _buildBadge(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 3),
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.15),
        borderRadius: BorderRadius.circular(6),
      ),
      child: Text(
        label,
        style: TextStyle(
          fontSize: 11,
          fontWeight: FontWeight.w600,
          color: color,
        ),
      ),
    );
  }

  /// Build empty state widget with icon, title, and subtitle
  Widget _buildEmptyState({
    required IconData icon,
    required String title,
    required String subtitle,
    required bool isDark,
  }) {
    return Center(
      child: SingleChildScrollView(
        physics: const AlwaysScrollableScrollPhysics(),
        child: Padding(
          padding: const EdgeInsets.all(24.0),
          child: Column(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              Icon(icon, size: 100, color: Colors.grey.shade300),
              const SizedBox(height: 24),
              Text(
                title,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.w600,
                  color: isDark ? Colors.grey[400] : Colors.grey.shade700,
                ),
              ),
              const SizedBox(height: 12),
              Text(
                subtitle,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 14,
                  color: isDark ? Colors.grey[500] : Colors.grey.shade600,
                  height: 1.5,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Build skeleton loading list for shimmer effect while data loads
  Widget _buildSkeletonList(bool isDark) {
    return Skeletonizer(
      enabled: true,
      child: ListView.builder(
        padding: const EdgeInsets.all(16),
        itemCount: 5,
        itemBuilder: (context, index) {
          return Container(
            margin: const EdgeInsets.only(bottom: 12),
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: isDark ? AppColors.surfaceDark : Colors.white,
              borderRadius: BorderRadius.circular(12),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 4,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// Skeleton: ticket number row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      width: 120,
                      height: 14,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                    Container(
                      width: 60,
                      height: 20,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(6),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 8),

                /// Skeleton: subject line
                Container(
                  width: double.infinity,
                  height: 18,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
                const SizedBox(height: 8),

                /// Skeleton: category badge
                Container(
                  width: 80,
                  height: 20,
                  decoration: BoxDecoration(
                    color: Colors.grey.shade300,
                    borderRadius: BorderRadius.circular(6),
                  ),
                ),
                const SizedBox(height: 8),

                /// Skeleton: bottom row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Container(
                      width: 100,
                      height: 12,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                    Container(
                      width: 60,
                      height: 12,
                      decoration: BoxDecoration(
                        color: Colors.grey.shade300,
                        borderRadius: BorderRadius.circular(4),
                      ),
                    ),
                  ],
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}
