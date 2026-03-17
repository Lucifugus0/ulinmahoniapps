import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'dart:convert';
import 'package:sign_in_with_apple/sign_in_with_apple.dart';
import '../data/repositories/auth_repository.dart';
import '../model/auth_model.dart';
import '../model/apple/applesignin_model.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/services/chat_background_service.dart';
import '../../../../core/services/apple_multi_account_storage.dart';

final authRepositoryProvider = Provider<AuthRepository>((ref) {
  return AuthRepository();
});

class AuthState {
  final bool isLoggedIn;
  final AsyncValue<User?> user; 
  final String? token;
  final String? errorMessage;

  AuthState({
    this.isLoggedIn = false,
    this.user = const AsyncValue.data(null),
    this.token,
    this.errorMessage,
  });

  AuthState copyWith({
    bool? isLoggedIn,
    AsyncValue<User?>? user,
    String? token,
    String? errorMessage,
  }) {
    return AuthState(
      isLoggedIn: isLoggedIn ?? this.isLoggedIn,
      user: user ?? this.user,
      token: token ?? this.token,
      errorMessage: errorMessage ?? this.errorMessage,
    );
  }
}


class AuthNotifier extends StateNotifier<AuthState> {
  AuthNotifier(this.ref) : super(AuthState());

  final Ref ref;


  Future<void> checkAuthStatus() async {
    final repository = ref.read(authRepositoryProvider);

    final storedToken = await repository.getTokenLocally();
    final storedUserMap = await repository.getUserLocally();

    if (storedToken == null || storedToken.isEmpty || storedUserMap == null) {
      state = AuthState();
      AppLogger.i('No local auth data found, user not logged in', 'AUTH-PROVIDER');

      // Clean up partial data if any
      if (storedToken != null || storedUserMap != null) {
        await repository.logout();
      }
      return;
    }

    AppLogger.i('Local auth data found, loading user profile', 'AUTH-PROVIDER');

    try {
      final User localUser = User.fromJson(storedUserMap);
      state = state.copyWith(
        isLoggedIn: true,
        user: AsyncValue<User?>.data(localUser),
        token: storedToken,
      );

      AppLogger.s('Local user loaded, validating with server', 'AUTH-PROVIDER');

      // Save user ID for background service
      final prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', localUser.id);

      // Start background polling for chat notifications
      await ChatBackgroundService().startPolling();
      AppLogger.s('Background polling restored for user ${localUser.id}', 'AUTH-PROVIDER');

      // Validate and refresh from server
      await refreshUserProfile();

    } catch (e) {
      AppLogger.e('Failed to parse local user data, logging out', e, StackTrace.current, 'AUTH-PROVIDER');
      await repository.logout();
      state = AuthState();
    }
  }


  Future<void> refreshUserProfile() async {
    final repository = ref.read(authRepositoryProvider);

    final currentUserId = state.user.value?.id;

    if (!state.isLoggedIn || currentUserId == null) {
      AppLogger.w('Cannot refresh profile - user not logged in', 'AUTH-PROVIDER');
      return;
    }

    AppLogger.i('Refreshing profile for user ID: $currentUserId', 'AUTH-PROVIDER');

    final result = await repository.fetchUserProfileById(currentUserId);

    switch (result) {
      case Success(:final data):
        // Check if email is verified
        if (!data.isEmailVerified) {
          AppLogger.w('Email not verified for user ID: $currentUserId, logging out', 'AUTH-PROVIDER');
          await logout();
          return;
        }

        // Update local storage
        await repository.updateUserProfileLocally(data.toJson());

        // Update state
        state = state.copyWith(user: AsyncValue<User?>.data(data));
        AppLogger.s('Profile refreshed successfully', 'AUTH-PROVIDER');

      case Failure(:final errorType, :final message):
        AppLogger.w('Failed to refresh profile - $errorType: $message', 'AUTH-PROVIDER');

        // Handle unauthorized - logout user
        if (errorType == ApiErrorType.unauthorized) {
          AppLogger.w('Token expired, logging out user', 'AUTH-PROVIDER');
          await logout();
        } else {
          // For other errors, keep previous user data but mark as error
          state = state.copyWith(
            user: AsyncValue<User?>.error(Exception(message), StackTrace.current).copyWithPrevious(state.user),
          );
        }
    }
  }

  Future<void> setUser(User? newUser) async {
    final repository = ref.read(authRepositoryProvider);

    if (newUser != null) {
      await repository.updateUserProfileLocally(newUser.toJson());
      state = state.copyWith(user: AsyncValue.data(newUser), isLoggedIn: true);
      AppLogger.s('User updated in state and SharedPreferences', 'AUTH-PROVIDER');
    } else {
      await repository.clearLocalUser();
      state = state.copyWith(user: const AsyncValue.data(null), isLoggedIn: false, token: null);
      AppLogger.s('User cleared from state and SharedPreferences', 'AUTH-PROVIDER');
    }
  }

  /// Set user in memory only (for session without Remember Me)
  /// Does NOT save to SharedPreferences
  void setUserInMemoryOnly(User newUser) {
    state = state.copyWith(
      user: AsyncValue.data(newUser),
      isLoggedIn: true,
    );
    AppLogger.s('User set in memory only (no SharedPreferences)', 'AUTH-PROVIDER');
  }

  Future<void> login(String login, String password, {required bool rememberMe}) async {
    state = state.copyWith(user: const AsyncValue.loading());
    final repository = ref.read(authRepositoryProvider);

    final result = await repository.login(login, password);

    switch (result) {
      case Success(:final data):
        final user = data.user;

        if (user != null) {
          AppLogger.s('Login successful for user: ${user.username}', 'AUTH-PROVIDER');

          // Save credentials if Remember Me is checked
          await repository.saveLoginCredentials(
            token: data.token,
            user: user,
            shouldRemember: rememberMe,
          );

          // Save user ID for background service
          final prefs = await SharedPreferences.getInstance();
          await prefs.setInt('user_id', user.id);

          // Start background polling for chat notifications
          await ChatBackgroundService().startPolling();
          AppLogger.s('Background polling started for user ${user.id}', 'AUTH-PROVIDER');

          state = state.copyWith(
            isLoggedIn: true,
            user: AsyncValue.data(user),
            token: data.token,
          );
        } else {
          AppLogger.w('Login response missing user data', 'AUTH-PROVIDER');

          state = state.copyWith(
            isLoggedIn: false,
            user: AsyncValue.error(Exception('Login response missing user data'), StackTrace.current),
            token: null,
            errorMessage: 'Login gagal. Data user tidak ditemukan.',
          );
        }

      case Failure(:final errorType, :final message):
        AppLogger.w('Login failed - $errorType: $message', 'AUTH-PROVIDER');

        state = state.copyWith(
          isLoggedIn: false,
          user: AsyncValue.error(Exception(message), StackTrace.current),
          token: null,
          errorMessage: message,
        );
    }
  }

  /// Check if there are saved Apple accounts for quick login
  Future<bool> hasAppleAccounts() async {
    try {
      final storage = AppleMultiAccountStorage();
      final accounts = await storage.getAllAccounts();
      return accounts.isNotEmpty;
    } catch (e) {
      return false;
    }
  }

  /// Quick login with existing Apple account (without re-authentication)
  Future<void> quickLoginWithAppleAccount(AppleAccountData account) async {
    try {
      AppLogger.i('Quick login with Apple account: ${account.displayEmail}', 'AUTH-PROVIDER');

      final repository = ref.read(authRepositoryProvider);
      final storage = AppleMultiAccountStorage();

      // Update last active timestamp and set as active
      final updatedAccount = account.copyWith(
        lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
      );
      await storage.saveAccount(updatedAccount);

      // Create User object
      // Use saved backend userId if available, otherwise fallback to hash
      final userId = account.userId ?? account.userIdentifier.hashCode;
      final user = User(
        id: userId,
        username: account.displayEmail,
        email: account.displayEmail,
        name: account.fullName,
        profilePhotoUrl: '',
        phoneNumber: '',
        firstName: account.givenName ?? '',
        lastName: account.familyName ?? '',
        appleUserId: account.userIdentifier,
      );

      // Save credentials to repository
      await repository.saveLoginCredentials(
        token: 'apple_token',
        user: user,
        shouldRemember: true,
      );

      // Save user ID for background service
      final prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', user.id);

      // Start background polling for chat notifications
      await ChatBackgroundService().startPolling();
      AppLogger.s('Background polling started for Apple user ${user.id}', 'AUTH-PROVIDER');

      state = state.copyWith(
        isLoggedIn: true,
        user: AsyncValue.data(user),
        token: 'apple_token',
      );

      AppLogger.s('Quick login successful for ${account.displayEmail}', 'AUTH-PROVIDER');

    } catch (e, stackTrace) {
      AppLogger.e('Quick login failed', e, stackTrace, 'AUTH-PROVIDER');

      state = state.copyWith(
        isLoggedIn: false,
        user: AsyncValue.error(e, stackTrace),
        token: null,
        errorMessage: 'Login gagal: ${e.toString()}',
      );
    }
  }

  /// Sign in with Apple (supports multi-account)
  Future<void> signInWithApple() async {
    try {
      AppLogger.i('Starting Apple Sign In flow', 'AUTH-PROVIDER');
      state = state.copyWith(user: const AsyncValue.loading());

      final repository = ref.read(authRepositoryProvider);

      // Request Apple Sign In credential
      final credential = await SignInWithApple.getAppleIDCredential(
        scopes: [
          AppleIDAuthorizationScopes.email,
          AppleIDAuthorizationScopes.fullName,
        ],
      );

      AppLogger.d('Apple credential received: ${credential.userIdentifier}', 'AUTH-PROVIDER');

      // Validate userIdentifier
      final userIdentifier = credential.userIdentifier;
      if (userIdentifier == null || userIdentifier.isEmpty) {
        throw Exception('Apple userIdentifier is null or empty');
      }

      final storage = AppleMultiAccountStorage();

      // Check if account already exists locally
      final existingAccount = await storage.getAllAccounts().then(
        (accounts) => accounts.where((a) => a.userIdentifier == userIdentifier).firstOrNull,
      );

      AppleAccountData accountData;

      if (existingAccount != null) {
        // Existing account - update last active timestamp
        AppLogger.i('Existing Apple account found, updating timestamp', 'AUTH-PROVIDER');

        accountData = existingAccount.copyWith(
          lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
        );
      } else {
        // New account - save full credentials
        AppLogger.i('New Apple account, saving credentials', 'AUTH-PROVIDER');

        accountData = AppleAccountData(
          userIdentifier: userIdentifier,
          email: credential.email,
          givenName: credential.givenName,
          familyName: credential.familyName,
          loginTimestamp: DateTime.now().millisecondsSinceEpoch,
          lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
        );
      }

      // Save account and set as active
      await storage.saveAccount(accountData);
      AppLogger.s('Apple account saved and set as active', 'AUTH-PROVIDER');

      // TODO: Send identityToken to backend for authentication
      // For now, create a local User object for testing
      // In production, you should call your backend API with credential.identityToken

      // Use saved backend userId if available, otherwise fallback to hash
      final userId = accountData.userId ?? accountData.userIdentifier.hashCode;
      final user = User(
        id: userId,
        username: accountData.displayEmail,
        email: accountData.displayEmail,
        name: accountData.fullName,
        profilePhotoUrl: '',
        phoneNumber: '',
        firstName: accountData.givenName ?? '',
        lastName: accountData.familyName ?? '',
        appleUserId: accountData.userIdentifier,
      );

      // Save credentials to repository (so logout can clear them)
      await repository.saveLoginCredentials(
        token: credential.identityToken ?? 'apple_token',
        user: user,
        shouldRemember: true, // Always remember Apple Sign In
      );

      // Save user ID for background service
      final prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', user.id);

      // Start background polling for chat notifications
      await ChatBackgroundService().startPolling();
      AppLogger.s('Background polling started for Apple user ${user.id}', 'AUTH-PROVIDER');

      state = state.copyWith(
        isLoggedIn: true,
        user: AsyncValue.data(user),
        token: credential.identityToken, // Use identityToken as temporary token
      );

      AppLogger.s('Apple Sign In successful for ${accountData.displayEmail}', 'AUTH-PROVIDER');

    } catch (e, stackTrace) {
      AppLogger.e('Apple Sign In failed', e, stackTrace, 'AUTH-PROVIDER');

      state = state.copyWith(
        isLoggedIn: false,
        user: AsyncValue.error(e, stackTrace),
        token: null,
        errorMessage: 'Apple Sign In gagal: ${e.toString()}',
      );
    }
  }

  /// Load active Apple account on app startup (auto-login)
  Future<void> loadActiveAppleAccount() async {
    try {
      final storage = AppleMultiAccountStorage();
      final activeAccount = await storage.getActiveAccount();

      if (activeAccount == null) {
        AppLogger.i('No active Apple account found', 'AUTH-PROVIDER');
        return;
      }

      AppLogger.i('Loading active Apple account: ${activeAccount.displayEmail}', 'AUTH-PROVIDER');

      // Create User object from stored Apple account
      // Use saved backend userId if available, otherwise fallback to hash
      final userId = activeAccount.userId ?? activeAccount.userIdentifier.hashCode;
      final user = User(
        id: userId,
        username: activeAccount.displayEmail,
        email: activeAccount.displayEmail,
        name: activeAccount.fullName,
        profilePhotoUrl: '',
        phoneNumber: '',
        firstName: activeAccount.givenName ?? '',
        lastName: activeAccount.familyName ?? '',
        appleUserId: activeAccount.userIdentifier,
      );

      // Save user ID for background service
      final prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', user.id);

      // Start background polling for chat notifications
      await ChatBackgroundService().startPolling();
      AppLogger.s('Background polling restored for Apple user ${user.id}', 'AUTH-PROVIDER');

      state = state.copyWith(
        isLoggedIn: true,
        user: AsyncValue.data(user),
        token: 'apple_token', // Placeholder token
      );

      AppLogger.s('Active Apple account loaded: ${activeAccount.displayEmail}', 'AUTH-PROVIDER');

    } catch (e, stackTrace) {
      AppLogger.e('Failed to load active Apple account', e, stackTrace, 'AUTH-PROVIDER');
    }
  }

  /// Switch to a different Apple account
  Future<void> switchAppleAccount(AppleAccountData account) async {
    try {
      AppLogger.i('Switching to Apple account: ${account.displayEmail}', 'AUTH-PROVIDER');

      final storage = AppleMultiAccountStorage();

      // Update last active timestamp and set as active
      final updatedAccount = account.copyWith(
        lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
      );
      await storage.saveAccount(updatedAccount);

      // Create User object
      // Use saved backend userId if available, otherwise fallback to hash
      final userId = account.userId ?? account.userIdentifier.hashCode;
      final user = User(
        id: userId,
        username: account.displayEmail,
        email: account.displayEmail,
        name: account.fullName,
        profilePhotoUrl: '',
        phoneNumber: '',
        firstName: account.givenName ?? '',
        lastName: account.familyName ?? '',
        appleUserId: account.userIdentifier,
      );

      // Save user ID for background service
      final prefs = await SharedPreferences.getInstance();
      await prefs.setInt('user_id', user.id);

      state = state.copyWith(
        isLoggedIn: true,
        user: AsyncValue.data(user),
        token: 'apple_token',
      );

      AppLogger.s('Switched to Apple account: ${account.displayEmail}', 'AUTH-PROVIDER');

    } catch (e, stackTrace) {
      AppLogger.e('Failed to switch Apple account', e, stackTrace, 'AUTH-PROVIDER');
    }
  }

  Future<void> logout() async {
    final repository = ref.read(authRepositoryProvider);
    final currentUser = state.user.value;

    // TODO: Uncomment when backend FCM endpoints ready
    // Delete FCM token first
    // try {
    //   await FCMService().deleteToken();
    //   AppLogger.d('FCM token deleted on logout', 'AUTH-PROVIDER');
    // } catch (e) {
    //   AppLogger.e('Failed to delete FCM token', e, null, 'AUTH-PROVIDER');
    //   // Continue with logout even if FCM deletion fails
    // }

    // Stop background polling
    await ChatBackgroundService().stopPolling();
    AppLogger.s('Background polling stopped', 'AUTH-PROVIDER');

    // Clear user ID from shared preferences
    final prefs = await SharedPreferences.getInstance();
    await prefs.remove('user_id');

    // Clear last seen messages
    await ChatBackgroundService.saveLastSeenMessages({});

    // IMPORTANT: We keep Apple account credentials in SharedPreferences
    // Only clear the session data (token, user) from repository
    // This allows quick re-login via AccountPickerPage
    if (currentUser?.appleUserId != null) {
      AppLogger.i('Apple user logout - credentials preserved for quick re-login', 'AUTH-PROVIDER');
      // Note: AppleMultiAccountStorage data is NOT cleared
      // User can re-login by selecting from AccountPickerPage
    }

    await repository.logout();

    state = AuthState();
    AppLogger.s('Logout successful - state reset, Apple credentials preserved', 'AUTH-PROVIDER');
  }
}


final authProvider = StateNotifierProvider<AuthNotifier, AuthState>((ref) {
  
  
  final notifier = AuthNotifier(ref);
  notifier.checkAuthStatus();
  return notifier;
});