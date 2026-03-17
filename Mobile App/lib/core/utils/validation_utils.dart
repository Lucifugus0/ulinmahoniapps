/// Validation utilities for form validation
class ValidationUtils {
  // Email regex pattern
  static final RegExp _emailRegex = RegExp(r'^[\w-\.]+@([\w-]+\.)+[\w-]{2,4}$');
  
  // Phone regex pattern (Indonesian format)
  static final RegExp _phoneRegex = RegExp(r'^(?:\+62|0|62)[2-9]\d{6,11}$');

  /// Validates email format
  static bool isValidEmail(String email) {
    return _emailRegex.hasMatch(email.trim());
  }

  /// Validates phone number format (Indonesian)
  static bool isValidPhone(String phone) {
    return _phoneRegex.hasMatch(phone.trim());
  }

  /// Validates if input is either email or phone
  static bool isValidEmailOrPhone(String input) {
    return isValidEmail(input) || isValidPhone(input);
  }

  /// Validates password length (minimum 8 characters)
  static bool isValidPasswordLength(String password) {
    return password.length >= 8;
  }

  /// Validates if password and confirm password match
  static bool doPasswordsMatch(String password, String confirmPassword) {
    return password == confirmPassword;
  }

  /// Validates if field is not empty
  static bool isNotEmpty(String? value) {
    return value != null && value.trim().isNotEmpty;
  }

  /// Validates username (alphanumeric and underscore, 3-20 chars)
  static bool isValidUsername(String username) {
    final usernameRegex = RegExp(r'^[a-zA-Z0-9_]{3,20}$');
    return usernameRegex.hasMatch(username.trim());
  }

  /// Validates name (letters and spaces, 2-50 chars)
  static bool isValidName(String name) {
    final nameRegex = RegExp(r'^[a-zA-Z\s]{2,50}$');
    return nameRegex.hasMatch(name.trim());
  }

  /// Get validation error message for email
  static String? getEmailError(String email) {
    if (!isNotEmpty(email)) {
      return 'Email tidak boleh kosong';
    }
    if (!isValidEmail(email)) {
      return 'Format email tidak valid';
    }
    return null;
  }

  /// Get validation error message for phone
  static String? getPhoneError(String phone) {
    if (!isNotEmpty(phone)) {
      return 'Nomor telepon tidak boleh kosong';
    }
    if (!isValidPhone(phone)) {
      return 'Format nomor telepon tidak valid';
    }
    return null;
  }

  /// Get validation error message for password
  static String? getPasswordError(String password) {
    if (!isNotEmpty(password)) {
      return 'Password tidak boleh kosong';
    }
    if (!isValidPasswordLength(password)) {
      return 'Password minimal 8 karakter';
    }
    return null;
  }

  /// Get validation error message for confirm password
  static String? getConfirmPasswordError(String password, String confirmPassword) {
    if (!isNotEmpty(confirmPassword)) {
      return 'Konfirmasi password tidak boleh kosong';
    }
    if (!doPasswordsMatch(password, confirmPassword)) {
      return 'Password tidak cocok';
    }
    return null;
  }

  /// Get validation error message for username
  static String? getUsernameError(String username) {
    if (!isNotEmpty(username)) {
      return 'Username tidak boleh kosong';
    }
    if (!isValidUsername(username)) {
      return 'Username harus 3-20 karakter (huruf, angka, underscore)';
    }
    return null;
  }

  /// Get validation error message for name
  static String? getNameError(String name) {
    if (!isNotEmpty(name)) {
      return 'Nama tidak boleh kosong';
    }
    if (!isValidName(name)) {
      return 'Nama harus 2-50 karakter (huruf dan spasi)';
    }
    return null;
  }
}

