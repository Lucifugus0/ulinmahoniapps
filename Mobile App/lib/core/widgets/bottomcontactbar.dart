import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../features/auth/login/provider/auth_provider.dart';
import 'dialog/errordialog.dart';
import 'dialog/notificationdialog.dart';
import '../constants/appcolor_constants.dart';
import '../utils/app_logger.dart';

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
    return Container(
      padding: const EdgeInsets.fromLTRB(16, 8, 16, 20),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 2,
            blurRadius: 5,
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
                    color: Colors.black54,
                    fontSize: 12,
                  ),
                ),
                RichText(
                  text: TextSpan(
                    style: Theme.of(context).textTheme.titleLarge?.copyWith(
                      color: Colors.black,
                      fontSize: 12,
                    ),
                    children: [
                      TextSpan(
                        text: priceText,
                        style: const TextStyle(
                          color: AppColors.secondaryColor,
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      if (durationType != null && durationType!.isNotEmpty)
                        TextSpan(
                          text: " / $durationType",
                          style: const TextStyle(
                            fontWeight: FontWeight.w500,
                          ),
                        ),
                    ],
                  ),
                ),
                Container(
                  margin: const EdgeInsets.only(top: 6),
                  decoration: BoxDecoration(
                    color: AppColors.primaryColor.withOpacity(0.2),
                    borderRadius: BorderRadius.circular(5),
                    border: Border.all(color: AppColors.primaryColor, width: 1),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Icon(
                        Icons.local_offer,
                        color: AppColors.primaryColor,
                        size: Theme.of(context).iconTheme.size ?? 16,
                      ),
                      const SizedBox(width: 2),
                      Flexible(
                        child: Text(
                          localizations.contactBarSafetyLabel, 
                          style: Theme.of(context).textTheme.bodySmall?.copyWith(
                            color: AppColors.primaryColor,
                            fontSize: 10,
                          ),
                          overflow: TextOverflow.ellipsis,
                          maxLines: 1,
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
                            ? Colors.green.shade50
                            : Colors.orange.shade50,
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(
                          color: isButtonEnabled
                              ? Colors.green.shade200
                              : Colors.orange.shade200,
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
                                color: Colors.black87,
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
                    backgroundColor: showBookingButton
                        ? (isButtonEnabled ? AppColors.primaryColor : Colors.grey)
                        : AppColors.primaryColor,
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
    );
  }
}