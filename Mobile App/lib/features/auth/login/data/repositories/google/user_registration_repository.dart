import '../../../../../../core/network/api_result.dart';
import '../../../../../../core/network/dio_client.dart';
import '../../../../../../core/utils/app_logger.dart';
import '../../../../../../core/constants/api_constants.dart';
import '../../../model/google/user_registration_model.dart';

/// Repository for user registration
/// Handles POST /auth/register (auto-register from Google Sign-In)
class UserRegistrationRepository {
  final DioClient _dioClient;

  UserRegistrationRepository({required DioClient dioClient})
      : _dioClient = dioClient;

  /// Register new user
  /// Used for auto-registration after Google Sign-In
  Future<ApiResult<UserRegistrationData>> registerUser(
    UserRegistrationRequest request,
  ) async {
    try {
      final requestData = request.toJson();

      AppLogger.d(
        '🔍 DEBUG: Registration Request Data:',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - email: ${requestData['email']}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - first_name: ${requestData['first_name']}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - last_name: ${requestData['last_name']}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - username: ${requestData['username']}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - phone_number: ${requestData['phone_number']}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '  - password: ${requestData['password']?.toString().substring(0, 10)}...',
        'USER-REGISTRATION-REPO',
      );

      final response = await _dioClient.post(
        ApiConfig.registerUrl.replaceFirst(ApiConfig.baseUrl, ''),
        data: requestData,
      );

      AppLogger.d(
        '🔍 DEBUG: Registration Response Status: ${response.statusCode}',
        'USER-REGISTRATION-REPO',
      );
      AppLogger.d(
        '🔍 DEBUG: Registration Response Data: ${response.data}',
        'USER-REGISTRATION-REPO',
      );

      final registrationResponse =
          UserRegistrationResponse.fromJson(response.data);

      if (registrationResponse.data != null) {
        AppLogger.s(
          '✅ ${registrationResponse.message}',
          'USER-REGISTRATION-REPO',
        );
        AppLogger.i(
          'User: ${registrationResponse.data!.email} | Token: ${registrationResponse.token?.substring(0, 20)}...',
          'USER-REGISTRATION-REPO',
        );

        if (registrationResponse.requiresEmailVerification == true) {
          AppLogger.w(
            '📧 Email verification required',
            'USER-REGISTRATION-REPO',
          );
        }

        return Success(registrationResponse.data!);
      } else {
        AppLogger.w(
          '⚠️ Registration failed: ${registrationResponse.message}',
          'USER-REGISTRATION-REPO',
        );
        AppLogger.d(
          '🔍 DEBUG: Full response data when failed: ${response.data}',
          'USER-REGISTRATION-REPO',
        );

        return Failure(
          errorType: ApiErrorType.unknown,
          message: registrationResponse.message,
        );
      }
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error registering user',
        e,
        stackTrace,
        'USER-REGISTRATION-REPO',
      );

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to register user: ${e.toString()}',
        originalError: e,
      );
    }
  }
}
