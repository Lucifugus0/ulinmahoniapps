import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import '../data/repositories/google/googlesignin_repository.dart';
import '../data/repositories/google/user_filter_repository.dart';
import '../data/repositories/google/user_registration_repository.dart';
import 'auth_provider.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/network/dio_client.dart';
import '../../../../core/utils/app_logger.dart';
import '../model/google/user_registration_model.dart';
import '../model/auth_model.dart';

/// Provider for GoogleSignInRepository
final googleSignInRepositoryProvider = Provider<GoogleSignInRepository>((ref) {
  return GoogleSignInRepository();
});

/// Provider for UserFilterRepository
final userFilterRepositoryProvider = Provider<UserFilterRepository>((ref) {
  return UserFilterRepository(dioClient: DioClient());
});

/// Provider for UserRegistrationRepository
final userRegistrationRepositoryProvider =
    Provider<UserRegistrationRepository>((ref) {
  return UserRegistrationRepository(dioClient: DioClient());
});

/// Google Sign-In Result
class GoogleSignInResult {
  final bool isNewUser;
  final String message;
  final bool requiresEmailVerification;
  /// True when the user account has status = 0 (deactivated)
  final bool isAccountDeactivated;

  GoogleSignInResult({
    required this.isNewUser,
    required this.message,
    this.requiresEmailVerification = false,
    this.isAccountDeactivated = false,
  });
}

/// Migrated from StateNotifierProvider to NotifierProvider for Riverpod 3.x
final googleSignInControllerProvider =
    NotifierProvider<GoogleSignInController, AsyncValue<GoogleSignInResult?>>(GoogleSignInController.new);

/// Controller for Google Sign-In flow with auto-register
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
/// FLOW (without API login):
/// 1. Get user data from Google SDK (email, displayName, photoUrl)
/// 2. Check if user exists via GET /users?email={email}
/// 3. If user NOT exists -> Auto-register via POST /auth/register -> Show notification (must verify email)
/// 4. If user exists -> Save user data directly to local storage (no API login)
/// 5. Navigate to /home (handled by login_page.dart)
class GoogleSignInController extends Notifier<AsyncValue<GoogleSignInResult?>> {
  /// Returns initial state via build method (Riverpod 3.x pattern)
  @override
  AsyncValue<GoogleSignInResult?> build() => const AsyncValue.data(null);

  /// Sign in with Google and auto-register if needed
  /// FLOW:
  /// 1. Google SDK -> Get user data
  /// 2. GET /users?email={email} -> Check if user exists
  /// 3. If not exists -> POST /auth/register -> Show notification (must verify email)
  /// 4. If exists -> Save to local storage directly -> Navigate to home
  Future<void> signInWithGoogle({required bool rememberMe}) async {
    state = const AsyncValue.loading();

    try {
      // Step 1: Get user from Google SDK
      final googleRepo = ref.read(googleSignInRepositoryProvider);
      final googleResult = await googleRepo.signInWithGoogle();

      switch (googleResult) {
        case Failure(:final message):
          AppLogger.w('Google Sign-In failed: $message', 'GOOGLE-SIGNIN-CTRL');
          state = AsyncValue.error(message, StackTrace.current);
          return;

        case Success(:final data):
          final googleUser = data;
          final email = googleUser.email;
          final displayName = googleUser.displayName ?? 'User';
          final photoUrl = googleUser.photoUrl;

          AppLogger.d(
            'Google Sign-In successful: $email',
            'GOOGLE-SIGNIN-CTRL',
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
                'GOOGLE-SIGNIN-CTRL',
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
                  'GOOGLE-SIGNIN-CTRL',
                );

                // Create registration request from Google data
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
                      'GOOGLE-SIGNIN-CTRL',
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
                      'GOOGLE-SIGNIN-CTRL',
                    );

                    // Note: Do NOT save user data to local storage for new users
                    // They need to verify email first and login manually

                    // Return result for new user
                    AppLogger.i(
                      'User registered but NOT logged in. Email verification required.',
                      'GOOGLE-SIGNIN-CTRL',
                    );
                    state = AsyncValue.data(GoogleSignInResult(
                      isNewUser: true,
                      message: 'Registration successful. Please check your email to verify your account.',
                    ));
                }
              } else {
                // User already exists
                AppLogger.i(
                  'User already exists: ${existingUser.email}',
                  'GOOGLE-SIGNIN-CTRL',
                );

                // Step 3.5: Check if email is verified
                if (!existingUser.isEmailVerified) {
                  AppLogger.w(
                    'Email not verified for user: ${existingUser.email}',
                    'GOOGLE-SIGNIN-CTRL',
                  );
                  state = AsyncValue.data(GoogleSignInResult(
                    isNewUser: false,
                    message: 'EMAIL_NOT_VERIFIED', // Special message for UI to detect
                    requiresEmailVerification: true,
                  ));
                  return;
                }

                // Step 3.6: Check if account is active (status == 0 means deactivated)
                if (existingUser.status == 0) {
                  AppLogger.w(
                    'Account deactivated for user: ${existingUser.email}',
                    'GOOGLE-SIGNIN-CTRL',
                  );
                  // Sign out from Google so account is not cached on device
                  final googleRepo = ref.read(googleSignInRepositoryProvider);
                  await googleRepo.signOut();

                  state = AsyncValue.data(GoogleSignInResult(
                    isNewUser: false,
                    message: 'ACCOUNT_DEACTIVATED',
                    isAccountDeactivated: true,
                  ));
                  return;
                }

                // Step 4: Handle Remember Me
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
                    profilePhotoUrl: existingUser.profilePhotoUrl ?? photoUrl,
                    emailVerifiedAt: existingUser.emailVerifiedAt,
                    rememberMe: true,
                  );

                  // Update auth provider from SharedPreferences
                  final authNotifier = ref.read(authProvider.notifier);
                  authNotifier.checkAuthStatus();

                  AppLogger.s('Google Sign-In with Remember Me completed', 'GOOGLE-SIGNIN-CTRL');
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
                    profilePhotoUrl: existingUser.profilePhotoUrl ?? photoUrl ?? '',
                    profilePhotoPath: null,
                    emailVerifiedAt: existingUser.emailVerifiedAt,
                  );

                  // Set user in memory only (no SharedPreferences)
                  final authNotifier = ref.read(authProvider.notifier);
                  authNotifier.setUserInMemoryOnly(user);

                  AppLogger.s('Google Sign-In without Remember Me completed (memory only)', 'GOOGLE-SIGNIN-CTRL');
                }

                // Return result for existing user
                state = AsyncValue.data(GoogleSignInResult(
                  isNewUser: false,
                  message: 'You have successfully signed in with Google!',
                ));
              }
          }
      }
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error in Google Sign-In flow',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-CTRL',
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

      // Save token (use email as token for Google Sign-In users)
      // This is a dummy token since we don't have actual token from backend
      final dummyToken = 'google_signin_$email';

      // Save to SharedPreferences for persistent login
      await prefs.setString('auth_token', dummyToken);
      await prefs.setString('user_profile', jsonEncode(userData));
      AppLogger.d('User data saved to local storage (Remember Me enabled)', 'GOOGLE-SIGNIN-CTRL');
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error saving user data to local storage',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-CTRL',
      );
    }
  }

  /// Sign out from Google
  Future<void> signOut() async {
    try {
      final googleRepo = ref.read(googleSignInRepositoryProvider);
      await googleRepo.signOut();

      AppLogger.s('Google Sign-Out successful', 'GOOGLE-SIGNIN-CTRL');
      state = const AsyncValue.data(null);
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Google Sign-Out',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-CTRL',
      );
      state = AsyncValue.error(
        'Gagal keluar dari Google: ${e.toString()}',
        stackTrace,
      );
    }
  }

  /// Disconnect from Google (revoke access)
  Future<void> disconnect() async {
    try {
      final googleRepo = ref.read(googleSignInRepositoryProvider);
      await googleRepo.disconnect();

      AppLogger.s('Google disconnect successful', 'GOOGLE-SIGNIN-CTRL');
      state = const AsyncValue.data(null);
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Google disconnect',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-CTRL',
      );
      state = AsyncValue.error(
        'Gagal memutuskan Google: ${e.toString()}',
        stackTrace,
      );
    }
  }
}
