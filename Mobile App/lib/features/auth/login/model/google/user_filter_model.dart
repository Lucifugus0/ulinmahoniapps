/// User Filter Model
/// Response from GET /users?email={email} or GET /users?name={name}
class UserFilterModel {
  final int id;
  final String name;
  final String firstName;
  final String lastName;
  final String username;
  final String email;
  final String? phoneNumber;
  final String? profilePhotoPath;
  final int status;
  final String? profilePhotoUrl;
  final String? emailVerifiedAt;

  UserFilterModel({
    required this.id,
    required this.name,
    required this.firstName,
    required this.lastName,
    required this.username,
    required this.email,
    this.phoneNumber,
    this.profilePhotoPath,
    required this.status,
    this.profilePhotoUrl,
    this.emailVerifiedAt,
  });

  /// Check if email is verified
  bool get isEmailVerified => emailVerifiedAt != null && emailVerifiedAt!.isNotEmpty;

  factory UserFilterModel.fromJson(Map<String, dynamic> json) {
    return UserFilterModel(
      id: json['id'] as int,
      name: (json['name'] as String?) ?? '',
      firstName: (json['first_name'] as String?) ?? '',
      lastName: (json['last_name'] as String?) ?? '',
      username: (json['username'] as String?) ?? '',
      email: (json['email'] as String?) ?? '',
      phoneNumber: json['phone_number'] as String?,
      profilePhotoPath: json['profile_photo_path'] as String?,
      status: json['status'] as int? ?? 0,
      profilePhotoUrl: json['profile_photo_url'] as String?,
      emailVerifiedAt: json['email_verified_at'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'name': name,
      'first_name': firstName,
      'last_name': lastName,
      'username': username,
      'email': email,
      'phone_number': phoneNumber,
      'profile_photo_path': profilePhotoPath,
      'status': status,
      'profile_photo_url': profilePhotoUrl,
      'email_verified_at': emailVerifiedAt,
    };
  }
}

/// Response wrapper for user filter
class UserFilterResponse {
  final String status;
  final String message;
  final List<UserFilterModel> data;

  UserFilterResponse({
    required this.status,
    required this.message,
    required this.data,
  });

  factory UserFilterResponse.fromJson(Map<String, dynamic> json) {
    return UserFilterResponse(
      status: json['status'] as String,
      message: json['message'] as String,
      data: (json['data'] as List<dynamic>)
          .map((e) => UserFilterModel.fromJson(e as Map<String, dynamic>))
          .toList(),
    );
  }

  bool get hasUser => data.isNotEmpty;
  UserFilterModel? get firstUser => data.isNotEmpty ? data.first : null;
}
