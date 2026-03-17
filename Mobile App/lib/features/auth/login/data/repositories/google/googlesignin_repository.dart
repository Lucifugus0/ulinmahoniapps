import 'dart:convert';
import 'package:google_sign_in/google_sign_in.dart';
import '../../../../../../core/network/api_result.dart';
import '../../../../../../core/utils/app_logger.dart';
import '../../../model/google/googlesignin_model.dart';

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

      // Trigger Google Sign-In flow and authenticate
      final GoogleSignInAccount googleUser = await _googleSignIn.authenticate();

      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAccount object: ${googleUser.toString()}',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAccount.email: ${googleUser.email}',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAccount.displayName: ${googleUser.displayName}',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAccount.photoUrl: ${googleUser.photoUrl}',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAccount.id: ${googleUser.id}',
        'GOOGLE-SIGNIN-REPO',
      );

      // Get authentication credentials which include ID token
      final GoogleSignInAuthentication googleAuth = googleUser.authentication;
      final String? idToken = googleAuth.idToken;

      AppLogger.d(
        '🔍 DEBUG: GoogleSignInAuthentication object: ${googleAuth.toString()}',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: ID Token length: ${idToken?.length ?? 0}',
        'GOOGLE-SIGNIN-REPO',
      );
      if (idToken != null && idToken.length > 50) {
        AppLogger.d(
          '🔍 DEBUG: ID Token preview: ${idToken.substring(0, 50)}...',
          'GOOGLE-SIGNIN-REPO',
        );
      }

      if (idToken == null) {
        AppLogger.e(
          'ID Token is null',
          null,
          null,
          'GOOGLE-SIGNIN-REPO',
        );
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Gagal mendapatkan ID Token',
        );
      }

      // Decode ID token to get user info (email, name, etc.)
      final parts = idToken.split('.');
      if (parts.length != 3) {
        AppLogger.e(
          'Invalid ID Token format',
          null,
          null,
          'GOOGLE-SIGNIN-REPO',
        );
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Format ID Token tidak valid',
        );
      }

      // Decode the payload (second part of JWT)
      final payload = parts[1];
      final normalized = base64Url.normalize(payload);
      final decoded = utf8.decode(base64Url.decode(normalized));
      final payloadMap = json.decode(decoded) as Map<String, dynamic>;

      AppLogger.d(
        '🔍 DEBUG: Decoded ID Token payload: $payloadMap',
        'GOOGLE-SIGNIN-REPO',
      );

      final email = payloadMap['email'] as String?;
      final name = payloadMap['name'] as String?;
      final picture = payloadMap['picture'] as String?;

      AppLogger.d(
        '🔍 DEBUG: Extracted email: $email',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: Extracted name: $name',
        'GOOGLE-SIGNIN-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: Extracted picture: $picture',
        'GOOGLE-SIGNIN-REPO',
      );

      if (email == null) {
        AppLogger.e(
          'Email not found in ID Token',
          null,
          null,
          'GOOGLE-SIGNIN-REPO',
        );
        return Failure(
          errorType: ApiErrorType.unknown,
          message: 'Email tidak ditemukan',
        );
      }

      AppLogger.d(
        'Decoded email from ID token: $email',
        'GOOGLE-SIGNIN-REPO',
      );

      // Extract user data from the ID token
      final userData = GoogleUserData(
        email: email,
        displayName: name,
        photoUrl: picture,
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

      // User cancelled the sign-in - return silently without showing popup
      if (e.code == GoogleSignInExceptionCode.canceled) {
        AppLogger.d('User cancelled Google Sign-In', 'GOOGLE-SIGNIN-REPO');
        return Failure(
          errorType: ApiErrorType.unknown,
          message: '', // Empty message = no popup shown
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
