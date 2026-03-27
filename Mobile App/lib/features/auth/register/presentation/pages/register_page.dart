import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:flutter/services.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../provider/register_provider.dart';
import '../../../../../core/widgets/dialog/errordialog.dart';
import '../widgets/inputfield.dart';
import '../widgets/passwordfield.dart';
import '../../../../../core/widgets/button/backbutton.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/appfontweight_constants.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/core/widgets/biometric_auth.dart';
import 'package:ulinmahoniapps/core/widgets/languagedropdown.dart';
import '../../../../../core/theme/theme_provider.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';

class RegisterPage extends ConsumerStatefulWidget {
  const RegisterPage({Key? key}) : super(key: key);

  @override
  ConsumerState<RegisterPage> createState() => _RegisterPageState();
}

class _RegisterPageState extends ConsumerState<RegisterPage> {
  final _usernameController = TextEditingController();
  final _emailController = TextEditingController();
  final _passwordController = TextEditingController();
  final _confirmPasswordController = TextEditingController();
  final _phoneNumberController = TextEditingController();
  final _firstNameController = TextEditingController();
  final _lastNameController = TextEditingController();
  final BiometricAuthService _biometricAuthService = BiometricAuthService();

  bool _obscurePassword = true;
  bool _obscureConfirmPassword = true;

  // --- STATE CHECKBOX ---
  bool _showOnlyFirstName = false;
  bool _agreedTerms = false;
  bool _agreedPrivacy = false;
  bool _readBoth = false;

  String? _fullPhoneNumber;
  String _currentCountryCode = 'ID';

  @override
  void initState() {
    super.initState();

    _usernameController.addListener(_checkFormValidity);
    _emailController.addListener(_checkFormValidity);
    _passwordController.addListener(_checkFormValidity);
    _confirmPasswordController.addListener(_checkFormValidity);
    _phoneNumberController.addListener(_checkFormValidity);
    _firstNameController.addListener(_checkFormValidity);
    _lastNameController.addListener(_checkFormValidity);
  }

  @override
  void dispose() {
    _usernameController.removeListener(_checkFormValidity);
    _emailController.removeListener(_checkFormValidity);
    _passwordController.removeListener(_checkFormValidity);
    _confirmPasswordController.removeListener(_checkFormValidity);
    _phoneNumberController.removeListener(_checkFormValidity);
    _firstNameController.removeListener(_checkFormValidity);
    _lastNameController.removeListener(_checkFormValidity);
    _usernameController.dispose();
    _emailController.dispose();
    _passwordController.dispose();
    _confirmPasswordController.dispose();
    _phoneNumberController.dispose();
    _firstNameController.dispose();
    _lastNameController.dispose();
    super.dispose();
  }

  Future<void> _launchURL(String urlString) async {
    final Uri url = Uri.parse(urlString);
    try {
      if (!await launchUrl(url, mode: LaunchMode.externalApplication)) {
        throw Exception('Could not launch $url');
      }
    } catch (e) {
      if (mounted) {
        showNotificationDialog(
          context,
          'Gagal membuka link: $e',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  void _checkFormValidity() {
    setState(() {
      // This will trigger a rebuild to update button state
    });
  }

  bool _isFormValid() {
    // Check if all required fields are filled
    final firstName = _firstNameController.text.trim();
    final lastName = _lastNameController.text.trim();
    final email = _emailController.text.trim();
    final password = _passwordController.text.trim();
    final confirmPassword = _confirmPasswordController.text.trim();
    final phone = (_fullPhoneNumber ?? '').trim();

    // Check first name
    if (firstName.isEmpty) return false;

    // Check last name only if not using single name
    if (!_showOnlyFirstName && lastName.isEmpty) return false;

    // Check email
    if (email.isEmpty) return false;

    // Check phone
    if (phone.isEmpty) return false;

    // Check password
    if (password.isEmpty) return false;

    // Check confirm password
    if (confirmPassword.isEmpty) return false;

    // Check all checkboxes
    if (!_agreedTerms || !_agreedPrivacy || !_readBoth) return false;

    return true;
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final registerState = ref.watch(registerControllerProvider);
    final registerNotifier = ref.read(registerControllerProvider.notifier);

    ref.listen<RegisterState>(registerControllerProvider, (previous, current) {
      if (current.status == RegisterStatus.success) {
        showErrorDialog(
          context,
          localizations.registerSuccessMessage,
          routeName: "/login",
          buttonText: localizations.loginButton,
        );
        registerNotifier.resetState();
      } else if (current.status == RegisterStatus.error) {
        showErrorDialog(
          context,
          current.errorMessage ?? localizations.registerUnknownError,
        );
        registerNotifier.resetState();
      }
    });

    // Detect dark/light mode for theme-aware styling
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Style text abu-abu (untuk bagian "Saya menyetujui") — dark mode aware
    final TextStyle greyTextStyle = TextStyle(
      fontSize: 14,
      color: isDark ? Colors.grey[400] : Colors.grey[700],
    );

    // Style link (Bold, Underline) — dark mode aware
    final TextStyle linkStyle = TextStyle(
      fontSize: 14,
      color: isDark ? Colors.grey[300] : Colors.grey[800],
      fontWeight: FontWeight.bold,
      decoration: TextDecoration.underline,
    );

    return Scaffold(
      backgroundColor: Theme.of(context).scaffoldBackgroundColor,
      resizeToAvoidBottomInset: true,
      body: SafeArea(
        child: Stack(
          children: [
            SingleChildScrollView(
              padding: const EdgeInsets.symmetric(horizontal: 24.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.stretch,
                children: [
                  // Language dropdown + dark/light mode toggle aligned to the right
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
                  Image.asset(AppImage.logo, height: 170, width: 170),
                  const SizedBox(height: 24),
                  Text(
                    localizations.registerWelcomeTitle,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: FontWeight.bold,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 32),

                  // --- FORM INPUTS ---
                  inputField(
                    _showOnlyFirstName
                        ? localizations.fullNameLabel
                        : localizations.firstNameLabel,
                    _firstNameController,
                    context: context,
                  ),
                  const SizedBox(height: 16),

                  Visibility(
                    visible: !_showOnlyFirstName,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        inputField(
                          localizations.lastNameLabel,
                          _lastNameController,
                          context: context,
                        ),
                        const SizedBox(height: 16),
                      ],
                    ),
                  ),

                  inputField(localizations.emailLabel, _emailController, context: context),
                  const SizedBox(height: 16),

                  // Phone Field — single-layer fill via decoration (no Container wrapper).
                  // Strips leading zeros and allows digits only, matching frontend signup rules.
                  IntlPhoneField(
                      key: ValueKey(_currentCountryCode),
                      controller: _phoneNumberController,
                      inputFormatters: [
                        // Only allow digits — matches frontend oninput replace(/[^0-9]/g, '')
                        FilteringTextInputFormatter.digitsOnly,
                        // Strip leading zeros — matches frontend oninput replace(/^0+/, '')
                        _StripLeadingZerosFormatter(),
                      ],
                      decoration: InputDecoration(
                        labelText: localizations.phoneNumberLabel,
                        // Placeholder matching frontend format (no leading zero)
                        hintText: '8123456789',
                        // Single fill layer matching other input fields
                        filled: true,
                        fillColor: isDark ? const Color(0xFF374151) : Colors.grey[200],
                        border: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide.none,
                        ),
                        enabledBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide.none,
                        ),
                        focusedBorder: OutlineInputBorder(
                          borderRadius: BorderRadius.circular(8),
                          borderSide: BorderSide.none,
                        ),
                        contentPadding: const EdgeInsets.symmetric(
                          vertical: 14.0,
                          horizontal: 16.0,
                        ),
                        counterText: "",
                      ),
                      initialCountryCode: _currentCountryCode,
                      languageCode: "id",
                      dropdownIcon: const Icon(Icons.arrow_drop_down),
                      dropdownIconPosition: IconPosition.trailing,
                      dropdownTextStyle: TextStyle(
                        fontSize: 16,
                        color: isDark ? Colors.white : Colors.black,
                      ),
                      cursorColor: const Color(0xFF124624),
                      onChanged: (phone) {
                        _fullPhoneNumber = phone.completeNumber;
                        _checkFormValidity();
                      },
                      onCountryChanged: (country) {
                        setState(() {
                          _currentCountryCode = country.code;
                          _phoneNumberController.clear();
                          _fullPhoneNumber = null;
                        });
                        _checkFormValidity();
                      },
                      validator: (phone) {
                        if (phone == null || phone.number.isEmpty) {
                          return localizations.phoneNumberEmptyError;
                        }
                        return null;
                      },
                  ),
                  const SizedBox(height: 16),

                  passwordField(
                    localizations.passwordLabel,
                    _passwordController,
                    obscure: _obscurePassword,
                    onToggleVisibility: () {
                      setState(() {
                        _obscurePassword = !_obscurePassword;
                      });
                    },
                    context: context,
                  ),
                  const SizedBox(height: 4),
                  const Text(
                    "Minimal 8 karakter",
                    style: TextStyle(fontSize: 12, color: Colors.grey),
                  ),
                  const SizedBox(height: 16),

                  passwordField(
                    localizations.confirmPasswordLabel,
                    _confirmPasswordController,
                    obscure: _obscureConfirmPassword,
                    onToggleVisibility: () {
                      setState(() {
                        _obscureConfirmPassword = !_obscureConfirmPassword;
                      });
                    },
                    context: context,
                  ),
                  const SizedBox(height: 16),

                  Row(
                    children: [
                      Checkbox(
                        value: _showOnlyFirstName,
                        onChanged: (bool? newValue) {
                          setState(() {
                            _showOnlyFirstName = newValue ?? false;
                            if (_showOnlyFirstName) {
                              _lastNameController.clear();
                            }
                          });
                          _checkFormValidity();
                        },
                        activeColor: const Color(0xFF124624),
                        side: BorderSide(
                          color: Colors.grey.shade400,
                          width: 2.0,
                        ),
                      ),
                      GestureDetector(
                        onTap: () {
                          setState(() {
                            _showOnlyFirstName = !_showOnlyFirstName;
                            if (_showOnlyFirstName) {
                              _lastNameController.clear();
                            }
                          });
                          _checkFormValidity();
                        },
                        child: Text(
                          localizations.registerOneNameLabel,
                          style: greyTextStyle,
                        ),
                      ),
                    ],
                  ),

                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Checkbox(
                        value: _agreedTerms,
                        onChanged: (bool? newValue) {
                          setState(() {
                            _agreedTerms = newValue ?? false;
                          });
                          _checkFormValidity();
                        },
                        activeColor: const Color(0xFF124624),
                        side: BorderSide(
                          color: Colors.grey.shade400,
                          width: 2.0,
                        ),
                      ),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.only(
                            top: 10.0,
                          ), // Padding agar sejajar Checkbox
                          child: Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              GestureDetector(
                                onTap: () {
                                  setState(() => _agreedTerms = !_agreedTerms);
                                  _checkFormValidity();
                                },
                                child: Text(
                                  "Saya menyetujui ",
                                  style: greyTextStyle,
                                ),
                              ),
                              GestureDetector(
                                onTap:
                                    () => _launchURL(
                                      'https://web.ulinmahoni.com/terms-of-services',
                                    ),
                                child: Text(
                                  "Syarat dan Ketentuan",
                                  style: linkStyle,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),

                  // --- 3. CHECKBOX: SETUJU KEBIJAKAN PRIVASI (MODIFIED) ---
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Checkbox(
                        value: _agreedPrivacy,
                        onChanged: (bool? newValue) {
                          setState(() {
                            _agreedPrivacy = newValue ?? false;
                          });
                          _checkFormValidity();
                        },
                        activeColor: const Color(0xFF124624),
                        side: BorderSide(
                          color: Colors.grey.shade400,
                          width: 2.0,
                        ),
                      ),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.only(top: 10.0),
                          child: Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              GestureDetector(
                                onTap: () {
                                  setState(() => _agreedPrivacy = !_agreedPrivacy);
                                  _checkFormValidity();
                                },
                                child: Text(
                                  "Saya menyetujui ",
                                  style: greyTextStyle,
                                ),
                              ),
                              GestureDetector(
                                onTap:
                                    () => _launchURL(
                                      'https://web.ulinmahoni.com/privacy-policy',
                                    ),
                                child: Text(
                                  "Kebijakan Privasi",
                                  style: linkStyle,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),

                  // --- 4. CHECKBOX: SUDAH MEMBACA KEDUANYA ---
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Checkbox(
                        value: _readBoth,
                        onChanged: (bool? newValue) {
                          setState(() {
                            _readBoth = newValue ?? false;
                          });
                          _checkFormValidity();
                        },
                        activeColor: const Color(0xFF124624),
                        side: BorderSide(
                          color: Colors.grey.shade400,
                          width: 2.0,
                        ),
                      ),
                      Expanded(
                        child: Padding(
                          padding: const EdgeInsets.only(top: 10.0),
                          child: Wrap(
                            crossAxisAlignment: WrapCrossAlignment.center,
                            children: [
                              GestureDetector(
                                onTap: () {
                                  setState(() => _readBoth = !_readBoth);
                                  _checkFormValidity();
                                },
                                child: Text(
                                  "Saya telah membaca ",
                                  style: greyTextStyle,
                                ),
                              ),
                              GestureDetector(
                                onTap:
                                    () => _launchURL(
                                      'https://web.ulinmahoni.com/privacy-policy',
                                    ),
                                child: Text(
                                  "Kebijakan Privasi",
                                  style: linkStyle,
                                ),
                              ),
                              GestureDetector(
                                onTap: () {
                                  setState(() => _readBoth = !_readBoth);
                                  _checkFormValidity();
                                },
                                child: Text(" dan ", style: greyTextStyle),
                              ),
                              GestureDetector(
                                onTap:
                                    () => _launchURL(
                                      'https://web.ulinmahoni.com/terms-of-services',
                                    ),
                                child: Text(
                                  "Syarat dan Ketentuan",
                                  style: linkStyle,
                                ),
                              ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 24),

                  // --- TOMBOL REGISTER ---
                  ElevatedButton(
                    onPressed:
                        (registerState.status == RegisterStatus.loading ||
                                !_isFormValid())
                            ? null
                            : () async {
                              final firstName =
                                  _firstNameController.text.trim();
                              final lastName = _lastNameController.text.trim();
                              final email = _emailController.text.trim();
                              final password = _passwordController.text.trim();
                              final confirmPassword =
                                  _confirmPasswordController.text.trim();
                              final finalPhoneNumber =
                                  (_fullPhoneNumber ?? '').trim();
                              final emailRegex = RegExp(
                                r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$',
                              );

                              // VALIDASI FORM
                              if (firstName.isEmpty) {
                                showErrorDialog(
                                  context,
                                  _showOnlyFirstName
                                      ? localizations.nameEmptyError
                                      : localizations.firstNameEmptyError,
                                );
                                return;
                              }
                              if (!_showOnlyFirstName && lastName.isEmpty) {
                                showErrorDialog(
                                  context,
                                  localizations.lastNameEmptyError,
                                );
                                return;
                              }
                              if (email.isEmpty) {
                                showErrorDialog(
                                  context,
                                  localizations.emailEmptyError,
                                );
                                return;
                              }
                              if (!emailRegex.hasMatch(email)) {
                                showErrorDialog(
                                  context,
                                  localizations.emailInvalidError,
                                );
                                return;
                              }
                              if (finalPhoneNumber.isEmpty) {
                                showErrorDialog(
                                  context,
                                  localizations.phoneNumberEmptyError,
                                );
                                return;
                              }
                              if (password.isEmpty) {
                                showErrorDialog(
                                  context,
                                  localizations.passwordEmptyError,
                                );
                                return;
                              }
                              if (password.length < 8) {
                                showErrorDialog(
                                  context,
                                  localizations.passwordLengthError,
                                );
                                return;
                              }
                              if (confirmPassword.isEmpty) {
                                showErrorDialog(
                                  context,
                                  localizations.confirmPasswordEmptyError,
                                );
                                return;
                              }
                              if (password != confirmPassword) {
                                showErrorDialog(
                                  context,
                                  localizations.passwordMismatchError,
                                );
                                return;
                              }

                              // BIOMETRIC
                              final didAuthenticate =
                                  await _biometricAuthService
                                      .authenticateOnLoad(context);
                              if (!didAuthenticate) {
                                context.go('/login');
                                return;
                              }

                              // PROSES REGISTER
                              final String finalLastName;
                              if (_showOnlyFirstName) {
                                finalLastName = firstName;
                              } else {
                                finalLastName = lastName;
                              }
                              final username =  firstName;
                              await registerNotifier.register(
                                username,
                                email,
                                password,
                                finalPhoneNumber,
                                firstName,
                                finalLastName,
                              );
                            },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: const Color(0xFF124624),
                      // Disabled: dark-mode aware so it doesn't look too bright
                      disabledBackgroundColor: isDark ? Colors.grey[700] : Colors.grey[300],
                      foregroundColor: Colors.white,
                      disabledForegroundColor: isDark ? Colors.grey[500] : Colors.grey[600],
                      // Prevent Material 3 from applying surface tint in dark mode
                      surfaceTintColor: Colors.transparent,
                      elevation: 0,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      textStyle: const TextStyle(fontSize: 18),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(8),
                      ),
                    ),
                    child:
                        registerState.status == RegisterStatus.loading
                            ? const SizedBox(
                              width: 24,
                              height: 24,
                              child: CircularProgressIndicator(
                                valueColor: AlwaysStoppedAnimation<Color>(
                                  Colors.white,
                                ),
                              ),
                            )
                            : Text(localizations.registerButton),
                  ),

                  // --- LINK LOGIN ---
                  Row(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Text(
                        localizations.registerHaveAccountPrompt,
                        style: const TextStyle(fontWeight: AppFontWeight.bold),
                      ),
                      TextButton(
                        onPressed: () {
                          context.go('/login');
                        },
                        style: TextButton.styleFrom(
                          foregroundColor: const Color(0xFF124624),
                        ),
                        child: Text(localizations.loginButton),
                      ),
                    ],
                  ),
                ],
              ),
            ),
            Positioned(
              top: 16,
              left: 16,
              child: CustomBackButton(iconColor: isDark ? Colors.white : Colors.black),
            ),
          ],
        ),
      ),
    );
  }
}

/// TextInputFormatter that strips leading zeros from phone number input.
/// Matches frontend signup behavior: oninput replace(/^0+/, '').
class _StripLeadingZerosFormatter extends TextInputFormatter {
  @override
  TextEditingValue formatEditUpdate(
    TextEditingValue oldValue,
    TextEditingValue newValue,
  ) {
    final stripped = newValue.text.replaceFirst(RegExp(r'^0+'), '');
    if (stripped == newValue.text) return newValue;
    return TextEditingValue(
      text: stripped,
      selection: TextSelection.collapsed(offset: stripped.length),
    );
  }
}
