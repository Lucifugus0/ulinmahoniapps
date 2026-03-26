import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../auth/login/provider/auth_provider.dart';
import '../../provider/updatepassword_provider.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../widgets/passwordfield.dart';
import 'package:ulinmahoniapps/core/widgets/biometric_auth.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';

class UpdatePasswordPage extends ConsumerStatefulWidget {
  const UpdatePasswordPage({Key? key}) : super(key: key);

  @override
  ConsumerState<UpdatePasswordPage> createState() => _UpdatePasswordPageState();
}

class _UpdatePasswordPageState extends ConsumerState<UpdatePasswordPage> {
  final _oldPasswordController = TextEditingController();
  final _newPasswordController = TextEditingController();
  final _confirmNewPasswordController = TextEditingController();
  final BiometricAuthService _biometricAuthService = BiometricAuthService();

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

    ref.listen<UpdatePasswordState>(
      updatePasswordNotifierProvider,
          (previous, current) {
        if (current is UpdatePasswordSuccess) {
          showNotificationDialog(
            context,
            localizations.updatePasswordSuccessMessage,
            iconColor: Colors.green,
            defaultIcon: Icons.check_circle_outline,
          );

          // Navigate to profile after delay
          Future.delayed(const Duration(seconds: 2), () {
            if (context.mounted) {
              context.go('/profile');
              updatePasswordNotifier.resetState();
            }
          });
        } else if (current is UpdatePasswordError) {
          showErrorDialog(context, current.message);
          updatePasswordNotifier.resetState();
        }
      },
    );

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.white,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
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
                    style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),
                  passwordField(
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
                    style: const TextStyle(
                      fontSize: 12,
                      color: Colors.grey,
                    ),
                  ),
                  const SizedBox(height: 16),
                  passwordField(
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
                    style: const TextStyle(
                      fontSize: 12,
                      color: Colors.grey,
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

                      final bool didAuthenticate = await _biometricAuthService.authenticateOnLoad(context);
                      if (!didAuthenticate) {
                        return;
                      }

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