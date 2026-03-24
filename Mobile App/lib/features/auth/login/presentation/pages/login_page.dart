import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:dio/dio.dart';
import 'dart:convert';
import 'dart:io';
import 'package:ulinmahoniapps/core/widgets/button/backbutton.dart';
import '../../../login/provider/auth_provider.dart';
import '../widgets/inputfield.dart';
import '../widgets/passwordfield.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../widgets/socialmedi.dart';
import '../../../../../core/constants/appfontweight_constants.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/widgets/biometric_auth.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../provider/googlesignin_provider.dart';
import '../../provider/applesignin_provider.dart';
import 'package:ulinmahoniapps/core/widgets/languagedropdown.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/services/apple_multi_account_storage.dart';
import '../../../../../core/utils/app_logger.dart';
import '../widgets/apple_account_picker_dialog.dart';
import '../widgets/email_verification_popup.dart';

class LoginPage extends ConsumerStatefulWidget {
  const LoginPage({Key? key}) : super(key: key);

  @override
  ConsumerState<LoginPage> createState() => _LoginPageState();
}

class _LoginPageState extends ConsumerState<LoginPage> {
  final BiometricAuthService _biometricAuthService = BiometricAuthService();
  final TextEditingController _emailController = TextEditingController();
  bool _isLoginProcessing = false;
  final TextEditingController _passwordController = TextEditingController();
  bool _rememberMe = false;

  @override
  void dispose() {
    _emailController.dispose();
    _passwordController.dispose();
    super.dispose();
  }

  @override
  void initState() {
    super.initState();
    _authenticateBiometricsOnLoad();
  }

  Future<void> _authenticateBiometricsOnLoad() async {
    if (!mounted) return;

    // Step 1: Check if user has Remember Me enabled (token + user in SharedPreferences)
    final authRepository = ref.read(authRepositoryProvider);
    final storedToken = await authRepository.getTokenLocally();
    final storedUser = await authRepository.getUserLocally();

    if (storedToken == null || storedUser == null) {
      // No Remember Me - stay on login page
      return;
    }

    // Step 2: Verify user still exists in backend via GET /users/{id}
    final userId = storedUser['id'] as int?;
    if (userId == null) {
      // Invalid user data - clear and stay on login page
      await authRepository.logout();
      return;
    }

    final userResult = await authRepository.fetchUserProfileById(userId);

    switch (userResult) {
      case Failure():
        // User doesn't exist or API error - clear local data and stay on login page
        await authRepository.logout();
        return;

      case Success(:final data):
        // Check if email is verified
        if (!data.isEmailVerified) {
          // Email not verified - logout and show dialog
          await authRepository.logout();
          if (mounted) {
            final localizations = AppLocalizations.of(context)!;
            showNotificationDialog(
              context,
              localizations.emailNotVerifiedMessage,
              title: localizations.emailNotVerifiedTitle,
              iconColor: Colors.orange,
              defaultIcon: Icons.warning_amber_outlined,
            );
          }
          return;
        }

        // User exists and email verified - proceed with biometric
        if (!mounted) return;

        final didAuthenticate = await _biometricAuthService.authenticateOnLoad(context);

        if (didAuthenticate && mounted) {
          // Biometric success - navigate to home
          context.go('/home');
        }
        // If biometric fails - stay on login page with fingerprint button available
    }
  }

  Future<void> _onAppleSignInPressed() async {
    if (!mounted) return;

    try {
      // NEW FLOW: Check existing accounts FIRST
      AppLogger.i('Checking existing Apple accounts before sign-in', 'LOGIN-PAGE');
      await ref.read(appleSignInControllerProvider.notifier).checkExistingAccounts();

      // Check the result state
      final state = ref.read(appleSignInControllerProvider);

      state.when(
        data: (result) {
          // If result is null, it means no existing accounts - proceed with Apple SDK
          if (result == null) {
            AppLogger.i('No existing accounts, proceeding with Apple Sign-In SDK', 'LOGIN-PAGE');
            if (mounted) {
              ref.read(appleSignInControllerProvider.notifier).signInWithApple(rememberMe: _rememberMe);
            }
          }
          // If result is not null, the listener will handle it (account picker, verification popup, etc.)
        },
        loading: () {
          // Still checking - wait for result
        },
        error: (error, stackTrace) {
          AppLogger.e('Error checking accounts: $error', error, stackTrace, 'LOGIN-PAGE');
          // Error - fallback to normal Apple Sign In
          if (mounted) {
            ref.read(appleSignInControllerProvider.notifier).signInWithApple(rememberMe: _rememberMe);
          }
        },
      );
    } catch (e, stackTrace) {
      AppLogger.e('Error in Apple Sign-In flow', e, stackTrace, 'LOGIN-PAGE');

      // Error - fallback to normal Apple Sign In
      if (mounted) {
        await ref.read(appleSignInControllerProvider.notifier).signInWithApple(rememberMe: _rememberMe);
      }
    }
  }

  Future<void> _onFingerprintPressed() async {
    if (!mounted) return;

    final localizations = AppLocalizations.of(context)!;

    // Step 1: Check if user has Remember Me enabled
    final authRepository = ref.read(authRepositoryProvider);
    final storedToken = await authRepository.getTokenLocally();
    final storedUser = await authRepository.getUserLocally();

    if (storedToken == null || storedUser == null) {
      // No Remember Me - show error
      if (mounted) {
        showErrorDialog(context, localizations.biometricAuthNotConfigured);
      }
      return;
    }

    // Step 2: Verify user still exists in backend via GET /users/{id}
    final userId = storedUser['id'] as int?;
    if (userId == null) {
      // Invalid user data - clear and show error
      await authRepository.logout();
      if (mounted) {
        showErrorDialog(context, localizations.biometricAuthNotConfigured);
      }
      return;
    }

    final userResult = await authRepository.fetchUserProfileById(userId);

    switch (userResult) {
      case Failure():
        // User doesn't exist or API error - clear local data and show error
        await authRepository.logout();
        if (mounted) {
          showErrorDialog(context, localizations.biometricAuthNotConfigured);
        }
        return;

      case Success(:final data):
        // Check if email is verified
        if (!data.isEmailVerified) {
          // Email not verified - logout and show dialog
          await authRepository.logout();
          if (mounted) {
            showNotificationDialog(
              context,
              localizations.emailNotVerifiedMessage,
              title: localizations.emailNotVerifiedTitle,
              iconColor: Colors.orange,
              defaultIcon: Icons.warning_amber_outlined,
            );
          }
          return;
        }

        // User exists and email verified - proceed with biometric
        if (!mounted) return;

        final didAuthenticate = await _biometricAuthService.authenticateOnLoad(context);

        if (didAuthenticate && mounted) {
          // Biometric success - navigate to home
          context.go('/home');
        } else if (mounted) {
          // Biometric failed - show error
          showErrorDialog(context, localizations.biometricAuthFailed);
        }
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final googleAuthState = ref.watch(googleSignInControllerProvider);
    final appleAuthState = ref.watch(appleSignInControllerProvider);

    ref.listen<AsyncValue<GoogleSignInResult?>>(
      googleSignInControllerProvider,
      (previous, next) {
        next.when(
          data: (result) {
            if (result != null) {
              // Check if email verification is required
              if (result.requiresEmailVerification) {
                // Email not verified - show dialog and logout
                showNotificationDialog(
                  context,
                  localizations.emailNotVerifiedMessage,
                  title: localizations.emailNotVerifiedTitle,
                  iconColor: Colors.orange,
                  defaultIcon: Icons.warning_amber_outlined,
                  onOkPressed: () async {
                    // Logout user after dialog is closed
                    await ref.read(authProvider.notifier).logout();
                  },
                );
                return;
              }

              // Handle different behaviors for new vs existing users
              if (result.isNewUser) {
                // New user - just registered, NOT logged in
                // Show notification and stay on login page
                showNotificationDialog(
                  context,
                  localizations.googleSignInNewAccountMessage,
                  title: localizations.googleSignInNewAccountTitle,
                  iconColor: Colors.green,
                  defaultIcon: Icons.check_circle_outline,
                );
                // Do NOT navigate to /home for new users
                // They need to verify email and login manually
              } else {
                // Existing user - logged in successfully
                // DO NOT show notification dialog
                // Navigate directly to /home

                // Wait for auth provider to update, then navigate
                // Use addPostFrameCallback to ensure navigation happens after state update
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (mounted) {
                    final authState = ref.read(authProvider);
                    if (authState.isLoggedIn) {
                      AppLogger.i('Sign-In successful, navigating to home', 'LOGIN-PAGE');
                      context.go('/home');
                    } else {
                      AppLogger.w('Auth state not logged in after sign-in', 'LOGIN-PAGE');
                    }
                  }
                });
              }
            }
          },
          loading: () {
            // Loading state is handled by the button
          },
          error: (error, stackTrace) {
            if (mounted) {
              String displayMessage = error.toString().trim();
              // Don't show dialog if error message is empty (user cancelled)
              if (displayMessage.isNotEmpty) {
                showErrorDialog(context, displayMessage);
              } else {
                AppLogger.d('Google Sign-In cancelled by user (no error dialog)', 'LOGIN-PAGE');
              }
            }
          },
        );
      },
    );

    ref.listen<AsyncValue<AppleSignInResult?>>(
      appleSignInControllerProvider,
      (previous, next) {
        next.when(
          data: (result) {
            if (result != null) {
              // Check if should show account picker
              if (result.showAccountPicker && result.accounts != null) {
                AppLogger.d('Showing account picker with ${result.accounts!.length} accounts', 'LOGIN-PAGE');

                AppleAccountPickerDialog.show(
                  context: context,
                  accounts: result.accounts!,
                  onAccountSelected: (account) async {
                    // User selected an existing account
                    AppLogger.d('User selected account: ${account.email}', 'LOGIN-PAGE');
                    await ref.read(appleSignInControllerProvider.notifier)
                        .loginWithExistingAccount(account, rememberMe: _rememberMe);
                  },
                  onUseNewAccount: () async {
                    // User wants to use a new Apple account
                    AppLogger.d('User chose to use new Apple account', 'LOGIN-PAGE');
                    await ref.read(appleSignInControllerProvider.notifier)
                        .signInWithApple(rememberMe: _rememberMe);
                  },
                );
                return;
              }

              // Check if email verification is required
              if (result.requiresEmailVerification) {
                // Get email from result or from storage
                String email = 'your email';

                // Try to get email from AppleMultiAccountStorage
                Future.microtask(() async {
                  try {
                    final storage = AppleMultiAccountStorage();
                    final activeAccount = await storage.getActiveAccount();
                    if (activeAccount != null) {
                      email = activeAccount.email ?? email;
                    }
                  } catch (e) {
                    AppLogger.e('Failed to get email for verification popup', e, null, 'LOGIN-PAGE');
                  }

                  // Show email verification popup
                  if (mounted) {
                    EmailVerificationPopup.show(
                      context: context,
                      email: email,
                      onResendEmail: () async {
                        // TODO: Implement resend verification email API call
                        AppLogger.i('Resend verification email requested for: $email', 'LOGIN-PAGE');

                        // For now, just show success popup
                        if (mounted) {
                          EmailVerificationSentPopup.show(
                            context: context,
                            email: email,
                          );
                        }
                      },
                      onCancel: () {
                        AppLogger.d('User cancelled email verification popup', 'LOGIN-PAGE');
                      },
                    );
                  }
                });
                return;
              }

              // Handle different behaviors for new vs existing users
              if (result.isNewUser) {
                // New user - just registered, NOT logged in
                // Show notification and stay on login page
                showNotificationDialog(
                  context,
                  localizations.googleSignInNewAccountMessage, // Use same string as Google for now
                  title: localizations.googleSignInNewAccountTitle,
                  iconColor: Colors.green,
                  defaultIcon: Icons.check_circle_outline,
                );
                // Do NOT navigate to /home for new users
                // They need to verify email and login manually
              } else {
                // Existing user - logged in successfully
                // DO NOT show notification dialog
                // Navigate directly to /home

                // Wait for auth provider to update, then navigate
                // Use addPostFrameCallback to ensure navigation happens after state update
                WidgetsBinding.instance.addPostFrameCallback((_) {
                  if (mounted) {
                    final authState = ref.read(authProvider);
                    if (authState.isLoggedIn) {
                      AppLogger.i('Sign-In successful, navigating to home', 'LOGIN-PAGE');
                      context.go('/home');
                    } else {
                      AppLogger.w('Auth state not logged in after sign-in', 'LOGIN-PAGE');
                    }
                  }
                });
              }
            }
          },
          loading: () {
            // Loading state is handled by the button
          },
          error: (error, stackTrace) {
            if (mounted) {
              String displayMessage = error.toString().trim();
              // Don't show dialog if error message is empty (user cancelled)
              if (displayMessage.isNotEmpty) {
                showErrorDialog(context, displayMessage);
              } else {
                AppLogger.d('Apple Sign-In cancelled by user (no error dialog)', 'LOGIN-PAGE');
              }
            }
          },
        );
      },
    );

    // Detect dark/light mode for theme-aware styling
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Scaffold(
      // Use theme scaffold background (dark or light)
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      resizeToAvoidBottomInset: true,
      body: SafeArea(
        child: Stack(
          children: [
            SingleChildScrollView(
              child: Column(
                children: [
                  const SizedBox(height: 20),
                  Padding(
                    padding: const EdgeInsets.all(12.0),
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
                            localizations.loginWelcomeTitle,
                            style: TextStyle(
                                fontSize: 24,
                                fontWeight: FontWeight.bold,
                                color: isDark ? Colors.white : Colors.black),
                            textAlign: TextAlign.center,
                          ),
                        ),
                        const SizedBox(height: 15),
                        inputField(
                          localizations.loginEmailHint,
                          controller: _emailController,
                        ),
                        const SizedBox(height: 15),
                        PasswordField(
                          hint: localizations.loginPasswordHint,
                          controller: _passwordController,
                        ),
                        Row(
                          mainAxisAlignment: MainAxisAlignment.spaceBetween,
                          children: [
                            Row(
                              children: [
                                Checkbox(
                                  value: _rememberMe,
                                  onChanged: (bool? newValue) {
                                    setState(() {
                                      _rememberMe = newValue ?? false;
                                    });
                                  },
                                  activeColor: const Color(0xFF124624),
                                  side: BorderSide(
                                    color: isDark ? Colors.grey.shade600 : Colors.grey.shade400,
                                    width: 2.0,
                                  ),
                                ),
                                Text(
                                  localizations.rememberMe,
                                  style: TextStyle(
                                      fontSize: 14,
                                      color: isDark ? Colors.grey[400] : Colors.grey[600]),
                                ),
                              ],
                            ),
                            // TextButton(
                            //   onPressed: () {
                            //     context.push('/forgetpassword');
                            //   },
                            //   style: ButtonStyle(
                            //     backgroundColor: MaterialStateProperty.all(
                            //         Colors.transparent),
                            //     foregroundColor:
                            //     MaterialStateProperty.all(Colors.grey[600]),
                            //     overlayColor: MaterialStateProperty.all(
                            //         Colors.transparent),
                            //   ),
                            //   child: Text(localizations.forgotPasswordTitle),
                            // ),
                          ],
                        ),
                        SizedBox(
                          width: double.infinity,
                          height: 50,
                          child: TextButton(
                            onPressed: _isLoginProcessing
                                ? null
                                : () async {
                              if (!mounted) return;
                              setState(() {
                                _isLoginProcessing = true;
                              });

                              final login = _emailController.text.trim();
                              final password =
                              _passwordController.text.trim();

                              if (login.isEmpty || password.isEmpty) {
                                if (mounted) {
                                  showErrorDialog(context,
                                      localizations.loginEmptyError);
                                  setState(() {
                                    _isLoginProcessing = false;
                                  });
                                }
                                return;
                              }
                              final emailRegex = RegExp(
                                  r'^[\w-\.]+@([\w-]+\.)+[a-zA-Z]{2,4}$');
                              final phoneRegex = RegExp(
                                  r'^(?:\+62|0|62)[2-9]\d{6,11}$');
                              bool isEmail = emailRegex.hasMatch(login);
                              bool isPhone = phoneRegex.hasMatch(login);

                              if (!isEmail && !isPhone) {
                                if (mounted) {
                                  showErrorDialog(context,
                                      localizations.loginInvalidFormatError);
                                  setState(() {
                                    _isLoginProcessing = false;
                                  });
                                }
                                return;
                              }
                              if (password.length < 8) {
                                if (mounted) {
                                  showErrorDialog(context,
                                      localizations.loginPasswordLengthError);
                                  setState(() {
                                    _isLoginProcessing = false;
                                  });
                                }
                                return;
                              }
                              try {
                                await ref
                                    .read(authProvider.notifier)
                                    .login(
                                  login,
                                  password,
                                  rememberMe: _rememberMe,
                                );
                                if (mounted) {
                                  final authState =
                                  ref.read(authProvider);
                                  if (authState.isLoggedIn) {
                                    context.go('/home');
                                  } else {
                                    // Use the error message from authState if available
                                    final errorMessage = authState.errorMessage ??
                                        localizations.loginIncorrectCredentials;
                                    showErrorDialog(context, errorMessage);
                                  }
                                }
                              } on DioException catch (dioError) {
                                if (mounted) {
                                  String displayMessage =
                                      localizations.loginUnknownError;

                                  if (dioError.response?.statusCode ==
                                      401) {
                                    displayMessage = localizations
                                        .loginIncorrectCredentials;
                                  } else {
                                    if (dioError.response?.data != null) {
                                      try {
                                        final errorData =
                                            dioError.response?.data;
                                        if (errorData is String) {
                                          final decodedError =
                                          jsonDecode(errorData);
                                          displayMessage =
                                              decodedError['message'] ?? displayMessage;
                                        } else if (errorData is Map) {
                                          displayMessage =
                                              errorData['message'] ?? displayMessage;
                                        }
                                      } catch (e) {
                                        displayMessage =
                                            dioError.message ??
                                                localizations.loginUnknownNetworkError;
                                      }
                                    } else {
                                      displayMessage = dioError.message ??
                                          localizations.loginUnknownNetworkError;
                                    }
                                  }
                                  showErrorDialog(context, displayMessage);
                                }
                              } catch (e) {
                                if (mounted) {
                                  showErrorDialog(
                                      context,
                                      localizations
                                          .loginGeneralError(e.toString()));
                                }
                              } finally {
                                if (mounted) {
                                  setState(() {
                                    _isLoginProcessing = false;
                                  });
                                }
                              }
                            },
                            style: ButtonStyle(
                              backgroundColor: _isLoginProcessing
                                  ? MaterialStateProperty.all(Colors.grey)
                                  : MaterialStateProperty.all(
                                  const Color(0xFF124624)),
                              foregroundColor:
                              MaterialStateProperty.all(Colors.white),
                              shape: MaterialStateProperty.all(
                                RoundedRectangleBorder(
                                  borderRadius: BorderRadius.circular(8),
                                ),
                              ),
                            ),
                            child: _isLoginProcessing
                                ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                valueColor:
                                AlwaysStoppedAnimation<Color>(Colors.white),
                              ),
                            )
                                : Text(
                              localizations.loginButton,
                              style: const TextStyle(fontSize: 20),
                            ),
                          ),
                        ),
                        const SizedBox(height: 20),
                        Row(
                          children: [
                            Expanded(child: Divider(color: isDark ? Colors.grey.shade700 : Colors.grey.shade400)),
                            Padding(
                              padding:
                              const EdgeInsets.symmetric(horizontal: 12.0),
                              child: Text(
                                localizations.loginOrText,
                                style: TextStyle(color: isDark ? Colors.grey.shade500 : Colors.grey),
                              ),
                            ),
                            Expanded(child: Divider(color: isDark ? Colors.grey.shade700 : Colors.grey.shade400)),
                          ],
                        ),
                        const SizedBox(height: 5),
                        Padding(
                          padding: const EdgeInsets.symmetric(vertical: 6.0),
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceAround,
                            children: [
                              socialMedia(
                                "https://fonts.gstatic.com/s/i/productlogos/googleg/v6/24px.svg",
                                onTap: googleAuthState.isLoading ? null : () async {
                                  await ref.read(googleSignInControllerProvider.notifier).signInWithGoogle(rememberMe: _rememberMe);
                                },
                                isNetworkImage: true,
                                isSvg: true,
                              ),
                              // Apple Sign-In: iOS only (Android belum ready)
                              if (Platform.isIOS)
                                socialMedia(
                                  "https://upload.wikimedia.org/wikipedia/commons/f/fa/Apple_logo_black.svg",
                                  onTap: appleAuthState.isLoading ? null : _onAppleSignInPressed,
                                  isNetworkImage: true,
                                  isSvg: true,
                                ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                  Padding(
                    padding: const EdgeInsets.only(bottom: 5.0, top: 5.0),
                    child: TextButton(
                      onPressed: () {
                        context.push('/register');
                      },
                      style: ButtonStyle(
                        backgroundColor:
                        WidgetStateProperty.all(Colors.transparent),
                        foregroundColor:
                        WidgetStateProperty.all(const Color(0xFF124624)),
                        overlayColor:
                        WidgetStateProperty.all(Colors.transparent),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(
                            localizations.loginNoAccountPrompt,
                            style: TextStyle(
                                color: isDark ? Colors.white : Colors.black,
                                fontWeight: AppFontWeight.bold),
                          ),
                          const SizedBox(width: 5),
                          Text(localizations.registerButton),
                        ],
                      ),
                    ),
                  ),
                  Text(localizations.loginOrText,
                      style: TextStyle(
                          color: isDark ? Colors.white : Colors.black,
                          fontWeight: AppFontWeight.bold)),
                  Padding(
                    padding: const EdgeInsets.only(bottom: 2.0, top: 5),
                    child: TextButton(
                      onPressed: () async {
                        await ref.read(authProvider.notifier).logout();
                        if (mounted) {
                          context.go('/home');
                        }
                      },
                      style: ButtonStyle(
                        backgroundColor:
                        WidgetStateProperty.all(Colors.transparent),
                        foregroundColor:
                        WidgetStateProperty.all(const Color(0xFF124624)),
                        overlayColor:
                        WidgetStateProperty.all(Colors.transparent),
                      ),
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          Text(localizations.loginAsGuestButton),
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
            Positioned(
              top: 16,
              left: 16,
              child: CustomBackButton(iconColor: isDark ? Colors.white : Colors.black),
            ),
            Positioned(
              left: 20,
              bottom: -10,
              child: IconButton(
                icon: const Icon(Icons.fingerprint, size: 25),
                color: isDark ? Colors.white70 : const Color(0xFF124624),
                onPressed: _onFingerprintPressed,
              ),
            ),
        ],
        ),
      ),
    );
  }
}