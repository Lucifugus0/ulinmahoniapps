import 'dart:convert';
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/app_logger.dart';

/// Service for managing multiple Apple accounts locally
/// Stores accounts in SharedPreferences as JSON array
class AppleMultiAccountStorage {
  static final AppleMultiAccountStorage _instance = AppleMultiAccountStorage._internal();
  factory AppleMultiAccountStorage() => _instance;
  AppleMultiAccountStorage._internal();

  static const String _keyAccountsList = 'apple_accounts_list';
  static const String _keyActiveAccountId = 'apple_active_account_id';

  /// Get all stored Apple accounts
  Future<List<AppleAccountData>> getAllAccounts() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final accountsJson = prefs.getString(_keyAccountsList);

      if (accountsJson == null || accountsJson.isEmpty) {
        return [];
      }

      final List<dynamic> decoded = jsonDecode(accountsJson);
      final accounts = decoded
          .map((json) => AppleAccountData.fromJson(json as Map<String, dynamic>))
          .toList();

      AppLogger.d('Loaded ${accounts.length} Apple accounts', 'APPLE-STORAGE');
      return accounts;
    } catch (e, stackTrace) {
      AppLogger.e('Failed to get accounts', e, stackTrace, 'APPLE-STORAGE');
      return [];
    }
  }

  /// Add or update an Apple account
  Future<void> saveAccount(AppleAccountData account) async {
    try {
      final accounts = await getAllAccounts();

      // Check if account already exists
      final existingIndex = accounts.indexWhere(
        (a) => a.userIdentifier == account.userIdentifier
      );

      if (existingIndex != -1) {
        // Update existing account
        accounts[existingIndex] = account;
        AppLogger.d('Updated existing account: ${account.userIdentifier.substring(0, 10)}...', 'APPLE-STORAGE');
      } else {
        // Add new account
        accounts.add(account);
        AppLogger.d('Added new account: ${account.userIdentifier.substring(0, 10)}...', 'APPLE-STORAGE');
      }

      // Save back to SharedPreferences
      final prefs = await SharedPreferences.getInstance();
      final encoded = jsonEncode(accounts.map((a) => a.toJson()).toList());
      await prefs.setString(_keyAccountsList, encoded);

      // Set as active account
      await setActiveAccount(account.userIdentifier);

      AppLogger.s('Account saved successfully', 'APPLE-STORAGE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to save account', e, stackTrace, 'APPLE-STORAGE');
      rethrow;
    }
  }

  /// Get the currently active account
  Future<AppleAccountData?> getActiveAccount() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final activeAccountId = prefs.getString(_keyActiveAccountId);

      if (activeAccountId == null || activeAccountId.isEmpty) {
        AppLogger.d('No active account set', 'APPLE-STORAGE');
        return null;
      }

      final accounts = await getAllAccounts();

      try {
        final account = accounts.firstWhere(
          (a) => a.userIdentifier == activeAccountId,
        );

        AppLogger.d('Active account: ${account.email ?? "no email"}', 'APPLE-STORAGE');
        return account;
      } catch (e) {
        AppLogger.w('Active account not found in list: $activeAccountId', 'APPLE-STORAGE');
        return null;
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to get active account', e, stackTrace, 'APPLE-STORAGE');
      return null;
    }
  }

  /// Set active account by userIdentifier
  Future<void> setActiveAccount(String userIdentifier) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.setString(_keyActiveAccountId, userIdentifier);
      AppLogger.s('Active account set: ${userIdentifier.substring(0, 10)}...', 'APPLE-STORAGE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to set active account', e, stackTrace, 'APPLE-STORAGE');
    }
  }

  /// Remove an account
  Future<void> removeAccount(String userIdentifier) async {
    try {
      final accounts = await getAllAccounts();
      accounts.removeWhere((a) => a.userIdentifier == userIdentifier);

      final prefs = await SharedPreferences.getInstance();
      final encoded = jsonEncode(accounts.map((a) => a.toJson()).toList());
      await prefs.setString(_keyAccountsList, encoded);

      // If removed account was active, clear active account
      final activeId = prefs.getString(_keyActiveAccountId);
      if (activeId == userIdentifier) {
        await prefs.remove(_keyActiveAccountId);

        // Set first account as active if available
        if (accounts.isNotEmpty) {
          await setActiveAccount(accounts.first.userIdentifier);
        }
      }

      AppLogger.s('Account removed: ${userIdentifier.substring(0, 10)}...', 'APPLE-STORAGE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to remove account', e, stackTrace, 'APPLE-STORAGE');
    }
  }

  /// Clear all accounts
  Future<void> clearAllAccounts() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_keyAccountsList);
      await prefs.remove(_keyActiveAccountId);
      AppLogger.s('All accounts cleared', 'APPLE-STORAGE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to clear accounts', e, stackTrace, 'APPLE-STORAGE');
    }
  }

  /// Check if account exists
  Future<bool> accountExists(String userIdentifier) async {
    final accounts = await getAllAccounts();
    return accounts.any((a) => a.userIdentifier == userIdentifier);
  }

  /// Get account count
  Future<int> getAccountCount() async {
    final accounts = await getAllAccounts();
    return accounts.length;
  }
}

/// Model for Apple account data stored locally
class AppleAccountData {
  final String userIdentifier;
  final String? email;
  final String? givenName;
  final String? familyName;
  final int loginTimestamp;
  final int lastActiveTimestamp;
  final int? userId; // Backend user ID for API validation
  final bool? isEmailVerified; // Email verification status (extra security layer)

  AppleAccountData({
    required this.userIdentifier,
    this.email,
    this.givenName,
    this.familyName,
    required this.loginTimestamp,
    required this.lastActiveTimestamp,
    this.userId,
    this.isEmailVerified,
  });

  /// Get full name from given and family name
  String get fullName {
    final first = givenName ?? '';
    final last = familyName ?? '';
    final name = '$first $last'.trim();
    return name.isEmpty ? 'Apple User' : name;
  }

  /// Get display email with fallback
  String get displayEmail => email ?? 'no-email@apple.com';

  /// Check if email is verified
  /// If userId exists, we assume verified (user successfully logged in before)
  /// Can be overridden by isEmailVerified field for extra security
  bool get isVerified => isEmailVerified ?? (userId != null);

  /// Create from JSON
  factory AppleAccountData.fromJson(Map<String, dynamic> json) {
    return AppleAccountData(
      userIdentifier: json['userIdentifier'] as String,
      email: json['email'] as String?,
      givenName: json['givenName'] as String?,
      familyName: json['familyName'] as String?,
      loginTimestamp: json['loginTimestamp'] as int,
      lastActiveTimestamp: json['lastActiveTimestamp'] as int? ?? json['loginTimestamp'] as int,
      userId: json['userId'] as int?,
      isEmailVerified: json['isEmailVerified'] as bool?,
    );
  }

  /// Convert to JSON
  Map<String, dynamic> toJson() {
    return {
      'userIdentifier': userIdentifier,
      'email': email,
      'givenName': givenName,
      'familyName': familyName,
      'loginTimestamp': loginTimestamp,
      'lastActiveTimestamp': lastActiveTimestamp,
      'userId': userId,
      'isEmailVerified': isEmailVerified,
    };
  }

  /// Create a copy with updated fields
  AppleAccountData copyWith({
    String? userIdentifier,
    String? email,
    String? givenName,
    String? familyName,
    int? loginTimestamp,
    int? lastActiveTimestamp,
    int? userId,
    bool? isEmailVerified,
  }) {
    return AppleAccountData(
      userIdentifier: userIdentifier ?? this.userIdentifier,
      email: email ?? this.email,
      givenName: givenName ?? this.givenName,
      familyName: familyName ?? this.familyName,
      loginTimestamp: loginTimestamp ?? this.loginTimestamp,
      lastActiveTimestamp: lastActiveTimestamp ?? this.lastActiveTimestamp,
      userId: userId ?? this.userId,
      isEmailVerified: isEmailVerified ?? this.isEmailVerified,
    );
  }

  @override
  String toString() {
    return 'AppleAccountData(email: $displayEmail, name: $fullName, userIdentifier: ${userIdentifier.substring(0, 10)}...)';
  }
}
