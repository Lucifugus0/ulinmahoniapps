/// Apple Sign-In user data model
class AppleUserData {
  final String email;
  final String? givenName;
  final String? familyName;
  final String identityToken;
  final String userIdentifier;

  AppleUserData({
    required this.email,
    this.givenName,
    this.familyName,
    required this.identityToken,
    required this.userIdentifier,
  });

  /// Get display name from given and family name
  String get displayName {
    if (givenName != null && familyName != null) {
      return '$givenName $familyName';
    }
    if (givenName != null) return givenName!;
    if (familyName != null) return familyName!;
    return email.split('@').first;
  }

  /// Convert to JSON for local storage (optional)
  Map<String, dynamic> toJson() {
    return {
      'email': email,
      'givenName': givenName,
      'familyName': familyName,
      'identityToken': identityToken,
      'userIdentifier': userIdentifier,
    };
  }

  /// Create from JSON (optional)
  factory AppleUserData.fromJson(Map<String, dynamic> json) {
    return AppleUserData(
      email: json['email'] as String,
      givenName: json['givenName'] as String?,
      familyName: json['familyName'] as String?,
      identityToken: (json['identityToken'] as String?) ?? '',
      userIdentifier: json['userIdentifier'] as String,
    );
  }

  @override
  String toString() {
    return 'AppleUserData(email: $email, name: $displayName, userIdentifier: $userIdentifier)';
  }
}
