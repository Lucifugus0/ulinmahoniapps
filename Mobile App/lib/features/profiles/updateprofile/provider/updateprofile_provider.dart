import '../../../auth/login/model/auth_model.dart';
import '../data/updateprofile_services.dart';
import '../../../auth/login/provider/auth_provider.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/utils/app_logger.dart';

/// Provider for UpdateProfileService singleton.
final updateProfileServiceProvider = Provider<UpdateProfileService>((ref) {
  return UpdateProfileService();
});

/// Sealed class hierarchy for update profile state.
sealed class UpdateProfileState {}

class UpdateProfileInitial extends UpdateProfileState {}
class UpdateProfileLoading extends UpdateProfileState {}
class UpdateProfileSuccess extends UpdateProfileState {
  final User updatedUser;
  UpdateProfileSuccess(this.updatedUser);
}
class UpdateProfileError extends UpdateProfileState {
  final String message;
  UpdateProfileError(this.message);
}

/// Notifier for update profile actions.
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
/// Uses ref directly instead of constructor-injected dependencies.
class UpdateProfileNotifier extends Notifier<UpdateProfileState> {
  /// build() returns initial state.
  @override
  UpdateProfileState build() {
    return UpdateProfileInitial();
  }

  /// Update user profile with the given fields.
  Future<void> updateProfile({
    required String username,
    required String email,
    required String phoneNumber,
    required String firstName,
    required String lastName,
    String? profilePhotoBase64,
  }) async {
    state = UpdateProfileLoading();

    try {
      final service = ref.read(updateProfileServiceProvider);
      final authNotifier = ref.read(authProvider.notifier);
      final currentUser = authNotifier.state.user.value;

      if (currentUser == null ) {
        throw Exception("Pengguna tidak terautentikasi atau data tidak lengkap.");
      }

      final updatedUser = await service.updateProfile(
        userId: currentUser.id,
        username: username,
        email: email,
        phoneNumber: phoneNumber,
        profilePhotoBase64: profilePhotoBase64,
        firstName: firstName ,
        lastName: lastName ,
      );

      /// Update auth state with new user data
      authNotifier.setUser(updatedUser);

      state = UpdateProfileSuccess(updatedUser);
      AppLogger.s("Profile updated successfully for userId: ${currentUser.id}", "UPDATE-PROFILE");
    } catch (e, st) {
      state = UpdateProfileError(e.toString());
      AppLogger.e("Failed to update profile", e, st, "UPDATE-PROFILE");
    }
  }
}

/// Provider for UpdateProfileNotifier — Riverpod 3.x NotifierProvider.
final updateProfileNotifierProvider = NotifierProvider<UpdateProfileNotifier, UpdateProfileState>(
  UpdateProfileNotifier.new,
);
