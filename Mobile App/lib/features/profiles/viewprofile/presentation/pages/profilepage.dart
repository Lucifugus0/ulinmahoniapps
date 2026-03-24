import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:convert';
import 'dart:io';
import '../../../../../core/layout/mainlayout.dart';
import '../widgets/profile_menuitem_dart.dart';
import '../../../../auth/login/provider/auth_provider.dart';
import '../../provider/deactiveaccount_provider.dart';
import '../../provider/uploadid_provider.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/widgets/dialog/imagesourcedialog.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/widgets/dialog/contactdialog.dart';
import '../../../../../core/widgets/appbar.dart';
import '../../../../../core/provider/language_provider.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/services/apple_multi_account_storage.dart';
import '../../../../auth/presentation/pages/account_picker_page.dart';
import '../../../../../core/theme/theme_provider.dart';

class ProfilePage extends ConsumerStatefulWidget {
  const ProfilePage({Key? key}) : super(key: key);

  @override
  ConsumerState<ProfilePage> createState() => _ProfilePageState();
}

class _ProfilePageState extends ConsumerState<ProfilePage> {
  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _onRefresh();
    });
  }

  Future<void> _onRefresh() async {
    await ref.read(authProvider.notifier).refreshUserProfile();

    if (!mounted) return;

    final authState = ref.read(authProvider);
    final error = authState.user.error;

    if (authState.user.hasError && error != null) {
      if (!error.toString().contains('Unauthorized')) {
        showNotificationDialog(
          context,
          'Gagal memperbarui profil: ${error.toString()}',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  Future<void> _launchURL(String urlString) async {
    final Uri url = Uri.parse(urlString);
    if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
      if (mounted) {
        showErrorDialog(
          context,
          'Could not launch $urlString',
        );
      }
    }
  }

  void _showLanguageDialog() {
    showDialog(
      context: context,
      builder: (BuildContext dialogContext) {
        return StatefulBuilder(
          builder: (context, setState) {
            final currentLocale = ref.watch(localeProvider);
            final localizations = AppLocalizations.of(context)!;

            return AlertDialog(
          backgroundColor: Colors.white,
          title: Text(
            currentLocale.languageCode == 'id' ? 'Pilih Bahasa' : 'Select Language',
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 18,
              fontWeight: FontWeight.w600,
              color: Colors.black87,
            ),
          ),
          contentPadding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Logo
              Image.asset(
                AppImage.logo,
                width: 80,
                height: 80,
                errorBuilder: (context, error, stackTrace) {
                  return const Icon(
                    Icons.language,
                    color: AppColors.primaryColor,
                    size: 80,
                  );
                },
              ),
              const SizedBox(height: 24),
              // Indonesia Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    ref.read(localeProvider.notifier).state = const Locale('id');
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: currentLocale.languageCode == 'id'
                        ? AppColors.primaryColor
                        : Colors.white,
                    foregroundColor: currentLocale.languageCode == 'id'
                        ? Colors.white
                        : Colors.black87,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(
                        color: currentLocale.languageCode == 'id'
                            ? AppColors.primaryColor
                            : Colors.grey[300]!,
                        width: 1,
                      ),
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('🇮🇩', style: TextStyle(fontSize: 20)),
                      const SizedBox(width: 12),
                      const Text('Indonesia'),
                      if (currentLocale.languageCode == 'id') ...[
                        const SizedBox(width: 8),
                        const Icon(Icons.check_circle, size: 20),
                      ],
                    ],
                  ),
                ),
              ),
              const SizedBox(height: 12),
              // English Button
              SizedBox(
                width: double.infinity,
                child: ElevatedButton(
                  onPressed: () {
                    ref.read(localeProvider.notifier).state = const Locale('en');
                  },
                  style: ElevatedButton.styleFrom(
                    backgroundColor: currentLocale.languageCode == 'en'
                        ? AppColors.primaryColor
                        : Colors.white,
                    foregroundColor: currentLocale.languageCode == 'en'
                        ? Colors.white
                        : Colors.black87,
                    padding: const EdgeInsets.symmetric(vertical: 14),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                      side: BorderSide(
                        color: currentLocale.languageCode == 'en'
                            ? AppColors.primaryColor
                            : Colors.grey[300]!,
                        width: 1,
                      ),
                    ),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      const Text('🇺🇸', style: TextStyle(fontSize: 20)),
                      const SizedBox(width: 12),
                      const Text('English'),
                      if (currentLocale.languageCode == 'en') ...[
                        const SizedBox(width: 8),
                        const Icon(Icons.check_circle, size: 20),
                      ],
                    ],
                  ),
                ),
              ),
            ],
          ),
          actionsAlignment: MainAxisAlignment.center,
          actions: [
            TextButton(
              onPressed: () {
                context.pop();
              },
              child: Text(
                localizations.cancelButton,
                style: const TextStyle(
                  color: Colors.black54,
                  fontWeight: FontWeight.w500,
                ),
              ),
            ),
          ],
        );
          },
        );
      },
    );
  }


  Future<void> _deactivateAccount() async {
    final localizations = AppLocalizations.of(context)!;
    final user = ref.read(authProvider).user.value;

    if (user == null) {
      showNotificationDialog(
        context,
        localizations.profileDeactivateAccountUserError,
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // Show confirmation dialog
    final bool? confirmDeactivate = await showErrorDialog(
      context,
      localizations.profileDeactivateAccountConfirm,
      dialogActions: <Widget>[
        TextButton(
          onPressed: () {
            context.pop(false);
          },
          child: Text(localizations.cancelButton),
        ),
        TextButton(
          onPressed: () {
            context.pop(true);
          },
          child: Text(
            localizations.profileDeactivateAccountButton,
            style: const TextStyle(color: AppColors.secondaryColor),
          ),
        ),
      ],
      routeName: null,
      buttonText: null,
    );

    if (confirmDeactivate != true || !mounted) return;

    // Show loading dialog
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Center(
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(AppColors.primaryColor),
              ),
              const SizedBox(height: 16),
              Text(
                localizations.profileDeactivateAccountLoading,
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
        ),
      ),
    );

    try {
      final result = await ref.read(deactivateAccountProvider.notifier).deactivateAccount(user.id);

      if (!mounted) return;

      // Close loading dialog
      context.pop();

      if (result['success'] == true) {
        AppLogger.s('Account deactivated successfully', 'DEACTIVATE-ACCOUNT');

        // Logout user
        await ref.read(authProvider.notifier).logout();

        if (mounted) {
          showNotificationDialog(
            context,
            localizations.profileDeactivateAccountSuccess,
            defaultIcon: Icons.check_circle_outline,
            iconColor: Colors.green,
          );

          // Navigate to home after delay
          Future.delayed(const Duration(seconds: 1), () {
            if (mounted) {
              context.go('/home');
            }
          });
        }
      } else {
        AppLogger.e('Failed to deactivate account: ${result['message']}', null, null, 'DEACTIVATE-ACCOUNT');

        if (mounted) {
          showNotificationDialog(
            context,
            '${localizations.profileDeactivateAccountError}: ${result['message']}',
            defaultIcon: Icons.error_outline,
            iconColor: Colors.red,
          );
        }
      }
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error deactivating account', e, stackTrace, 'DEACTIVATE-ACCOUNT');

      if (mounted) {
        context.pop(); // Close loading dialog
        showNotificationDialog(
          context,
          '${localizations.profileDeactivateAccountError}: $e',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  Future<void> _switchAppleAccount() async {
    final localizations = AppLocalizations.of(context)!;

    // Navigate to account picker page
    final result = await Navigator.push<dynamic>(
      context,
      MaterialPageRoute(
        builder: (context) => const AccountPickerPage(),
      ),
    );

    if (!mounted) return;

    if (result == 'add_new') {
      // User wants to add a new account - navigate to login page
      AppLogger.i('User requested to add new Apple account', 'PROFILE');
      context.push('/login');
    } else if (result is AppleAccountData) {
      // User selected an account to switch to
      AppLogger.i('Switching to Apple account: ${result.displayEmail}', 'PROFILE');

      await ref.read(authProvider.notifier).switchAppleAccount(result);

      if (mounted) {
        showNotificationDialog(
          context,
          'Switched to ${result.displayEmail}',
          defaultIcon: Icons.check_circle_outline,
          iconColor: Colors.green,
        );

        // Refresh profile data
        await _onRefresh();
      }
    }
  }

  Future<void> _uploadIdDocument() async {
    final localizations = AppLocalizations.of(context)!;
    final user = ref.read(authProvider).user.value;

    if (user == null) {
      showNotificationDialog(
        context,
        localizations.profileUploadIdNotLoggedIn,
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // Show image source dialog
    final result = await showImageSourceDialog(
      context,
      title: localizations.profileUploadIdTitle,
      message: localizations.profileUploadIdInfo,
      cameraButtonText: localizations.profileUploadIdCamera,
      galleryButtonText: localizations.profileUploadIdGallery,
      cancelButtonText: localizations.cancelButton,
    );

    if (result == null) {
      AppLogger.i('User cancelled image selection', 'UPLOAD-ID');
      return;
    }

    // Pick image based on selection
    final picker = ImagePicker();
    final pickedFile = await picker.pickImage(
      source: result == ImageSourceOption.camera ? ImageSource.camera : ImageSource.gallery,
      maxWidth: 1920,
      maxHeight: 1080,
      imageQuality: 85,
    );

    if (pickedFile == null) {
      AppLogger.i('No image selected', 'UPLOAD-ID');
      return;
    }

    // Show loading dialog
    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Center(
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(16),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const CircularProgressIndicator(
                valueColor: AlwaysStoppedAnimation<Color>(AppColors.primaryColor),
              ),
              const SizedBox(height: 16),
              Text(
                localizations.profileUploadIdUploading,
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
        ),
      ),
    );

    try {
      // Convert image to base64
      final bytes = await pickedFile.readAsBytes();
      final base64Image = base64Encode(bytes);

      AppLogger.i('Uploading ID document for user: ${user.id}', 'UPLOAD-ID');

      // Upload to server
      final repository = ref.read(uploadIdRepositoryProvider);
      final uploadResult = await repository.uploadIdDocument(user.id, base64Image);

      if (!mounted) return;

      // Close loading dialog
      context.pop();

      switch (uploadResult) {
        case Success():
          AppLogger.s('ID document uploaded successfully', 'UPLOAD-ID');

          // Refresh user profile
          await ref.read(authProvider.notifier).refreshUserProfile();

          if (mounted) {
            showNotificationDialog(
              context,
              localizations.profileUploadIdSuccess,
              defaultIcon: Icons.check_circle_outline,
              iconColor: Colors.green,
            );
          }

        case Failure(:final message):
          AppLogger.e('Failed to upload ID document: $message', null, null, 'UPLOAD-ID');

          if (mounted) {
            showNotificationDialog(
              context,
              '${localizations.profileUploadIdError}: $message',
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          }
      }
    } catch (e, stackTrace) {
      AppLogger.e('Unexpected error uploading ID', e, stackTrace, 'UPLOAD-ID');

      if (mounted) {
        context.pop(); // Close loading dialog
        showNotificationDialog(
          context,
          '${localizations.profileUploadIdError}: $e',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final authState = ref.watch(authProvider);
    final user = authState.user.value;
    final displayName = user?.firstName ?? localizations.profileDefaultUsername;
    // Dark/light mode detection for theme-aware glass containers
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return MainLayout(
      showBottomNav: false,
      showNavBar: false,
      currentIndex: 4,
      child: Column(
        children: [
          CustomAppBar(
            title: localizations.profileTitle,
            showBackButton: false,
          ),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _onRefresh,
              color: AppColors.primaryColor,
              child: SingleChildScrollView(
                physics: const AlwaysScrollableScrollPhysics(),
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      // Profile Card (glass-style container)
                      Container(
                        padding: const EdgeInsets.all(20),
                        decoration: BoxDecoration(
                          color: isDark ? AppColors.surfaceDark : Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isDark ? AppColors.glassBorderDark : Colors.transparent,
                            width: 0.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: isDark ? 0.3 : 0.12),
                              blurRadius: 16,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Row(
                          children: [
                            // CircleAvatar with default profile icon
                            CircleAvatar(
                              radius: 32,
                              backgroundColor: AppColors.primaryColor.withValues(alpha: 0.1),
                              child: Icon(
                                Icons.person,
                                size: 32,
                                color: AppColors.primaryColor,
                              ),
                            ),
                            const SizedBox(width: 16),
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Text(
                                    displayName,
                                    style: const TextStyle(
                                      fontSize: 18,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black87,
                                    ),
                                  ),
                                  const SizedBox(height: 4),
                                  Text(
                                    user?.email ?? localizations.profileDefaultEmail,
                                    style: TextStyle(
                                      fontSize: 14,
                                      color: Colors.grey[600],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            // Edit Icon
                            IconButton(
                              onPressed: () {
                                context.push('/updateprofile');
                              },
                              icon: Icon(
                                Icons.edit_outlined,
                                size: 24,
                                color: Colors.grey[700],
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 16),
                      // ID Document Warning
                      if (user != null &&
                          (user.profilePhotoUrl.isEmpty || user.profilePhotoUrl == '') &&
                          (user.profilePhotoPath == null || user.profilePhotoPath!.isEmpty))
                        Container(
                            padding: const EdgeInsets.all(16),
                            decoration: BoxDecoration(
                              color: Colors.orange[50],
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(
                                color: Colors.orange[300]!,
                                width: 1,
                              ),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withValues(alpha: 0.12),
                                  blurRadius: 16,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Row(
                              children: [
                                Icon(
                                  Icons.warning_amber_rounded,
                                  color: Colors.orange[700],
                                  size: 24,
                                ),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      Text(
                                        localizations.profileIdMissingWarning,
                                        style: TextStyle(
                                          fontSize: 14,
                                          fontWeight: FontWeight.bold,
                                          color: Colors.orange[900],
                                        ),
                                      ),
                                      const SizedBox(height: 4),
                                      Text(
                                        localizations.profileIdMissingDesc,
                                        style: TextStyle(
                                          fontSize: 12,
                                          color: Colors.orange[800],
                                        ),
                                      ),
                                    ],
                                  ),
                                ),
                                IconButton(
                                  onPressed: _uploadIdDocument,
                                  icon: Icon(
                                    Icons.arrow_forward_ios,
                                    size: 16,
                                    color: Colors.orange[700],
                                  ),
                                ),
                              ],
                            ),
                          ),

                      // Phone Number Warning
                      if (user != null &&
                          (user.profilePhotoUrl.isEmpty || user.profilePhotoUrl == '') &&
                          (user.profilePhotoPath == null || user.profilePhotoPath!.isEmpty))
                        const SizedBox(height: 12),

                      if (user != null &&
                          (user.phoneNumber == null || user.phoneNumber!.isEmpty))
                        Container(
                          padding: const EdgeInsets.all(16),
                          decoration: BoxDecoration(
                            color: Colors.orange[50],
                            borderRadius: BorderRadius.circular(12),
                            border: Border.all(
                              color: Colors.orange[300]!,
                              width: 1,
                            ),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.12),
                                blurRadius: 16,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Row(
                            children: [
                              Icon(
                                Icons.phone_disabled_outlined,
                                color: Colors.orange[700],
                                size: 24,
                              ),
                              const SizedBox(width: 12),
                              Expanded(
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    Text(
                                      localizations.profilePhoneMissingWarning,
                                      style: TextStyle(
                                        fontSize: 14,
                                        fontWeight: FontWeight.bold,
                                        color: Colors.orange[900],
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      localizations.profilePhoneMissingDesc,
                                      style: TextStyle(
                                        fontSize: 12,
                                        color: Colors.orange[800],
                                      ),
                                    ),
                                  ],
                                ),
                              ),
                              IconButton(
                                onPressed: () {
                                  context.push('/updateprofile');
                                },
                                icon: Icon(
                                  Icons.arrow_forward_ios,
                                  size: 16,
                                  color: Colors.orange[700],
                                ),
                              ),
                            ],
                          ),
                        ),

                      const SizedBox(height: 12),

                      // First Menu Group (glass-style container)
                      Container(
                        decoration: BoxDecoration(
                          color: isDark ? AppColors.surfaceDark : Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isDark ? AppColors.glassBorderDark : Colors.transparent,
                            width: 0.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: isDark ? 0.3 : 0.12),
                              blurRadius: 16,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            MenuItem(
                              icon: Icons.receipt_long_outlined,
                              text: localizations.profileOrderHistoryTitle,
                              subText: localizations.profileOrderHistorySubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () {
                                context.push('/mybooking');
                              },
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.badge_outlined,
                              text: localizations.profileUploadIdTitle,
                              subText: localizations.profileUploadIdSubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: _uploadIdDocument,
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.language_outlined,
                              text: localizations.profileLanguageTitle,
                              subText: localizations.profileLanguageSubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: _showLanguageDialog,
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            // Dark/Light mode toggle
                            Consumer(
                              builder: (context, ref, _) {
                                final themeMode = ref.watch(themeProvider);
                                final isDarkMode = themeMode == ThemeMode.dark;
                                return MenuItem(
                                  icon: isDarkMode ? Icons.dark_mode : Icons.light_mode,
                                  text: isDarkMode ? 'Dark Mode' : 'Light Mode',
                                  subText: isDarkMode ? 'Switch to light mode' : 'Switch to dark mode',
                                  trailing: Switch(
                                    value: isDarkMode,
                                    activeTrackColor: AppColors.primaryColor,
                                    onChanged: (value) {
                                      ref.read(themeProvider.notifier).toggle();
                                    },
                                  ),
                                  onTap: () {
                                    ref.read(themeProvider.notifier).toggle();
                                  },
                                );
                              },
                            ),
                            // Show "Switch Apple Account" only on iOS and if user is signed in with Apple
                            if (Platform.isIOS && user?.appleUserId != null) ...[
                              Divider(height: 1, indent: 60, color: Colors.grey[200]),
                              MenuItem(
                                icon: Icons.swap_horiz_outlined,
                                text: 'Switch Apple Account',
                                subText: 'Switch between multiple Apple accounts',
                                trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                                onTap: _switchAppleAccount,
                              ),
                            ],
                          ],
                        ),
                      ),

                      const SizedBox(height: 16),

                      // Second Menu Group (glass-style container)
                      Container(
                        decoration: BoxDecoration(
                          color: isDark ? AppColors.surfaceDark : Colors.white,
                          borderRadius: BorderRadius.circular(16),
                          border: Border.all(
                            color: isDark ? AppColors.glassBorderDark : Colors.transparent,
                            width: 0.5,
                          ),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: isDark ? 0.3 : 0.12),
                              blurRadius: 16,
                              offset: const Offset(0, 4),
                            ),
                          ],
                        ),
                        child: Column(
                          children: [
                            MenuItem(
                              icon: Icons.phone_outlined,
                              text: localizations.profileContactUsMenu,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () {
                                showContactDialog(context);
                              },
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.shield_outlined,
                              text: localizations.profilePrivacyPolicyTitle,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () {
                                _launchURL('https://web.ulinmahoni.com/privacy-policy');
                              },
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.description_outlined,
                              text: localizations.profileTermsConditionsTitle,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () {
                                _launchURL('https://web.ulinmahoni.com/terms-of-services');
                              },
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.lock_outline,
                              text: localizations.changePasswordTitle,
                              subText: localizations.changePasswordSubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () {
                                context.push('/updatepassword');
                              },
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.person_remove_outlined,
                              text: localizations.profileDeactivateAccountTitle,
                              subText: localizations.profileDeactivateAccountSubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: _deactivateAccount,
                            ),
                            Divider(height: 1, indent: 60, color: Colors.grey[200]),
                            MenuItem(
                              icon: Icons.logout_outlined,
                              text: localizations.logoutTitle,
                              subText: localizations.logoutSubText,
                              trailing: const Icon(Icons.arrow_forward_ios, size: 18, color: Colors.grey),
                              onTap: () async {
                                final bool? confirmLogout = await showErrorDialog(
                                  context,
                                  localizations.confirmLogoutMessage,
                                  dialogActions: <Widget>[
                                    TextButton(
                                      onPressed: () {
                                        context.pop(false);
                                      },
                                      child: Text(localizations.cancelButton),
                                    ),
                                    TextButton(
                                      onPressed: () {
                                        context.pop(true);
                                      },
                                      child: Text(
                                        localizations.logoutButton,
                                        style: const TextStyle(color: AppColors.secondaryColor),
                                      ),
                                    ),
                                  ],
                                  routeName: null,
                                  buttonText: null,
                                );

                                if (context.mounted && confirmLogout == true) {
                                  try {
                                    await ref.read(authProvider.notifier).logout();
                                    if (context.mounted) {
                                      context.go('/home');
                                    }
                                  } catch (e) {
                                    if (context.mounted) {
                                      showErrorDialog(
                                        context,
                                        '${localizations.logoutErrorMessage}: ${e.toString()}',
                                      );
                                    }
                                  }
                                }
                              },
                            ),
                          ],
                        ),
                      ),

                      const SizedBox(height: 120),
                    ],
                  ),
                ),
              ),
            ),
          ),
        ],
      ),
    );
  }
}
