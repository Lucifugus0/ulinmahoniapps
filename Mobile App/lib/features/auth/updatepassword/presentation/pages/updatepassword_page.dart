import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../auth/login/provider/auth_provider.dart';
import '../../provider/updatepassword_provider.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../widgets/passwordfield.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/theme/theme_provider.dart';
import '../../../../../core/provider/language_provider.dart';

class UpdatePasswordPage extends ConsumerStatefulWidget {
  const UpdatePasswordPage({Key? key}) : super(key: key);

  @override
  ConsumerState<UpdatePasswordPage> createState() => _UpdatePasswordPageState();
}

class _UpdatePasswordPageState extends ConsumerState<UpdatePasswordPage> {
  final _oldPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmNewPasswordController = TextEditingController();

  bool _obscureOldPassword = true;
  bool _obscureNewPassword = true;
  bool _obscureConfirmNewPassword = true;

  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _oldPasswordController.dispose();
    _newPasswordController.dispose();
    _confirmNewPasswordController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final updatePasswordState = ref.watch(updatePasswordNotifierProvider);
    final updatePasswordNotifier = ref.read(updatePasswordNotifierProvider.notifier);
    final currentUserId = ref.read(authProvider).user.value?.id;
    // Dark mode detection for background and AppBar colors
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final currentLocale = ref.watch(localeProvider);

    ref.listen<UpdatePasswordState>(
      updatePasswordNotifierProvider,
          (previous, current) {
        if (current is UpdatePasswordSuccess) {
          // Navigate to profile when user taps OK — avoids GoRouter stack conflict with Future.delayed
          showNotificationDialog(
            context,
            localizations.updatePasswordSuccessMessage,
            iconColor: Colors.green,
            defaultIcon: Icons.check_circle_outline,
            onOkPressed: () {
              updatePasswordNotifier.resetState();
              if (context.mounted) {
                context.go('/profile');
              }
            },
          );
        } else if (current is UpdatePasswordError) {
          showErrorDialog(context, current.message);
          updatePasswordNotifier.resetState();
        }
      },
    );

    return Scaffold(
      // Dark-aware scaffold background
      backgroundColor: isDark ? AppColors.backgroundDark : Colors.white,
      appBar: AppBar(
        // Dark-aware AppBar background
        backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
        elevation: 0,
        iconTheme: IconThemeData(
          // Dark-aware back button color
          color: isDark ? Colors.white : Colors.black,
        ),
        // Language + theme toggle actions (top right)
        actions: [
          // Dark/light mode toggle button
          GestureDetector(
            onTap: () => ref.read(themeProvider.notifier).toggle(),
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10),
              padding: const EdgeInsets.all(6),
              decoration: BoxDecoration(
                color: isDark
                    ? Colors.white.withValues(alpha: 0.1)
                    : Colors.grey.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isDark ? Colors.white24 : Colors.grey[300]!,
                  width: 0.5,
                ),
              ),
              child: Icon(
                isDark ? Icons.light_mode_outlined : Icons.dark_mode_outlined,
                color: isDark ? Colors.white70 : Colors.black54,
                size: 18,
              ),
            ),
          ),
          const SizedBox(width: 8),
          // Language cycle button: ID → EN → ZH → ID
          GestureDetector(
            onTap: () {
              final code = currentLocale.languageCode;
              final newLocale = code == 'id'
                  ? const Locale('en')
                  : code == 'en'
                      ? const Locale('zh')
                      : const Locale('id');
              ref.read(localeProvider.notifier).state = newLocale;
            },
            child: Container(
              margin: const EdgeInsets.symmetric(vertical: 10),
              padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
              decoration: BoxDecoration(
                color: isDark
                    ? Colors.white.withValues(alpha: 0.1)
                    : Colors.grey.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(20),
                border: Border.all(
                  color: isDark ? Colors.white24 : Colors.grey[300]!,
                  width: 0.5,
                ),
              ),
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(
                    Icons.language,
                    color: isDark ? Colors.white70 : Colors.black54,
                    size: 14,
                  ),
                  const SizedBox(width: 4),
                  Text(
                    currentLocale.languageCode.toUpperCase(),
                    style: TextStyle(
                      color: isDark ? Colors.white70 : Colors.black54,
                      fontSize: 12,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ],
              ),
            ),
          ),
          const SizedBox(width: 12),
        ],
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 24.0),
            child: Form(
              key: _formKey,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  Image.asset(AppImage.logo, height: 120),
                  const SizedBox(height: 24),
                  Text(
                    localizations.updatePasswordTitle,
                    // Dark-aware title color
                    style: TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),
                  // Pass context for dark mode support in passwordField
                  passwordField(
                    context,
                    localizations.oldPasswordLabel,
                    _oldPasswordController,
                    obscure: _obscureOldPassword,
                    onToggleVisibility: () {
                      setState(() {
                        _obscureOldPassword = !_obscureOldPassword;
                      });
                    },
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return localizations.oldPasswordEmptyError;
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 16),
                  passwordField(
                    context,
                    localizations.newPasswordLabel,
                    _newPasswordController,
                    obscure: _obscureNewPassword,
                    onToggleVisibility: () {
                      setState(() {
                        _obscureNewPassword = !_obscureNewPassword;
                      });
                    },
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return localizations.newPasswordEmptyError;
                      }
                      if (value.length < 8) {
                        return localizations.newPasswordLengthError;
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 4),
                  Text(
                    localizations.newPasswordLengthInfo,
                    style: TextStyle(
                      fontSize: 12,
                      // Dark-aware hint text color
                      color: isDark ? Colors.grey[400] : Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 16),
                  passwordField(
                    context,
                    localizations.confirmNewPasswordLabel,
                    _confirmNewPasswordController,
                    obscure: _obscureConfirmNewPassword,
                    onToggleVisibility: () {
                      setState(() {
                        _obscureConfirmNewPassword = !_obscureConfirmNewPassword;
                      });
                    },
                    validator: (value) {
                      if (value == null || value.isEmpty) {
                        return localizations.confirmNewPasswordEmptyError;
                      }
                      if (value != _newPasswordController.text) {
                        return localizations.newPasswordMismatchError;
                      }
                      return null;
                    },
                  ),
                  const SizedBox(height: 4),
                  Text(
                    localizations.confirmNewPasswordInfo,
                    style: TextStyle(
                      fontSize: 12,
                      // Dark-aware hint text color
                      color: isDark ? Colors.grey[400] : Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 24),
                  ElevatedButton(
                    onPressed: updatePasswordState is UpdatePasswordLoading
                        ? null
                        : () async {
                      if (!_formKey.currentState!.validate()) {
                        showErrorDialog(context, localizations.updatePasswordFormError);
                        return;
                      }

                      if (currentUserId == null) {
                        showErrorDialog(context, localizations.updatePasswordUserIdError);
                        return;
                      }

                      // Biometric removed — handled at login level only

                      final oldPassword = _oldPasswordController.text.trim();
                      final newPassword = _newPasswordController.text.trim();
                      final confirmNewPassword = _confirmNewPasswordController.text.trim();

                      await updatePasswordNotifier.updatePassword(
                        oldPassword: oldPassword,
                        newPassword: newPassword,
                        confirmNewPassword: confirmNewPassword,
                      );
                    },
                    style: ElevatedButton.styleFrom(
                      // Use primaryAdaptive for the update password button background
                      backgroundColor: AppColors.primaryAdaptive(context),
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      textStyle: const TextStyle(fontSize: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                      // Dark-aware disabled state
                      disabledBackgroundColor: isDark ? Colors.grey[700] : Colors.grey[300],
                    ),
                    child: updatePasswordState is UpdatePasswordLoading
                        ? const SizedBox(
                      width: 24,
                      height: 24,
                      child: CircularProgressIndicator(
                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                      ),
                    )
                        : Text(localizations.updatePasswordButton),
                  ),
                  const SizedBox(height: 48),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
