import '../../../auth/login/model/auth_model.dart';
import '../data/updateprofile_services.dart';
import '../../../auth/login/provider/auth_provider.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../core/utils/app_logger.dart';

final updateProfileServiceProvider = Provider<UpdateProfileService>((ref) {
  return UpdateProfileService();
});

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

class UpdateProfileNotifier extends StateNotifier<UpdateProfileState> {
  final UpdateProfileService _service;
  final AuthNotifier _authNotifier; 

  UpdateProfileNotifier(this._service, this._authNotifier) : super(UpdateProfileInitial());
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
      
      final currentUser = _authNotifier.state.user.value;

      if (currentUser == null ) {
        throw Exception("Pengguna tidak terautentikasi atau data tidak lengkap.");
      }

      final updatedUser = await _service.updateProfile(
        userId: currentUser.id, 
        username: username,
        email: email,
        phoneNumber: phoneNumber,
        profilePhotoBase64: profilePhotoBase64,
        firstName: firstName ,
        lastName: lastName ,
      );

      _authNotifier.setUser(updatedUser);

      state = UpdateProfileSuccess(updatedUser);
      AppLogger.s("Profile updated successfully for userId: ${currentUser.id}", "UPDATE-PROFILE");
    } catch (e, st) {
      state = UpdateProfileError(e.toString());
      AppLogger.e("Failed to update profile", e, st, "UPDATE-PROFILE");
    }
  }
}

final updateProfileNotifierProvider = StateNotifierProvider<UpdateProfileNotifier, UpdateProfileState>((ref) {
  final service = ref.watch(updateProfileServiceProvider);
  
  final authNotifier = ref.watch(authProvider.notifier);
  return UpdateProfileNotifier(service, authNotifier);
});