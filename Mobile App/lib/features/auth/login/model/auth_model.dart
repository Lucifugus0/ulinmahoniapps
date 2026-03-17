class User {
  final int id;
  final String username;
  final String email;
  final String? name;
  final String profilePhotoUrl;
  final String? profilePhotoPath;
  final String phoneNumber;
  final String firstName;
  final String lastName;
  final String? emailVerifiedAt;
  final String? appleUserId;  // Apple Sign In userIdentifier
  final String? googleUserId; // Google Sign In userIdentifier (future use)

  User({
    required this.id,
    required this.username,
    required this.email,
    this.name,
    this.profilePhotoPath,
    required this.profilePhotoUrl,
    required this.phoneNumber,
    required this.firstName,
    required this.lastName,
    this.emailVerifiedAt,
    this.appleUserId,
    this.googleUserId,
  });

  /// Check if email is verified
  bool get isEmailVerified => emailVerifiedAt != null && emailVerifiedAt!.isNotEmpty;

  factory User.fromJson(Map<String, dynamic> json) {
    return User(
      id: json['id'],
      username: json['username'],
      email: json['email'],
      name: json['name'],
      profilePhotoPath: json['profile_photo_path'] ?? '',
      profilePhotoUrl: json['profile_photo_url'] ?? '',
      phoneNumber: json['phone_number'] ?? '',
      firstName: json['first_name'] ?? '',
      lastName: json['last_name'] ?? '',
      emailVerifiedAt: json['email_verified_at'],
      appleUserId: json['apple_user_id'],
      googleUserId: json['google_user_id'],
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'username': username,
      'email': email,
      'name': name,
      'profile_photo_url': profilePhotoUrl,
      'profile_photo_path': profilePhotoPath,
      'phone_number': phoneNumber,
      'first_name': firstName,
      'last_name': lastName,
      'email_verified_at': emailVerifiedAt,
      'apple_user_id': appleUserId,
      'google_user_id': googleUserId,
    };
  }
}
