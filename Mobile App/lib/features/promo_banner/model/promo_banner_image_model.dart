class PromoBannerImageModel {
  final int id;
  final int promoBannerId;
  final String imageUrl;
  final String? thumbnailUrl;
  final int displayOrder;
  final bool isPrimary;

  PromoBannerImageModel({
    required this.id,
    required this.promoBannerId,
    required this.imageUrl,
    this.thumbnailUrl,
    required this.displayOrder,
    required this.isPrimary,
  });

  factory PromoBannerImageModel.fromJson(Map<String, dynamic> json) {
    return PromoBannerImageModel(
      id: json['id'] as int,
      promoBannerId: json['promo_banner_id'] as int? ?? 0,  // May not exist in API response
      imageUrl: json['image'] as String? ?? json['image_url'] as String? ?? '',  // Backend uses 'image'
      thumbnailUrl: json['thumbnail'] as String? ?? json['thumbnail_url'] as String?,  // Backend uses 'thumbnail'
      displayOrder: json['sort_order'] as int? ?? json['display_order'] as int? ?? 0,  // Backend uses 'sort_order'
      isPrimary: json['is_primary'] as bool? ?? (json['sort_order'] == 0),  // Fallback: first image (sort_order=0) is primary
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'promo_banner_id': promoBannerId,
      'image': imageUrl,
      'thumbnail': thumbnailUrl,
      'sort_order': displayOrder,
      'is_primary': isPrimary,
    };
  }
}
