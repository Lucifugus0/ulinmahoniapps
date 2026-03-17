import 'package:sign_in_with_apple/sign_in_with_apple.dart';
import '../../../../../../core/network/api_result.dart';
import '../../../../../../core/utils/app_logger.dart';
import '../../../model/apple/applesignin_model.dart';

/// Repository for Apple Sign-In operations
/// Handles only Apple SDK authentication - NO HTTP calls, NO local storage
class AppleSignInRepository {
  /// Sign in with Apple
  /// Returns AppleUserData if successful
  Future<ApiResult<AppleUserData>> signInWithApple() async {
    try {
      AppLogger.d('Starting Apple Sign-In flow', 'APPLE-SIGNIN-REPO');

      // Request Apple Sign-In with email and full name scopes
      final credential = await SignInWithApple.getAppleIDCredential(
        scopes: [
          AppleIDAuthorizationScopes.email,
          AppleIDAuthorizationScopes.fullName,
        ],
        webAuthenticationOptions: WebAuthenticationOptions(
          clientId: 'com.ulinmahoni.apps.signin',
          redirectUri: Uri.parse(
            'https://ulinmahoni.com/login',
          ),
        ),
      );

      AppLogger.d(
        'Apple Sign-In credential received',
        'APPLE-SIGNIN-REPO',
      );

      // Extract user data from credential
      // Note: email and name are only available on FIRST sign-in
      // Subsequent sign-ins will have null email/name
      final email = credential.email;
      final givenName = credential.givenName;
      final familyName = credential.familyName;
      final identityToken = credential.identityToken;
      final userIdentifier = credential.userIdentifier;

      AppLogger.d(
        'Apple credential - email: $email, givenName: $givenName, familyName: $familyName',
        'APPLE-SIGNIN-REPO',
      );
      AppLogger.d(
        'Apple credential - userIdentifier: $userIdentifier',
        'APPLE-SIGNIN-REPO',
      );

      // On first sign-in, email should be available
      if (email == null || email.isEmpty) {
        AppLogger.w(
          'Email not available from Apple Sign-In (user may have signed in before)',
          'APPLE-SIGNIN-REPO',
        );
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Email tidak tersedia. Silakan hapus akses app di Settings > Apple ID > Password & Security > Apps Using Apple ID, lalu coba lagi.',
        );
      }

      // userIdentifier should always be available, but check just in case
      if (userIdentifier == null || userIdentifier.isEmpty) {
        AppLogger.e(
          'User identifier is empty',
          null,
          null,
          'APPLE-SIGNIN-REPO',
        );
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Gagal mendapatkan identifier user dari Apple',
        );
      }

      final userData = AppleUserData(
        email: email,
        givenName: givenName,
        familyName: familyName,
        identityToken: identityToken ?? '',
        userIdentifier: userIdentifier,
      );

      AppLogger.s(
        'Apple Sign-In successful: ${userData.email}',
        'APPLE-SIGNIN-REPO',
      );

      return Success(userData);
    } on SignInWithAppleAuthorizationException catch (e) {
      AppLogger.w(
        'Apple Sign-In exception: ${e.code}',
        'APPLE-SIGNIN-REPO',
      );

      // User cancelled the sign-in - return silently without showing popup
      if (e.code == AuthorizationErrorCode.canceled) {
        AppLogger.d('User cancelled Apple Sign-In', 'APPLE-SIGNIN-REPO');
        return Failure(
          errorType: ApiErrorType.unknown,
          message: '', // Empty message = no popup shown
        );
      }

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal masuk dengan Apple: ${e.message}',
        originalError: e,
      );
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Apple Sign-In',
        e,
        stackTrace,
        'APPLE-SIGNIN-REPO',
      );

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal masuk dengan Apple: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Check if Sign in with Apple is available on this device
  /// iOS 13+ and macOS 10.15+
  Future<bool> isAvailable() async {
    try {
      return await SignInWithApple.isAvailable();
    } catch (e) {
      AppLogger.e(
        'Error checking Apple Sign-In availability',
        e,
        null,
        'APPLE-SIGNIN-REPO',
      );
      return false;
    }
  }
}
