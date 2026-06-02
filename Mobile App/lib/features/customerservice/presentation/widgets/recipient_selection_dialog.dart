import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/features/mybooking/mybooking/model/mybooking_model.dart';

/// Dialog for selecting chat recipient (FO or HO)
class RecipientSelectionDialog extends StatelessWidget {
  final MyBookingModel booking;
  final Function(String recipientType) onRecipientSelected;

  const RecipientSelectionDialog({
    super.key,
    required this.booking,
    required this.onRecipientSelected,
  });

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final propertyName = booking.propertyName;
    final orderId = booking.orderId;
    // Dark mode detection for dialog background and text colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Dialog(
      // Use dark-aware dialog background color
      backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Header Logo
            Image.asset(
              'assets/images/ulinmahonilogo.png',
              width: 80,
              height: 80,
              fit: BoxFit.contain,
            ),
            const SizedBox(height: 16),

            // Title
            Text(
              localizations.csSelectRecipient,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                // Use dark-aware title text color
                color: isDark ? Colors.white : Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),

            // Booking info
            Text(
              orderId,
              style: TextStyle(
                fontSize: 14,
                // Use dark-aware subtitle text color
                color: isDark ? Colors.grey[400] : Colors.grey.shade600,
                fontWeight: FontWeight.w500,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),

            // Recipient options
            _RecipientOption(
              icon: Icons.store,
              title: localizations.csFrontOffice,
              subtitle: propertyName,
              color: AppColors.secondaryColor,
              onTap: () => onRecipientSelected('fo'),
            ),
            const SizedBox(height: 12),
            _RecipientOption(
              icon: Icons.business_center,
              title: localizations.csHeadOffice,
              subtitle: localizations.csHeadOfficeDesc,
              // Use primaryAdaptive for dark/light mode compatibility
              color: AppColors.primaryAdaptive(context),
              onTap: () => onRecipientSelected('ho'),
            ),
            const SizedBox(height: 16),

            // Cancel button
            TextButton(
              onPressed: () => Navigator.of(context).pop(),
              child: Text(
                localizations.cancel,
                style: TextStyle(
                  fontSize: 14,
                  // Use dark-aware cancel button text color
                  color: isDark ? Colors.grey[400] : Colors.grey.shade600,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _RecipientOption extends StatelessWidget {
  final IconData icon;
  final String title;
  final String subtitle;
  final Color color;
  final VoidCallback onTap;

  const _RecipientOption({
    required this.icon,
    required this.title,
    required this.subtitle,
    required this.color,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return Material(
      color: Colors.transparent,
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Container(
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(
            color: color.withValues(alpha: 0.05),
            borderRadius: BorderRadius.circular(12),
            border: Border.all(
              color: color.withValues(alpha: 0.3),
              width: 1.5,
            ),
          ),
          child: Row(
            children: [
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: color,
                  borderRadius: BorderRadius.circular(10),
                ),
                child: Icon(
                  icon,
                  size: 24,
                  color: Colors.white,
                ),
              ),
              const SizedBox(width: 16),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      title,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.bold,
                        color: color,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      subtitle,
                      style: TextStyle(
                        fontSize: 13,
                        color: Colors.grey.shade600,
                      ),
                      maxLines: 2,
                      overflow: TextOverflow.ellipsis,
                    ),
                  ],
                ),
              ),
              Icon(
                Icons.arrow_forward_ios,
                size: 16,
                color: color,
              ),
            ],
          ),
        ),
      ),
    );
  }
}
