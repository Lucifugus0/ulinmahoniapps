import '../../../../../../core/network/api_result.dart';
import '../../../../../../core/network/dio_client.dart';
import '../../../../../../core/utils/app_logger.dart';
import '../../../model/google/user_filter_model.dart';

/// Repository for filtering users by email or name
/// Handles GET /users?email={email} or GET /users?name={name}
class UserFilterRepository {
  final DioClient _dioClient;

  UserFilterRepository({required DioClient dioClient})
      : _dioClient = dioClient;

  /// Filter users by email
  /// Returns user data if found, null if not found
  Future<ApiResult<UserFilterModel?>> getUserByEmail(String email) async {
    try {
      AppLogger.d('Fetching user by email: $email', 'USER-FILTER-REPO');

      final response = await _dioClient.get(
        '/users',
        queryParameters: {'email': email},
      );

      AppLogger.d('User filter response: ${response.data}', 'USER-FILTER-REPO');

      final filterResponse = UserFilterResponse.fromJson(response.data);

      if (filterResponse.hasUser) {
        AppLogger.s(
          'User found: ${filterResponse.firstUser!.email}',
          'USER-FILTER-REPO',
        );
        return Success(filterResponse.firstUser);
      } else {
        AppLogger.d('User not found with email: $email', 'USER-FILTER-REPO');
        return Success(null);
      }
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error fetching user by email',
        e,
        stackTrace,
        'USER-FILTER-REPO',
      );

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to check user: ${e.toString()}',
        originalError: e,
      );
    }
  }

  /// Filter users by name
  /// Returns list of users matching the name
  Future<ApiResult<List<UserFilterModel>>> getUsersByName(String name) async {
    try {
      AppLogger.d('Fetching users by name: $name', 'USER-FILTER-REPO');

      final response = await _dioClient.get(
        '/users',
        queryParameters: {'name': name},
      );

      final filterResponse = UserFilterResponse.fromJson(response.data);

      AppLogger.s(
        'Found ${filterResponse.data.length} users',
        'USER-FILTER-REPO',
      );

      return Success(filterResponse.data);
    } catch (e, stackTrace) {
      AppLogger.e(
        'Error fetching users by name',
        e,
        stackTrace,
        'USER-FILTER-REPO',
      );

      return Failure(
        errorType: ApiErrorType.unknown,
        message: 'Failed to fetch users: ${e.toString()}',
        originalError: e,
      );
    }
  }
}
