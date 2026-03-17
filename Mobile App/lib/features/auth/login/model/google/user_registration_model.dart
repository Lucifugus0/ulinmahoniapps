/// User Registration Request Model
/// For POST /users (auto-register from Google Sign-In)
class UserRegistrationRequest {
  final String firstName;
  final String lastName;
  final String username;
  final String email;
  final String? phoneNumber;
  final String password;

  UserRegistrationRequest({
    required this.firstName,
    required this.lastName,
    required this.username,
    required this.email,
    this.phoneNumber,
    required this.password,
  });

  Map<String, dynamic> toJson() {
    return {
      'first_name': firstName,
      'last_name': lastName,
      'username': username,
      'email': email,
      'phone_number': phoneNumber,
      'password': password,
    };
  }

  /// Factory to create from Google Sign-In data
  /// Uses email as password for Google Sign-In users
  factory UserRegistrationRequest.fromGoogleSignIn({
    required String email,
    required String displayName,
  }) {
    // Split display name into first and last name
    final nameParts = displayName.trim().split(' ');
    final firstName = nameParts.isNotEmpty ? nameParts.first : displayName;
    final lastName = nameParts.length > 1 ? nameParts.sublist(1).join(' ') : firstName;

    // Generate username from email (part before @) + random numbers to avoid duplicates
    final emailPrefix = email.split('@').first;

    // Sanitize username: only allow letters, numbers, dashes, and underscores
    // Replace dots, spaces, and other invalid characters with underscores
    final sanitizedPrefix = emailPrefix
        .replaceAll(RegExp(r'[^a-zA-Z0-9_-]'), '_')
        .toLowerCase();

    // Add timestamp to avoid duplicates
    final timestamp = DateTime.now().millisecondsSinceEpoch.toString().substring(8);
    final username = '$sanitizedPrefix$timestamp';

    return UserRegistrationRequest(
      firstName: firstName,
      lastName: lastName,
      username: username,
      email: email,
      phoneNumber: null,
      password: email, // Use email as password for Google Sign-In users
    );
  }
}

/// User Registration Response Model
class UserRegistrationResponse {
  final String status;
  final String message;
  final UserRegistrationData? data;
  final String? token;
  final bool? requiresEmailVerification;

  UserRegistrationResponse({
    required this.status,
    required this.message,
    this.data,
    this.token,
    this.requiresEmailVerification,
  });

  factory UserRegistrationResponse.fromJson(Map<String, dynamic> json) {
    final dataMap = json['data'] as Map<String, dynamic>?;

    return UserRegistrationResponse(
      status: json['status'] as String,
      message: json['message'] as String,
      token: dataMap?['token'] as String?,
      requiresEmailVerification: dataMap?['requires_email_verification'] as bool?,
      data: dataMap?['user'] != null
          ? UserRegistrationData.fromJson(dataMap!['user'] as Map<String, dynamic>)
          : null,
    );
  }
}

/// User Registration Data
class UserRegistrationData {
  final int id;
  final String firstName;
  final String lastName;
  final String username;
  final String email;
  final int status;
  final String? profilePhotoUrl;

  UserRegistrationData({
    required this.id,
    required this.firstName,
    required this.lastName,
    required this.username,
    required this.email,
    required this.status,
    this.profilePhotoUrl,
  });

  // Helper to get full name
  String get name => '$firstName $lastName';

  factory UserRegistrationData.fromJson(Map<String, dynamic> json) {
    return UserRegistrationData(
      id: json['id'] as int,
      firstName: json['first_name'] as String,
      lastName: json['last_name'] as String,
      username: json['username'] as String,
      email: json['email'] as String,
      status: json['status'] as int,
      profilePhotoUrl: json['profile_photo_url'] as String?,
    );
  }
}
