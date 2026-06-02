import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/widgets/dialog/errordialog.dart';
import '../../provider/forgotpassword_provider.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/theme/theme_provider.dart';
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
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final forgotPasswordState = ref.watch(forgotPasswordControllerProvider);
    final forgotPasswordNotifier = ref.read(forgotPasswordControllerProvider.notifier);

    ref.listen<ForgotPasswordState>(
      forgotPasswordControllerProvider,
      (previous, current) {
        if (current.status == ForgotPasswordStatus.success) {
          showNotificationDialog(
            context,
            localizations.passwordResetSuccess,
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
      backgroundColor: isDark ? const Color(0xFF111827) : Colors.white,
      appBar: AppBar(
        backgroundColor: Colors.transparent,
        elevation: 0,
        iconTheme: IconThemeData(color: isDark ? Colors.white : Colors.black),
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
                      /* Language dropdown + theme toggle aligned to the right — same as login page */
                      Align(
                        alignment: Alignment.centerRight,
                        child: Padding(
                          padding: const EdgeInsets.only(top: 16.0, bottom: 10.0),
                          child: Row(
                            mainAxisSize: MainAxisSize.min,
                            children: [
                              const LanguageDropdown(),
                              const SizedBox(width: 4),
                              IconButton(
                                onPressed: () => ref.read(themeProvider.notifier).toggle(),
                                icon: Icon(
                                  isDark ? Icons.light_mode_outlined : Icons.dark_mode_outlined,
                                  color: isDark ? Colors.white70 : Colors.grey.shade600,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Image.asset(AppImage.logo, width: 170, height: 170),
                      Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Text(
                          localizations.forgotPasswordTitle,
                          style: TextStyle(
                            fontSize: 24,
                            fontWeight: FontWeight.bold,
                            color: isDark ? Colors.white : Colors.black,
                          ),
                        ),
                      ),
                      Padding(
                        padding: const EdgeInsets.all(12.0),
                        child: Text(
                          localizations.forgotPasswordSubtitle,
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 16,
                            color: isDark ? Colors.grey[400] : Colors.grey[600],
                          ),
                        ),
                      ),
                      const SizedBox(height: 20),

                      /* Email input — single border via OutlineInputBorder, no Container wrapper */
                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 12.0),
                        child: TextFormField(
                          controller: _emailController,
                          keyboardType: TextInputType.emailAddress,
                          style: TextStyle(
                            color: isDark ? Colors.white : Colors.black87,
                          ),
                          decoration: InputDecoration(
                            hintText: localizations.emailInputHint,
                            hintStyle: TextStyle(
                              color: isDark ? Colors.grey[500] : Colors.grey[500],
                            ),
                            filled: true,
                            fillColor: isDark ? const Color(0xFF1F2937) : Colors.grey[100],
                            /* Single clean border — no double border */
                            border: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide(
                                color: isDark ? Colors.grey[700]! : Colors.grey[300]!,
                              ),
                            ),
                            enabledBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide(
                                color: isDark ? Colors.grey[700]! : Colors.grey[300]!,
                              ),
                            ),
                            focusedBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: BorderSide(
                                color: AppColors.primaryAdaptive(context),
                                width: 2,
                              ),
                            ),
                            errorBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: const BorderSide(color: Colors.red),
                            ),
                            focusedErrorBorder: OutlineInputBorder(
                              borderRadius: BorderRadius.circular(8),
                              borderSide: const BorderSide(color: Colors.red, width: 2),
                            ),
                            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                          ),
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
                              backgroundColor: WidgetStateProperty.all(const Color(0xFF124624)),
                              foregroundColor: WidgetStateProperty.all(Colors.white),
                              shape: WidgetStateProperty.all(
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
                    backgroundColor: WidgetStateProperty.all(Colors.transparent),
                    foregroundColor: WidgetStateProperty.all(Colors.green),
                    overlayColor: WidgetStateProperty.all(Colors.transparent),
                  ),
                  child: Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        localizations.rememberPasswordPrompt,
                        style: TextStyle(color: isDark ? Colors.white70 : Colors.black),
                      ),
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
}
