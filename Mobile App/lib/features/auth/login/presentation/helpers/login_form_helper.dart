import 'package:ulinmahoniapps/core/utils/validation_utils.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

/// Helper class for login form validation
class LoginFormHelper {
  /// Validates login credentials (email/phone and password)
  static String? validateLoginForm({
    required String login,
    required String password,
    required AppLocalizations localizations,
  }) {
    // Check if login is empty
    if (login.trim().isEmpty || password.trim().isEmpty) {
      return localizations.loginEmptyError;
    }

    // Check if login is valid email or phone
    if (!ValidationUtils.isValidEmailOrPhone(login)) {
      return localizations.loginInvalidFormatError;
    }

    // Check password length
    if (!ValidationUtils.isValidPasswordLength(password)) {
      return localizations.loginPasswordLengthError;
    }

    return null; // No error
  }
}

