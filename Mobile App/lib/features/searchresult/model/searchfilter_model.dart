class SearchFilter {
  final String? category;
  final String? rentType;
  final String? checkInDate; 
  final int? durationRaw;
  final int? durationInDays;
  final String? checkOutDate; 
  final String? city;
  final String? province;

  SearchFilter({
    required this.category,
    required this.rentType,
    this.checkInDate,
    this.durationRaw,
    this.durationInDays,
    this.checkOutDate,
    this.city,
    this.province
  });

  
  factory SearchFilter.fromMap(Map<String, dynamic> map) {
    return SearchFilter(
      category: map['category'] as String? ?? 'Housing',
      rentType: map['rentType'] as String? ?? 'Monthly',
      checkInDate: map['checkInDate'] as String?,
      durationRaw: map['durationRaw'] as int?,
      durationInDays: map['durationInDays'] as int?,
      checkOutDate: map['checkOutDate'] as String?,
      city: map['city'] as String?,
      province: map['province'] as String?
    );
  }

  
  SearchFilter copyWith({
    String? category,
    String? rentType,
    String? checkInDate,
    int? durationRaw,
    int? durationInDays,
    String? checkOutDate,
    String? city,
    String? province
  }) {
    return SearchFilter(
      category: category ?? this.category,
      rentType: rentType ?? this.rentType,
      checkInDate: checkInDate ?? this.checkInDate,
      durationRaw: durationRaw ?? this.durationRaw,
      durationInDays: durationInDays ?? this.durationInDays,
      checkOutDate: checkOutDate ?? this.checkOutDate,
      city: city ?? this.city,
      province: province ?? this.province
    );
  }
}