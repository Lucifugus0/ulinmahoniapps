import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/utils/formatcurrency.dart';

/// Displays per-date pricing breakdown for daily bookings.
/// Shows each day with its price type (weekday/weekend/holiday/etc.) and price.
class DailyPriceBreakdown extends StatelessWidget {
  final List<dynamic> breakdown;
  final double totalPrice;

  const DailyPriceBreakdown({
    super.key,
    required this.breakdown,
    required this.totalPrice,
  });

  @override
  Widget build(BuildContext context) {
    if (breakdown.isEmpty) return const SizedBox.shrink();

    final isDark = Theme.of(context).brightness == Brightness.dark;
    final textTheme = Theme.of(context).textTheme;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Per-day price rows
        ...breakdown.map((item) {
          final map = item as Map<String, dynamic>;
          final date = map['date'] as String? ?? '';
          final price = (map['price'] as num?)?.toDouble() ?? 0;
          final type = map['type'] as String? ?? 'weekday';
          final dayName = map['day_name'] as String? ?? '';
          final label = map['label'] as String?;

          // Format date: "15 Apr" style
          String formattedDate = date;
          try {
            final parsed = DateTime.parse(date);
            formattedDate = DateFormat('dd MMM').format(parsed);
          } catch (_) {}

          // Badge color based on price type
          final badgeColor = _getBadgeColor(type, isDark);
          final badgeText = label ?? _getTypeLabel(type);

          return Padding(
            padding: const EdgeInsets.symmetric(vertical: 3),
            child: Row(
              children: [
                // Date + day name
                SizedBox(
                  width: 80,
                  child: Text(
                    formattedDate,
                    style: textTheme.bodySmall?.copyWith(
                      color: isDark ? Colors.grey[400] : Colors.grey[600],
                    ),
                  ),
                ),
                // Day name
                SizedBox(
                  width: 40,
                  child: Text(
                    _getShortDayName(dayName),
                    style: textTheme.bodySmall?.copyWith(
                      color: isDark ? Colors.grey[400] : Colors.grey[600],
                    ),
                  ),
                ),
                // Price type badge
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 2),
                  decoration: BoxDecoration(
                    color: badgeColor.withValues(alpha: 0.15),
                    borderRadius: BorderRadius.circular(4),
                  ),
                  child: Text(
                    badgeText,
                    style: textTheme.labelSmall?.copyWith(
                      color: badgeColor,
                      fontSize: 10,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
                const Spacer(),
                // Price
                Text(
                  formatCurrency(price),
                  style: textTheme.bodySmall?.copyWith(
                    fontWeight: FontWeight.w500,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
              ],
            ),
          );
        }),
        // Total row
        const Divider(height: 16),
        Row(
          mainAxisAlignment: MainAxisAlignment.spaceBetween,
          children: [
            Text(
              'Total',
              style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.bold),
            ),
            Text(
              formatCurrency(totalPrice),
              style: textTheme.bodyMedium?.copyWith(
                fontWeight: FontWeight.bold,
                color: isDark ? const Color(0xFFFF9500) : AppColors.primaryColor,
              ),
            ),
          ],
        ),
      ],
    );
  }

  /// Badge color for each price type
  Color _getBadgeColor(String type, bool isDark) {
    switch (type.toLowerCase()) {
      case 'weekend':
        return Colors.orange;
      case 'holiday':
        return Colors.red;
      case 'high_season':
        return Colors.purple;
      case 'low_season':
        return Colors.blue;
      default:
        return isDark ? Colors.green.shade400 : Colors.green.shade700;
    }
  }

  /// Label for price type
  String _getTypeLabel(String type) {
    switch (type.toLowerCase()) {
      case 'weekend':
        return 'Weekend';
      case 'holiday':
        return 'Holiday';
      case 'high_season':
        return 'High Season';
      case 'low_season':
        return 'Low Season';
      default:
        return 'Weekday';
    }
  }

  /// Short day name (Mon, Tue, etc.)
  String _getShortDayName(String dayName) {
    if (dayName.length >= 3) return dayName.substring(0, 3);
    return dayName;
  }
}
