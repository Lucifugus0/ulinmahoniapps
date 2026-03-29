import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../data/repositories/apple/applesignin_repository.dart';
import '../data/repositories/apple/email_verification_validator.dart';
import '../data/repositories/google/user_filter_repository.dart';
import '../data/repositories/google/user_registration_repository.dart';
import 'auth_provider.dart';
import 'googlesignin_provider.dart'; // Import to reuse providers
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/services/apple_multi_account_storage.dart';
import '../model/google/user_registration_model.dart';
import '../model/auth_model.dart';

/// Provider for AppleSignInRepository
final appleSignInRepositoryProvider = Provider<AppleSignInRepository>((ref) {
  return AppleSignInRepository();
});

/// Provider for EmailVerificationValidator
final emailVerificationValidatorProvider = Provider<EmailVerificationValidator>((ref) {
  return EmailVerificationValidator();
});

/// Apple Sign-In Result
class AppleSignInResult {
  final bool isNewUser;
  final String message;
  final bool requiresEmailVerification;
  final bool showAccountPicker;
  final List<AppleAccountData>? accounts;

  AppleSignInResult({
    required this.isNewUser,
    required this.message,
    this.requiresEmailVerification = false,
    this.showAccountPicker = false,
    this.accounts,
  });
}

/// Migrated from StateNotifierProvider to NotifierProvider for Riverpod 3.x
final appleSignInControllerProvider =
    NotifierProvider<AppleSignInController, AsyncValue<AppleSignInResult?>>(AppleSignInController.new);

/// Controller for Apple Sign-In flow with auto-register
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
/// FLOW (same as Google Sign-In):
/// 1. Get user data from Apple SDK (email, givenName, familyName)
/// 2. Check if user exists via GET /users?email={email}
/// 3. If user NOT exists -> Auto-register via POST /auth/register -> Show notification (must verify email)
/// 4. If user exists -> Save user data directly to local storage (no API login)
/// 5. Navigate to /home (handled by login_page.dart)
class AppleSignInController extends Notifier<AsyncValue<AppleSignInResult?>> {
  /// Returns initial state via build method (Riverpod 3.x pattern)
  @override
  AsyncValue<AppleSignInResult?> build() => const AsyncValue.data(null);

  /// Check for existing Apple accounts and show picker if multiple accounts exist
  /// This is called BEFORE calling Apple Sign-In SDK
  Future<void> checkExistingAccounts() async {
    try {
      final storage = AppleMultiAccountStorage();
      final accounts = await storage.getAllAccounts();

      if (accounts.isEmpty) {
        // No existing accounts - proceed with normal Apple Sign-In
        AppLogger.d('No existing Apple accounts found', 'APPLE-SIGNIN-CTRL');
        state = const AsyncValue.data(null);
      } else {
        // Has existing accounts (1 or more) - ALWAYS show account picker
        // User should explicitly choose to login with saved account or use new account
        // This prevents auto-login after logout
        AppLogger.d('Found ${accounts.length} Apple account(s), showing account picker', 'APPLE-SIGNIN-CTRL');
        state = AsyncValue.data(AppleSignInResult(
          isNewUser: false,
          message: 'SHOW_ACCOUNT_PICKER',
          showAccountPicker: true,
          accounts: accounts,
        ));
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error checking existing accounts', e, stackTrace, 'APPLE-SIGNIN-CTRL');
      // On error, proceed with normal Apple Sign-In
      state = const AsyncValue.data(null);
    }
  }

  /// Login with an existing Apple account from account picker
  Future<void> loginWithExistingAccount(AppleAccountData account, {required bool rememberMe}) async {
    state = const AsyncValue.loading();

    try {
      // Check if userId exists
      if (account.userId == null) {
        AppLogger.e('Cannot login: account has no userId', null, null, 'APPLE-SIGNIN-CTRL');
        state = AsyncValue.error(
          'Data akun tidak lengkap. Silakan login ulang dengan Apple.',
          StackTrace.current,
        );
        return;
      }

      // Validate email verification via API
      final validator = ref.read(emailVerificationValidatorProvider);
      final verificationResult = await validator.isEmailVerified(account.userId!);

      switch (verificationResult) {
        case Success(:final data):
          final isVerified = data;

          if (!isVerified) {
            // Email not verified - show popup
            AppLogger.w('Account not verified: ${account.email}', 'APPLE-SIGNIN-CTRL');
            state = AsyncValue.data(AppleSignInResult(
              isNewUser: false,
              message: 'EMAIL_NOT_VERIFIED',
              requiresEmailVerification: true,
            ));
            return;
          }

          // Email verified - proceed with login
          await _loginWithExistingAccount(account, rememberMe: rememberMe);

        case Failure(:final message):
          AppLogger.e('Failed to check verification status: $message', null, null, 'APPLE-SIGNIN-CTRL');
          state = AsyncValue.error(
            'Gagal memeriksa status verifikasi: $message',
            StackTrace.current,
          );
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error logging in with existing account', e, stackTrace, 'APPLE-SIGNIN-CTRL');
      state = AsyncValue.error(
        'Terjadi kesalahan: ${e.toString()}',
        stackTrace,
      );
    }
  }

  /// Internal method to login with existing account
  /// Called after email verification is confirmed
  Future<void> _loginWithExistingAccount(AppleAccountData account, {bool rememberMe = true}) async {
    try {
      // Get full user data from API
      final userFilterRepo = ref.read(userFilterRepositoryProvider);
      final filterResult = await userFilterRepo.getUserByEmail(account.email ?? '');

      switch (filterResult) {
        case Failure(:final message):
          AppLogger.e('Failed to fetch user data: $message', null, null, 'APPLE-SIGNIN-CTRL');
          state = AsyncValue.error(
            'Gagal mengambil data user: $message',
            StackTrace.current,
          );
          return;

        case Success(:final data):
          final existingUser = data;

          if (existingUser == null) {
            AppLogger.e('User not found for email: ${account.email}', null, null, 'APPLE-SIGNIN-CTRL');
            state = AsyncValue.error(
              'Data user tidak ditemukan. Silakan login ulang.',
              StackTrace.current,
            );
            return;
          }

          // Update lastActiveTimestamp and verification status in storage
          final updatedAccount = account.copyWith(
            lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
            isEmailVerified: true, // User successfully logged in = verified
          );
          final storage = AppleMultiAccountStorage();
          await storage.saveAccount(updatedAccount);

          // Handle Remember Me
          if (rememberMe) {
            // Save to SharedPreferences for persistent login
            await _saveUserDataToLocalStorage(
              userId: existingUser.id,
              email: existingUser.email,
              name: existingUser.name,
              firstName: existingUser.firstName,
              lastName: existingUser.lastName,
              username: existingUser.username,
              phoneNumber: existingUser.phoneNumber,
              profilePhotoUrl: existingUser.profilePhotoUrl,
              emailVerifiedAt: existingUser.emailVerifiedAt,
              rememberMe: true,
            );

            // Update auth provider from SharedPreferences
            final authNotifier = ref.read(authProvider.notifier);
            authNotifier.checkAuthStatus();

            AppLogger.s('Login with existing Apple account (Remember Me) completed', 'APPLE-SIGNIN-CTRL');
          } else {
            // Don't save to SharedPreferences - only set in memory
            final user = User(
              id: existingUser.id,
              username: existingUser.username,
              email: existingUser.email,
              name: existingUser.name,
              firstName: existingUser.firstName,
              lastName: existingUser.lastName,
              phoneNumber: existingUser.phoneNumber ?? '',
              profilePhotoUrl: existingUser.profilePhotoUrl ?? '',
              profilePhotoPath: null,
              emailVerifiedAt: existingUser.emailVerifiedAt,
            );

            // Set user in memory only (no SharedPreferences)
            final authNotifier = ref.read(authProvider.notifier);
            authNotifier.setUserInMemoryOnly(user);

            AppLogger.s('Login with existing Apple account (memory only) completed', 'APPLE-SIGNIN-CTRL');
          }

          // Return success result
          state = AsyncValue.data(AppleSignInResult(
            isNewUser: false,
            message: 'Login berhasil dengan akun Apple!',
          ));
      }
    } catch (e, stackTrace) {
      AppLogger.e('Error in _loginWithExistingAccount', e, stackTrace, 'APPLE-SIGNIN-CTRL');
      state = AsyncValue.error(
        'Terjadi kesalahan: ${e.toString()}',
        stackTrace,
      );
    }
  }

  /// Sign in with Apple and auto-register if needed
  /// FLOW:
  /// 1. Apple SDK -> Get user data
  /// 2. GET /users?email={email} -> Check if user exists
  /// 3. If not exists -> POST /auth/register -> Show notification (must verify email)
  /// 4. If exists -> Save to local storage directly -> Navigate to home
  Future<void> signInWithApple({required bool rememberMe}) async {
    state = const AsyncValue.loading();

    try {
      // Step 1: Get user from Apple SDK
      final appleRepo = ref.read(appleSignInRepositoryProvider);
      final appleResult = await appleRepo.signInWithApple();

      switch (appleResult) {
        case Failure(:final message):
          AppLogger.w('Apple Sign-In failed: $message', 'APPLE-SIGNIN-CTRL');
          state = AsyncValue.error(message, StackTrace.current);
          return;

        case Success(:final data):
          final appleUser = data;
          final email = appleUser.email;
          final displayName = appleUser.displayName;

          AppLogger.d(
            'Apple Sign-In successful: $email',
            'APPLE-SIGNIN-CTRL',
          );

          // Step 2: Check if user exists via GET /users?email={email}
          final userFilterRepo = ref.read(userFilterRepositoryProvider);
          final filterResult = await userFilterRepo.getUserByEmail(email);

          switch (filterResult) {
            case Failure(:final message):
              AppLogger.e(
                'User filter failed: $message',
                null,
                null,
                'APPLE-SIGNIN-CTRL',
              );
              state = AsyncValue.error(
                'Gagal memeriksa data user: $message',
                StackTrace.current,
              );
              return;

            case Success(:final data):
              final existingUser = data;

              // Step 3: Auto-register if user doesn't exist
              if (existingUser == null) {
                AppLogger.i(
                  'User not found, auto-registering: $email',
                  'APPLE-SIGNIN-CTRL',
                );

                // Create registration request from Apple data
                final registrationRequest =
                    UserRegistrationRequest.fromGoogleSignIn(
                  email: email,
                  displayName: displayName,
                );

                final userRegRepo = ref.read(userRegistrationRepositoryProvider);
                final registerResult =
                    await userRegRepo.registerUser(registrationRequest);

                switch (registerResult) {
                  case Failure(:final message):
                    AppLogger.e(
                      'Auto-registration failed: $message',
                      null,
                      null,
                      'APPLE-SIGNIN-CTRL',
                    );
                    state = AsyncValue.error(
                      'Gagal registrasi otomatis: $message',
                      StackTrace.current,
                    );
                    return;

                  case Success(:final data):
                    final registeredUser = data;
                    AppLogger.s(
                      'Auto-registration successful: ${registeredUser.email}',
                      'APPLE-SIGNIN-CTRL',
                    );

                    // Save Apple account to AppleMultiAccountStorage for quick re-login
                    // This allows user to quick re-login after email verification
                    try {
                      final storage = AppleMultiAccountStorage();
                      final appleAccountData = AppleAccountData(
                        userIdentifier: appleUser.userIdentifier,
                        email: email,
                        givenName: appleUser.givenName,
                        familyName: appleUser.familyName,
                        loginTimestamp: DateTime.now().millisecondsSinceEpoch,
                        lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
                        userId: registeredUser.id, // Save backend user ID
                        isEmailVerified: false, // New user - not verified yet
                      );
                      await storage.saveAccount(appleAccountData);
                      AppLogger.s('Apple account saved to AppleMultiAccountStorage for new user: $email (userId: ${registeredUser.id})', 'APPLE-SIGNIN-CTRL');
                    } catch (e, stackTrace) {
                      AppLogger.e('Failed to save Apple account to storage', e, stackTrace, 'APPLE-SIGNIN-CTRL');
                      // Don't fail registration if storage save fails
                    }

                    // Note: Do NOT save user data to AuthRepository for new users
                    // They need to verify email first and login manually

                    // Return result for new user
                    AppLogger.i(
                      'User registered but NOT logged in. Email verification required.',
                      'APPLE-SIGNIN-CTRL',
                    );
                    state = AsyncValue.data(AppleSignInResult(
                      isNewUser: true,
                      message: 'Registration successful. Please check your email to verify your account.',
                    ));
                }
              } else {
                // User already exists
                AppLogger.i(
                  'User already exists: ${existingUser.email}',
                  'APPLE-SIGNIN-CTRL',
                );

                // Step 3.5: Check if email is verified
                if (!existingUser.isEmailVerified) {
                  AppLogger.w(
                    'Email not verified for user: ${existingUser.email}',
                    'APPLE-SIGNIN-CTRL',
                  );
                  state = AsyncValue.data(AppleSignInResult(
                    isNewUser: false,
                    message: 'EMAIL_NOT_VERIFIED', // Special message for UI to detect
                    requiresEmailVerification: true,
                  ));
                  return;
                }

                // Step 4: Save to AppleMultiAccountStorage FIRST (ALWAYS, regardless of Remember Me)
                try {
                  final storage = AppleMultiAccountStorage();
                  final appleAccountData = AppleAccountData(
                    userIdentifier: appleUser.userIdentifier,
                    email: email,
                    givenName: appleUser.givenName,
                    familyName: appleUser.familyName,
                    loginTimestamp: DateTime.now().millisecondsSinceEpoch,
                    lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
                    userId: existingUser.id, // Save backend user ID
                    isEmailVerified: true, // Existing verified user
                  );
                  await storage.saveAccount(appleAccountData);
                  AppLogger.s('Apple account saved to AppleMultiAccountStorage: $email (userId: ${existingUser.id})', 'APPLE-SIGNIN-CTRL');
                } catch (e, stackTrace) {
                  AppLogger.e('Failed to save Apple account to storage', e, stackTrace, 'APPLE-SIGNIN-CTRL');
                  // Don't fail login if storage save fails
                }

                // Step 5: Handle Remember Me
                if (rememberMe) {
                  // Save to SharedPreferences for persistent login
                  await _saveUserDataToLocalStorage(
                    userId: existingUser.id,
                    email: existingUser.email,
                    name: existingUser.name,
                    firstName: existingUser.firstName,
                    lastName: existingUser.lastName,
                    username: existingUser.username,
                    phoneNumber: existingUser.phoneNumber,
                    profilePhotoUrl: existingUser.profilePhotoUrl,
                    emailVerifiedAt: existingUser.emailVerifiedAt,
                    rememberMe: true,
                  );

                  // Update auth provider from SharedPreferences
                  final authNotifier = ref.read(authProvider.notifier);
                  authNotifier.checkAuthStatus();

                  AppLogger.s('Apple Sign-In with Remember Me completed', 'APPLE-SIGNIN-CTRL');
                } else {
                  // Don't save to SharedPreferences - only set in memory
                  final user = User(
                    id: existingUser.id,
                    username: existingUser.username,
                    email: existingUser.email,
                    name: existingUser.name,
                    firstName: existingUser.firstName,
                    lastName: existingUser.lastName,
                    phoneNumber: existingUser.phoneNumber ?? '',
                    profilePhotoUrl: existingUser.profilePhotoUrl ?? '',
                    profilePhotoPath: null,
                    emailVerifiedAt: existingUser.emailVerifiedAt,
                  );

                  // Set user in memory only (no SharedPreferences)
                  final authNotifier = ref.read(authProvider.notifier);
                  authNotifier.setUserInMemoryOnly(user);

                  AppLogger.s('Apple Sign-In without Remember Me completed (memory only)', 'APPLE-SIGNIN-CTRL');
                }

                // Return result for existing user
                state = AsyncValue.data(AppleSignInResult(
                  isNewUser: false,
                  message: 'You have successfully signed in with Apple!',
                ));
              }
          }
      }
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error in Apple Sign-In flow',
        e,
        stackTrace,
        'APPLE-SIGNIN-CTRL',
      );
      state = AsyncValue.error(
        'Terjadi kesalahan: ${e.toString()}',
        stackTrace,
      );
    }
  }


  /// Save user data to local storage for Remember Me
  /// Only called when rememberMe = true
  Future<void> _saveUserDataToLocalStorage({
    required int userId,
    required String email,
    required String name,
    required String firstName,
    required String lastName,
    required String username,
    String? phoneNumber,
    String? profilePhotoUrl,
    String? emailVerifiedAt,
    required bool rememberMe,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      // Create user data map (must match User model in auth_model.dart)
      final userData = {
        'id': userId, // Must be 'id' not 'idrec' to match User.fromJson
        'name': name,
        'first_name': firstName,
        'last_name': lastName,
        'username': username,
        'email': email,
        'phone_number': phoneNumber,
        'profile_photo_url': profilePhotoUrl,
        'profile_photo_path': null,
        'email_verified_at': emailVerifiedAt,
      };

      // Save token (use email as token for Apple Sign-In users)
      // This is a dummy token since we don't have actual token from backend
      final dummyToken = 'apple_signin_$email';

      // Save to SharedPreferences for persistent login
      await prefs.setString('auth_token', dummyToken);
      await prefs.setString('user_profile', jsonEncode(userData));
      AppLogger.d('User data saved to local storage (Remember Me enabled)', 'APPLE-SIGNIN-CTRL');
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error saving user data to local storage',
        e,
        stackTrace,
        'APPLE-SIGNIN-CTRL',
      );
    }
  }
}
