class FacilityModel {
  final String name;
  final String icon;

  FacilityModel({
    required this.name,
    required this.icon,
  });

  factory FacilityModel.fromJson(Map<String, dynamic> json) {
    return FacilityModel(
      name: json['name'] as String? ?? '',
      icon: json['icon'] as String? ?? '',
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'name': name,
      'icon': icon,
    };
  }
}
