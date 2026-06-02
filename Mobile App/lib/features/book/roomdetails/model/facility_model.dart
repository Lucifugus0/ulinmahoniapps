/// Facility model with multi-language name support.
/// API returns name (ID fallback) and name_parsed ({id, en, zh}) for locale selection.
class FacilityModel {
  final String name;
  final String icon;
  // <!-- Multi-language: parsed facility name for locale-aware display -->
  final Map<String, String>? nameParsed;

  FacilityModel({
    required this.name,
    required this.icon,
    this.nameParsed,
  });

  factory FacilityModel.fromJson(Map<String, dynamic> json) {
    return FacilityModel(
      name: json['name'] as String? ?? '',
      icon: json['icon'] as String? ?? '',
      // <!-- Parse name_parsed map if available from API -->
      nameParsed: json['name_parsed'] != null
          ? Map<String, String>.from(
              (json['name_parsed'] as Map).map((k, v) => MapEntry(k.toString(), v?.toString() ?? '')))
          : null,
    );
  }

  /// <!-- Get facility name for the given locale with fallback chain -->
  String getLocalizedName(String locale) {
    if (nameParsed == null) return name;
    final localized = nameParsed![locale];
    if (localized != null && localized.isNotEmpty) return localized;
    if (nameParsed!['en'] != null && nameParsed!['en']!.isNotEmpty) return nameParsed!['en']!;
    if (nameParsed!['id'] != null && nameParsed!['id']!.isNotEmpty) return nameParsed!['id']!;
    return name;
  }

  Map<String, dynamic> toJson() {
    return {
      'name': name,
      'icon': icon,
    };
  }
}
