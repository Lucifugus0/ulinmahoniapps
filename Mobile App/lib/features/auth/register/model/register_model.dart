
class Register {
  final int id;
  final String username;
  final String email;
  final String profilePhotoUrl;
  final String firstName;
  final String lastName;
  final String? token;

  Register({
    required this.id,
    required this.username,
    required this.email,
    required this.profilePhotoUrl,
    required this.token,
    required this.firstName,
    required this.lastName,
  });

  factory Register.fromJson(Map<String, dynamic> json) {
    final user = json['user'];
    return Register(
      id: user['id'],
      username: user['username'],
      email: user['email'],
      profilePhotoUrl: user['profile_photo_url'] ?? '',
      token: json['token'],
      firstName: user['first_name'],
      lastName: user['last_name'],
    );
  }
}
