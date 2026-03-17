/// Google Sign-In user data model
class GoogleUserData {
  final String email;
  final String? displayName;
  final String? photoUrl;

  GoogleUserData({
    required this.email,
    this.displayName,
    this.photoUrl,
  });

  /// Convert to JSON for local storage (optional)
  Map<String, dynamic> toJson() {
    return {
      'email': email,
      'name': displayName,
      'photoUrl': photoUrl,
    };
  }

  /// Create from JSON (optional)
  factory GoogleUserData.fromJson(Map<String, dynamic> json) {
    return GoogleUserData(
      email: json['email'] as String,
      displayName: json['name'] as String?,
      photoUrl: json['photoUrl'] as String?,
    );
  }

  @override
  String toString() {
    return 'GoogleUserData(email: $email, name: $displayName, photoUrl: $photoUrl)';
  }
}
