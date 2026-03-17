import '../../../../../../core/network/api_result.dart';
import '../../../../../../core/network/dio_client.dart';
import '../../../../../../core/constants/api_constants.dart';
import '../../../../../../core/utils/app_logger.dart';

/// Service for validating user email verification status via API
/// Used by Apple Sign-In to check if user has verified their email
class EmailVerificationValidator {
  final DioClient _dioClient = DioClient();

  /// Check if user's email is verified via GET /users/:id
  /// Returns true if verified, false if not verified
  /// Throws error if API call fails
  Future<ApiResult<bool>> isEmailVerified(int userId) async {
    try {
      AppLogger.d('Checking email verification for userId: $userId', 'EMAIL-VERIFICATION');

      final response = await _dioClient.get(
        '${ApiConfig.allUsers}$userId',
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success') {
          final userData = body['data'];

          // Check both possible fields for email verification
          final emailVerifiedAt = userData['email_verified_at'] as String?;
          final isEmailVerified = userData['is_email_verified'] as bool?;

          // User is verified if either:
          // 1. email_verified_at is not null (has timestamp)
          // 2. is_email_verified is true
          final isVerified = emailVerifiedAt != null || (isEmailVerified ?? false);

          AppLogger.d(
            'Email verification status for user $userId: $isVerified '
            '(email_verified_at: $emailVerifiedAt, is_email_verified: $isEmailVerified)',
            'EMAIL-VERIFICATION',
          );

          return Success(isVerified);
        } else {
          final message = body['message'] ?? 'Invalid API response format';
          AppLogger.w('Invalid API response: $message', 'EMAIL-VERIFICATION');
          return Failure(
            errorType: ApiErrorType.parsing,
            message: message,
          );
        }
      } else {
        final message = 'API returned status code ${response.statusCode}';
        AppLogger.w(message, 'EMAIL-VERIFICATION');
        return Failure(
          errorType: ApiErrorType.server,
          message: message,
          statusCode: response.statusCode,
        );
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to check email verification', e, stackTrace, 'EMAIL-VERIFICATION');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal memeriksa status verifikasi email: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Get user verification details including email, name, and verification status
  /// Used to display in account picker dialog
  Future<ApiResult<UserVerificationDetails>> getUserVerificationDetails(int userId) async {
    try {
      AppLogger.d('Fetching user verification details for userId: $userId', 'EMAIL-VERIFICATION');

      final response = await _dioClient.get(
        '${ApiConfig.allUsers}$userId',
      );

      if (response.statusCode == 200) {
        final body = response.data;

        if (body is Map && body['status'] == 'success') {
          final userData = body['data'];

          final details = UserVerificationDetails(
            userId: userData['id'] as int,
            email: userData['email'] as String? ?? '',
            name: userData['name'] as String? ?? '',
            firstName: userData['first_name'] as String? ?? '',
            lastName: userData['last_name'] as String? ?? '',
            emailVerifiedAt: userData['email_verified_at'] as String?,
            isEmailVerified: userData['is_email_verified'] as bool? ?? false,
          );

          AppLogger.d(
            'User verification details: ${details.email} (verified: ${details.isVerified})',
            'EMAIL-VERIFICATION',
          );

          return Success(details);
        } else {
          final message = body['message'] ?? 'Invalid API response format';
          AppLogger.w('Invalid API response: $message', 'EMAIL-VERIFICATION');
          return Failure(
            errorType: ApiErrorType.parsing,
            message: message,
          );
        }
      } else {
        final message = 'API returned status code ${response.statusCode}';
        AppLogger.w(message, 'EMAIL-VERIFICATION');
        return Failure(
          errorType: ApiErrorType.server,
          message: message,
          statusCode: response.statusCode,
        );
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to fetch user verification details', e, stackTrace, 'EMAIL-VERIFICATION');
      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Gagal mengambil data user: ${e.toString()}',
        originalError: e,
      );
    }
  }
}

/// Model for user verification details
class UserVerificationDetails {
  final int userId;
  final String email;
  final String name;
  final String firstName;
  final String lastName;
  final String? emailVerifiedAt;
  final bool isEmailVerified;

  UserVerificationDetails({
    required this.userId,
    required this.email,
    required this.name,
    required this.firstName,
    required this.lastName,
    this.emailVerifiedAt,
    required this.isEmailVerified,
  });

  /// Get full name from first and last name
  String get fullName {
    final first = firstName;
    final last = lastName;
    final combinedName = '$first $last'.trim();
    return combinedName.isEmpty ? name : combinedName;
  }

  /// Check if email is verified (either field is set)
  bool get isVerified => emailVerifiedAt != null || isEmailVerified;

  @override
  String toString() {
    return 'UserVerificationDetails(userId: $userId, email: $email, verified: $isVerified)';
  }
}
