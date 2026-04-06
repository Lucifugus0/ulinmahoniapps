import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../features/auth/login/provider/auth_provider.dart';
import 'dialog/errordialog.dart';
import 'dialog/notificationdialog.dart';
import '../constants/appcolor_constants.dart';
import '../utils/app_logger.dart';
import '../theme/glass_theme.dart';

/// Glass-effect sticky bottom CTA bar for booking/browsing.
/// Adapts to dark/light mode using Theme.of(context).
class BottomContactBar extends ConsumerWidget {
  final bool showBookingButton;
  final bool isButtonEnabled;
  final VoidCallback? onButtonPressed;
  final dynamic roomData;
  final String priceText;
  final String? durationType;
  final String? warningText;
  final String? priceLabel;

  const BottomContactBar({
    super.key,
    this.showBookingButton = false,
    this.isButtonEnabled = true,
    this.onButtonPressed,
    this.roomData,
    this.priceText = "100.000",
    this.durationType = "Hari",
    this.warningText,
    this.priceLabel,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return ClipRect(
      child: BackdropFilter(
        filter: GlassTheme.standardBlur,
        child: Container(
          padding: const EdgeInsets.fromLTRB(16, 8, 16, 20),
          decoration: BoxDecoration(
            // Glass surface — higher opacity in light mode for readability
            color: isDark
                ? const Color(0xFF1F2937).withValues(alpha: 0.45)
                : Colors.white.withValues(alpha: 0.85),
            border: Border(
              top: BorderSide(
                color: isDark
                    ? GlassTheme.glassBorderDark
                    : GlassTheme.glassBorderLight,
                width: GlassTheme.borderWidth,
              ),
            ),
            boxShadow: [
              BoxShadow(
                color: isDark
                    ? Colors.black.withValues(alpha: 0.3)
                    : Colors.grey.withValues(alpha: 0.2),
                spreadRadius: 1,
                blurRadius: 8,
                offset: const Offset(0, -2),
              ),
            ],
          ),
          child: Row(
            children: [
              Expanded(
                flex: 2,
                child: Column(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      priceLabel ?? localizations.contactBarPriceLabel,
                      style: Theme.of(context).textTheme.labelSmall?.copyWith(
                        color: isDark
                            ? AppColors.fontColorDarkMuted
                            : Colors.black54,
                        fontSize: 12,
                      ),
                    ),
                    RichText(
                      text: TextSpan(
                        style: Theme.of(context).textTheme.titleLarge?.copyWith(
                          color: isDark ? AppColors.fontColorDark : Colors.black,
                          fontSize: 18,
                        ),
                        children: [
                          TextSpan(
                            text: priceText,
                            style: TextStyle(
                              // Bright orange for readability on dark/glass backgrounds
                              color: isDark ? const Color(0xFFFF9500) : AppColors.primaryColor,
                              fontWeight: FontWeight.bold,
                            ),
                          ),
                          if (durationType != null && durationType!.isNotEmpty)
                            TextSpan(
                              text: " / $durationType",
                              style: TextStyle(
                                fontWeight: FontWeight.w500,
                                fontSize: 14,
                                color: isDark ? AppColors.fontColorDark : Colors.black,
                              ),
                            ),
                        ],
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(width: 5),
              Expanded(
                flex: 2,
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  crossAxisAlignment: CrossAxisAlignment.end,
                  children: [
                    if (warningText != null && showBookingButton)
                      Padding(
                        padding: const EdgeInsets.only(bottom: 8.0),
                        child: Container(
                          padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                          decoration: BoxDecoration(
                            color: isButtonEnabled
                                ? (isDark ? Colors.green.shade900.withValues(alpha: 0.4) : Colors.green.shade50)
                                : (isDark ? Colors.orange.shade900.withValues(alpha: 0.4) : Colors.orange.shade50),
                            borderRadius: BorderRadius.circular(8),
                            border: Border.all(
                              color: isButtonEnabled
                                  ? (isDark ? Colors.green.shade700 : Colors.green.shade200)
                                  : (isDark ? Colors.orange.shade700 : Colors.orange.shade200),
                              width: 1,
                            ),
                          ),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              Icon(
                                isButtonEnabled
                                    ? Icons.check_circle_outline
                                    : Icons.warning_amber_rounded,
                                size: 16,
                                color: isButtonEnabled
                                    ? Colors.green.shade700
                                    : Colors.orange.shade700,
                              ),
                              const SizedBox(width: 6),
                              Flexible(
                                child: Text(
                                  warningText!,
                                  style: Theme.of(context).textTheme.bodySmall?.copyWith(
                                    color: isDark ? AppColors.fontColorDark : Colors.black87,
                                    fontSize: 11,
                                    fontWeight: FontWeight.w500,
                                  ),
                                  textAlign: TextAlign.left,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ElevatedButton.icon(
                      onPressed: () {
                        if (showBookingButton) {
                          if (isButtonEnabled) {
                            final authState = ref.read(authProvider);
                            final user = authState.user.value;
                            // Check if user is logged in
                            if (user == null) {
                              showErrorDialog(
                                context,
                                localizations.contactBarLoginRequired,
                                routeName: '/login',
                                buttonText: localizations.contactBarLoginButton,
                              );
                              return;
                            }
                            // Check if user has phone number
                            if (user.phoneNumber.isEmpty) {
                              showErrorDialog(
                                context,
                                localizations.contactBarPhoneRequired,
                                routeName: '/updateprofile',
                                buttonText: localizations.contactBarPhoneButton,
                              );
                              return;
                            }
                            onButtonPressed?.call();
                          } else {
                            showNotificationDialog(
                              context,
                              localizations.contactBarIncompleteOrder,
                              defaultIcon: Icons.error_outline,
                              iconColor: AppColors.secondaryColor,
                            );
                          }
                        } else {
                          AppLogger.d('Browse All Button Pressed via bottomcontactbar', 'BOTTOM-BAR');
                          context.push('/browse-all');
                        }
                      },
                      style: ElevatedButton.styleFrom(
                        // Use primaryAdaptive for the booking/browse button background
                        backgroundColor: showBookingButton
                            ? (isButtonEnabled ? AppColors.primaryAdaptive(context) : Colors.grey)
                            : AppColors.primaryAdaptive(context),
                        padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 12),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      icon: Icon(
                        showBookingButton ? Icons.shopping_cart : Icons.house,
                        color: Colors.white,
                        size: Theme.of(context).iconTheme.size ?? 22,
                      ),
                      label: Text(
                        showBookingButton
                            ? localizations.contactBarBookNowButton
                            : localizations.contactBarOtherPropertiesButton,
                        style: Theme.of(context).textTheme.labelLarge?.copyWith(
                          color: Colors.white,
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                        ),
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
