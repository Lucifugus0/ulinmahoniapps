import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/widgets/dialog/errordialog.dart';
import '../../provider/forgotpassword_provider.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/widgets/languagedropdown.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';

class ForgotPasswordPage extends ConsumerStatefulWidget {
  const ForgotPasswordPage({super.key});

  @override
  ConsumerState<ForgotPasswordPage> createState() => _ForgotPasswordPageState();
}

class _ForgotPasswordPageState extends ConsumerState<ForgotPasswordPage> {
  final TextEditingController _emailController = TextEditingController();
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  @override
  void dispose() {
    _emailController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    
    final localizations = AppLocalizations.of(context)!;

    final forgotPasswordState = ref.watch(forgotPasswordControllerProvider);
    final forgotPasswordNotifier = ref.read(forgotPasswordControllerProvider.notifier);

    ref.listen<ForgotPasswordState>(
      forgotPasswordControllerProvider,
          (previous, current) {
        if (current.status == ForgotPasswordStatus.success) {
          showNotificationDialog(
            context,
            localizations.passwordResetSuccess,
            // Use primaryAdaptive for the success notification icon color
            iconColor: AppColors.primaryAdaptive(context),
            defaultIcon: Icons.check_circle_outline,
          );
          context.go('/login');
          forgotPasswordNotifier.resetState();
        } else if (current.status == ForgotPasswordStatus.error) {
          showErrorDialog(context, current.errorMessage ?? localizations.passwordResetError);
          forgotPasswordNotifier.resetState();
        }
      },
    );

    return Scaffold(
      backgroundColor: Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: const IconThemeData(color: Colors.black),
      ),
      body: SafeArea(
        child: SingleChildScrollView(
          child: Column(
            mainAxisAlignment: MainAxisAlignment.spaceBetween,
            children: [
              Padding(
                padding: const EdgeInsets.all(12.0),
                child: Form(
                  key: _formKey,
                  child: Column(
                    children: [
                      Align(
                        alignment: Alignment.centerRight,
                        child: Padding(
                          padding: const EdgeInsets.only(top: 16.0, bottom: 10.0),
                          child: const LanguageDropdown(),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Image.asset(AppImage.logo, width: 170, height: 170),
                      Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Text(
                          localizations.forgotPasswordTitle,
                          style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Text(
                          localizations.forgotPasswordSubtitle,
                          textAlign: TextAlign.center,
                          style: const TextStyle(fontSize: 16, color: Colors.grey),
                        ),
                      ),
                      const SizedBox(height: 20),
                      _inputField(
                        localizations.emailInputHint,
                        controller: _emailController,
                        validator: (value) {
                          if (value == null || value.isEmpty) {
                            return localizations.emailEmptyError;
                          }
                          if (!RegExp(r'^[^@]+@[^@]+\.[^@]+').hasMatch(value)) {
                            return localizations.emailInvalidError;
                          }
                          return null;
                        },
                      ),
                      const SizedBox(height: 8),
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12.0, vertical: 12.0),
                        child: SizedBox(
                          width: double.infinity,
                          height: 50,
                          child: TextButton(
                            onPressed: forgotPasswordState.status == ForgotPasswordStatus.loading
                                ? null
                                : () {
                              if (_formKey.currentState!.validate()) {
                                forgotPasswordNotifier.requestPasswordReset(_emailController.text);
                              }
                            },
                            style: ButtonStyle(
                              backgroundColor: MaterialStateProperty.all(const Color(0xFF124624)),
                              foregroundColor: MaterialStateProperty.all(Colors.white),
                              shape: MaterialStateProperty.all(
                                RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(8),
                                ),
                              ),
                            ),
                            child: forgotPasswordState.status == ForgotPasswordStatus.loading
                                ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                              ),
                            )
                                : Text(
                              localizations.sendCodeButton,
                              style: const TextStyle(fontSize: 20),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.only(bottom: 32.0),
                child: TextButton(
                  onPressed: () {
                    context.push('/login');
                  },
                  style: ButtonStyle(
                    backgroundColor: MaterialStateProperty.all(Colors.transparent),
                    foregroundColor: MaterialStateProperty.all(Colors.green),
                    overlayColor: MaterialStateProperty.all(Colors.transparent),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(localizations.rememberPasswordPrompt, style: const TextStyle(color: Colors.black)),
                      const SizedBox(width: 4),
                      Text(localizations.loginButtonText),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _inputField(String hint, {TextEditingController? controller, String? Function(String?)? validator}) {
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 12.0),
      child: Container(
        width: double.infinity,
        decoration: BoxDecoration(
          color: Colors.grey[200],
          borderRadius: BorderRadius.circular(8),
        ),
        child: Padding(
          padding: const EdgeInsets.symmetric(horizontal: 16.0),
          child: TextFormField(
            controller: controller,
            obscureText: false,
            decoration: InputDecoration(
              border: InputBorder.none,
              hintText: hint,
            ),
            validator: validator,
            keyboardType: TextInputType.emailAddress,
          ),
        ),
      ),
    );
  }
}