class PromoBannerImageModel {
  final int id;
  final int promoBannerId;
  final String imageUrl;
  /// Mobile-optimized image URL (16:9, 1080x608px). Falls back to imageUrl if null.
  final String? mobileImageUrl;
  final String? thumbnailUrl;
  final int displayOrder;
  final bool isPrimary;

  PromoBannerImageModel({
    required this.id,
    required this.promoBannerId,
    required this.imageUrl,
    this.mobileImageUrl,
    this.thumbnailUrl,
    required this.displayOrder,
    required this.isPrimary,
  });

  factory PromoBannerImageModel.fromJson(Map<String, dynamic> json) {
    return PromoBannerImageModel(
      id: json['id'] as int,
      promoBannerId: json['promo_banner_id'] as int? ?? 0,
      imageUrl: json['image'] as String? ?? json['image_url'] as String? ?? '',
      // Mobile image URL — falls back to main image via API accessor, or use imageUrl
      mobileImageUrl: json['mobile_image_url'] as String? ?? json['mobile_image'] as String?,
      thumbnailUrl: json['thumbnail'] as String? ?? json['thumbnail_url'] as String?,
      displayOrder: json['sort_order'] as int? ?? json['display_order'] as int? ?? 0,
      isPrimary: json['is_primary'] as bool? ?? (json['sort_order'] == 0),
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
