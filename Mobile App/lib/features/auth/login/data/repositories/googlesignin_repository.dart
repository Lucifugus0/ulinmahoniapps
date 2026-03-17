import 'package:google_sign_in/google_sign_in.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../model/google/googlesignin_model.dart';

/// Repository for Google Sign-In operations
/// Handles only Google SDK authentication - NO HTTP calls, NO local storage
class GoogleSignInRepository {
  final GoogleSignIn _googleSignIn;
  bool _isInitialized = false;

  GoogleSignInRepository({GoogleSignIn? googleSignIn})
      : _googleSignIn = googleSignIn ?? GoogleSignIn.instance;

  /// Initialize Google Sign-In (must be called before any other methods)
  Future<void> _ensureInitialized() async {
    if (_isInitialized) return;

    try {
      await _googleSignIn.initialize(
        serverClientId: '398455187942-p789o6qv18v71ap08abkvjnam7qct2t5.apps.googleusercontent.com',
      );
      _isInitialized = true;
      AppLogger.d('Google Sign-In initialized with serverClientId', 'GOOGLE-SIGNIN-REPO');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to initialize Google Sign-In', e, stackTrace, 'GOOGLE-SIGNIN-REPO');
      rethrow;
    }
  }

  /// Sign in with Google
  /// Returns GoogleUserData if successful
  Future<ApiResult<GoogleUserData>> signInWithGoogle() async {
    try {
      await _ensureInitialized();
      AppLogger.d('Starting Google Sign-In flow', 'GOOGLE-SIGNIN-REPO');

      // Trigger Google Sign-In flow
      final GoogleSignInAccount googleUser = await _googleSignIn.authenticate();

      // Extract user data
      final userData = GoogleUserData(
        email: googleUser.email,
        displayName: googleUser.displayName,
        photoUrl: googleUser.photoUrl,
      );

      AppLogger.s(
        'Google Sign-In successful: ${userData.email}',
        'GOOGLE-SIGNIN-REPO',
      );

      return Success(userData);
    } on GoogleSignInException catch (e) {
      AppLogger.w(
        'Google Sign-In exception: ${e.code}',
        'GOOGLE-SIGNIN-REPO',
      );

      // User cancelled the sign-in
      if (e.code == GoogleSignInExceptionCode.canceled) {
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Google Sign-In dibatalkan',
        );
      }

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal masuk dengan Google: ${e.code}',
        originalError: e,
      );
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Google Sign-In',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-REPO',
      );

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal masuk dengan Google: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Sign out from Google
  Future<void> signOut() async {
    try {
      await _ensureInitialized();
      await _googleSignIn.signOut();
      AppLogger.s('User signed out from Google', 'GOOGLE-SIGNIN-REPO');
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Google Sign-Out',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-REPO',
      );
    }
  }

  /// Disconnect from Google (revoke access)
  Future<void> disconnect() async {
    try {
      await _ensureInitialized();
      await _googleSignIn.disconnect();
      AppLogger.s('User disconnected from Google', 'GOOGLE-SIGNIN-REPO');
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error during Google disconnect',
        e,
        stackTrace,
        'GOOGLE-SIGNIN-REPO',
      );
    }
  }
}
