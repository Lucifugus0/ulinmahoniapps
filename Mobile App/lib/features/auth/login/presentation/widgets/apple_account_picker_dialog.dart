import 'package:flutter/material.dart';
import 'package:timeago/timeago.dart' as timeago;
import '../../../../../core/services/apple_multi_account_storage.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

/// Dialog for picking which Apple account to use for login
/// Shows list of all Apple accounts stored locally with verification status
class AppleAccountPickerDialog extends StatelessWidget {
  final List<AppleAccountData> accounts;
  final Function(AppleAccountData) onAccountSelected;
  final VoidCallback onUseNewAccount;

  const AppleAccountPickerDialog({
    super.key,
    required this.accounts,
    required this.onAccountSelected,
    required this.onUseNewAccount,
  });

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    // Dark mode detection for dialog background and text colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Configure Indonesian locale for timeago
    timeago.setLocaleMessages('id', timeago.IdMessages());

    return AlertDialog(
      backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
      contentPadding: EdgeInsets.zero,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      content: SizedBox(
        width: double.maxFinite,
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Logo Ulin Mahoni at top center
            Container(
              width: double.infinity,
              padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
              // Use dark-aware background for the header section
              color: isDark ? AppColors.surfaceDark : Colors.white,
              child: Column(
                children: [
                  // Logo
                  Image.asset(
                    AppImage.logo,
                    height: 60,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(height: 16),
                  // Title
                  Text(
                    localizations.selectAppleAccount,
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: FontWeight.bold,
                      // Use dark-aware text color for the dialog title
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    localizations.selectAccountForLogin,
                    // Use dark-aware subtitle text color
                    style: TextStyle(fontSize: 14, color: isDark ? Colors.grey[400] : Colors.grey),
                    textAlign: TextAlign.center,
                  ),
                ],
              ),
            ),
            Divider(height: 1, color: isDark ? Colors.white24 : Colors.grey[300]),
            // List of accounts
            Flexible(
              child: Container(
                // Use dark-aware background for the accounts list section
                color: isDark ? AppColors.surfaceDark : Colors.white,
                padding: const EdgeInsets.all(16),
                child: ListView.builder(
                  shrinkWrap: true,
                  itemCount: accounts.length,
                  itemBuilder: (context, index) {
                    final account = accounts[index];
                    return _buildAccountItem(context, account);
                  },
                ),
              ),
            ),
            Divider(height: 1, color: isDark ? Colors.white24 : Colors.grey[300]),
            // "Use new account" option
            Container(
              // Use dark-aware background for the footer section
              color: isDark ? AppColors.surfaceDark : Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
              child: _buildNewAccountOption(context),
            ),
          ],
        ),
      ),
    );
  }

  /// Build account list item with verification status
  Widget _buildAccountItem(BuildContext context, AppleAccountData account) {
    final lastLogin = DateTime.fromMillisecondsSinceEpoch(
      account.lastActiveTimestamp,
    );

    // Get verification status from account
    // If userId exists, we assume verified (successfully logged in before)
    // Can be explicitly set via isEmailVerified field for extra security
    final isVerified = account.isVerified;
    // Dark mode detection for card and text colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      elevation: 2,
      // Use dark-aware card background color
      color: isDark ? AppColors.surfaceDarkElevated : Colors.white,
      child: InkWell(
        onTap: () {
          AppLogger.d(
            'User selected account: ${account.email}',
            'APPLE-ACCOUNT-PICKER',
          );
          Navigator.pop(context);
          onAccountSelected(account);
        },
        borderRadius: BorderRadius.circular(8),
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Row(
            children: [
              // Apple logo icon
              Container(
                width: 48,
                height: 48,
                decoration: BoxDecoration(
                  color: Colors.black,
                  borderRadius: BorderRadius.circular(24),
                ),
                child: const Icon(
                  Icons.apple,
                  color: Colors.white,
                  size: 32,
                ),
              ),
              const SizedBox(width: 16),
              // Account details
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Name
                    Text(
                      account.fullName,
                      style: TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                        // Use dark-aware text color for account name
                        color: isDark ? Colors.white : Colors.black87,
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 4),
                    // Email
                    Text(
                      account.displayEmail,
                      style: TextStyle(
                        fontSize: 14,
                        // Use dark-aware subtle text color for email
                        color: isDark ? Colors.grey[400] : Colors.grey[700],
                      ),
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 8),
                    // Last login time
                    Row(
                      children: [
                        Icon(
                          Icons.access_time,
                          size: 14,
                          // Use dark-aware icon color for timestamp
                          color: isDark ? Colors.grey[400] : Colors.grey[600],
                        ),
                        const SizedBox(width: 4),
                        Flexible(
                          child: Text(
                            timeago.format(lastLogin, locale: 'id'),
                            style: TextStyle(
                              fontSize: 12,
                              // Use dark-aware text color for timestamp
                              color: isDark ? Colors.grey[400] : Colors.grey[600],
                            ),
                            maxLines: 1,
                            overflow: TextOverflow.ellipsis,
                          ),
                        ),
                      ],
                    ),
                    const SizedBox(height: 8),
                    // Verification badge (moved below timestamp)
                    _buildVerificationBadge(isVerified, context),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Build verification status badge
  Widget _buildVerificationBadge(bool isVerified, BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
      decoration: BoxDecoration(
        color: isVerified ? Colors.green[50] : Colors.orange[50],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isVerified ? Colors.green : Colors.orange,
          width: 1,
        ),
      ),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            isVerified ? Icons.verified : Icons.warning_amber,
            size: 16,
            color: isVerified ? Colors.green : Colors.orange,
          ),
          const SizedBox(width: 4),
          Text(
            isVerified ? localizations.verified : localizations.notVerified,
            style: TextStyle(
              fontSize: 12,
              fontWeight: FontWeight.w600,
              color: isVerified ? Colors.green[700] : Colors.orange[700],
            ),
          ),
        ],
      ),
    );
  }

  /// Build "Use new account" option at bottom
  Widget _buildNewAccountOption(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    return InkWell(
      onTap: () {
        AppLogger.d('User chose to use new Apple account', 'APPLE-ACCOUNT-PICKER');
        Navigator.pop(context);
        onUseNewAccount();
      },
      borderRadius: BorderRadius.circular(8),
      child: Padding(
        padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
        child: Row(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.add_circle_outline,
              color: Theme.of(context).primaryColor,
              size: 24,
            ),
            const SizedBox(width: 8),
            Text(
              localizations.useAnotherAppleAccount,
              style: TextStyle(
                fontSize: 16,
                fontWeight: FontWeight.w600,
                color: Theme.of(context).primaryColor,
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Static method to show the dialog
  static Future<void> show({
    required BuildContext context,
    required List<AppleAccountData> accounts,
    required Function(AppleAccountData) onAccountSelected,
    required VoidCallback onUseNewAccount,
  }) async {
    return showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => AppleAccountPickerDialog(
        accounts: accounts,
        onAccountSelected: onAccountSelected,
        onUseNewAccount: onUseNewAccount,
      ),
    );
  }
}
