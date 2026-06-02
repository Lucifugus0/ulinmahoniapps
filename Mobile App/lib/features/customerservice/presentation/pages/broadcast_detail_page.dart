import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/widgets/appbar.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../features/auth/login/provider/auth_provider.dart';
import '../../model/broadcast_model.dart';
import '../../provider/ticket_provider.dart';

/// BroadcastDetailPage — Read-only broadcast detail page.
/// Shows the full broadcast message, sender info, metadata, and audience details.
/// Marks the broadcast as read when opened.
class BroadcastDetailPage extends ConsumerStatefulWidget {
  /// The broadcast ID to display
  final int broadcastId;

  const BroadcastDetailPage({
    super.key,
    required this.broadcastId,
  });

  @override
  ConsumerState<BroadcastDetailPage> createState() =>
      _BroadcastDetailPageState();
}

class _BroadcastDetailPageState extends ConsumerState<BroadcastDetailPage> {
  /// The loaded broadcast detail (fetched directly from repository)
  BroadcastModel? _broadcast;

  /// Whether the page is still loading
  bool _isLoading = true;

  /// Error message if loading fails
  String? _error;

  @override
  void initState() {
    super.initState();
    _loadBroadcast();
  }

  /// Load broadcast detail from the repository.
  /// This also marks the broadcast as read on the server side.
  Future<void> _loadBroadcast() async {
    final user = ref.read(authProvider).user.value;
    if (user == null) {
      setState(() {
        _isLoading = false;
        _error = 'Not logged in';
      });
      return;
    }

    try {
      final repo = ref.read(ticketRepositoryProvider);
      final broadcast = await repo.getBroadcast(widget.broadcastId, user.id);

      if (mounted) {
        setState(() {
          _broadcast = broadcast;
          _isLoading = false;
        });

        /// Invalidate broadcast list to reflect read status change
        ref.invalidate(broadcastListProvider);
      }
    } catch (e, s) {
      AppLogger.e('Error loading broadcast', e, s, 'BROADCAST-DETAIL');
      if (mounted) {
        setState(() {
          _isLoading = false;
          _error = 'Failed to load broadcast';
        });
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      backgroundColor:
          isDark ? AppColors.backgroundDark : AppColors.backgroundLight,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(kToolbarHeight),
        child: CustomAppBar(
          title: 'Broadcast',
          showBackButton: true,
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _error != null
              ? _buildErrorState(isDark)
              : _broadcast != null
                  ? _buildContent(_broadcast!, isDark)
                  : _buildErrorState(isDark),
    );
  }

  /// Build the broadcast detail content.
  /// Includes header card with broadcast number and sender badge, title,
  /// full message body, and metadata section.
  Widget _buildContent(BroadcastModel broadcast, bool isDark) {
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final mutedColor =
        isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted;
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          /// Header card with broadcast number and sender type badge
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: cardBg,
              borderRadius: BorderRadius.circular(14),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                /// Broadcast number and sender type badge row
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      broadcast.broadcastNumber,
                      style: TextStyle(
                        fontSize: 13,
                        fontWeight: FontWeight.w500,
                        color: mutedColor,
                      ),
                    ),

                    /// Sender type badge (HQ or Front Desk)
                    Container(
                      padding: const EdgeInsets.symmetric(
                          horizontal: 10, vertical: 4),
                      decoration: BoxDecoration(
                        color: (broadcast.isHQ
                                ? AppColors.indigo
                                : AppColors.teal)
                            .withValues(alpha: 0.15),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Text(
                        broadcast.isHQ ? 'HQ' : 'FRONT DESK',
                        style: TextStyle(
                          fontSize: 12,
                          fontWeight: FontWeight.w600,
                          color: broadcast.isHQ
                              ? AppColors.indigo
                              : AppColors.teal,
                        ),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                /// Broadcast title (large text)
                Text(
                  broadcast.title,
                  style: TextStyle(
                    fontSize: 22,
                    fontWeight: FontWeight.bold,
                    color: textColor,
                    height: 1.3,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),

          /// Message body (full text, scrollable within parent)
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: cardBg,
              borderRadius: BorderRadius.circular(14),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Text(
              broadcast.messageText,
              style: TextStyle(
                fontSize: 15,
                color: textColor,
                height: 1.6,
              ),
            ),
          ),
          const SizedBox(height: 16),

          /// Metadata section: sender name, audience type, recipient count, sent date
          Container(
            width: double.infinity,
            padding: const EdgeInsets.all(16),
            decoration: BoxDecoration(
              color: cardBg,
              borderRadius: BorderRadius.circular(14),
              boxShadow: [
                BoxShadow(
                  color: Colors.black.withValues(alpha: 0.05),
                  blurRadius: 8,
                  offset: const Offset(0, 2),
                ),
              ],
            ),
            child: Column(
              children: [
                /// Sender name metadata row
                _buildMetadataRow(
                  icon: Icons.person_outline,
                  label: 'Sender',
                  value: broadcast.sender?.displayName ?? 'Admin',
                  isDark: isDark,
                ),
                const SizedBox(height: 12),

                /// Audience type metadata row
                _buildMetadataRow(
                  icon: Icons.group_outlined,
                  label: 'Audience',
                  value: broadcast.audience.isNotEmpty
                      ? broadcast.audience
                          .replaceAll('_', ' ')
                          .split(' ')
                          .map((w) => w.isNotEmpty
                              ? '${w[0].toUpperCase()}${w.substring(1)}'
                              : '')
                          .join(' ')
                      : 'All',
                  isDark: isDark,
                ),
                const SizedBox(height: 12),

                /// Recipient count metadata row
                _buildMetadataRow(
                  icon: Icons.people_alt_outlined,
                  label: 'Recipients',
                  value: '${broadcast.recipientCount}',
                  isDark: isDark,
                ),
                const SizedBox(height: 12),

                /// Property name metadata row (if applicable)
                if (broadcast.property != null) ...[
                  _buildMetadataRow(
                    icon: Icons.apartment_outlined,
                    label: 'Property',
                    value: broadcast.property!.name,
                    isDark: isDark,
                  ),
                  const SizedBox(height: 12),
                ],

                /// Sent date metadata row
                _buildMetadataRow(
                  icon: Icons.schedule_outlined,
                  label: 'Sent',
                  value: broadcast.formattedDate,
                  isDark: isDark,
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  /// Build a single metadata row with icon, label, and value text.
  Widget _buildMetadataRow({
    required IconData icon,
    required String label,
    required String value,
    required bool isDark,
  }) {
    final mutedColor =
        isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted;
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;

    return Row(
      children: [
        Icon(icon, size: 18, color: mutedColor),
        const SizedBox(width: 10),
        Text(
          '$label:',
          style: TextStyle(
            fontSize: 13,
            color: mutedColor,
          ),
        ),
        const SizedBox(width: 8),
        Expanded(
          child: Text(
            value,
            style: TextStyle(
              fontSize: 14,
              fontWeight: FontWeight.w500,
              color: textColor,
            ),
            textAlign: TextAlign.right,
          ),
        ),
      ],
    );
  }

  /// Build an error state with retry button
  Widget _buildErrorState(bool isDark) {
    return Center(
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Icon(Icons.error_outline, size: 80, color: Colors.red.shade300),
          const SizedBox(height: 16),
          Text(
            _error ?? 'Broadcast not found',
            style: TextStyle(
              fontSize: 16,
              color: isDark ? Colors.grey[400] : Colors.grey[700],
              fontWeight: FontWeight.w500,
            ),
          ),
          const SizedBox(height: 24),
          ElevatedButton.icon(
            onPressed: () {
              setState(() {
                _isLoading = true;
                _error = null;
              });
              _loadBroadcast();
            },
            icon: const Icon(Icons.refresh),
            label: const Text('Retry'),
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primaryColor,
              foregroundColor: Colors.white,
              padding:
                  const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
