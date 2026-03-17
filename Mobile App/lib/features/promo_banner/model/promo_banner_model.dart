import 'promo_banner_image_model.dart';

class PromoBannerModel {
  final int id;
  final String title;
  final String? description;
  final String? linkUrl;
  final String? linkText;
  final String? startDate;
  final String? endDate;
  final int? priority;
  final int status;  // Changed to int (1=active, 0=inactive)
  final int? createdBy;
  final int? updatedBy;
  final String? thumbnail;
  final List<PromoBannerImageModel> images;
  final List<String>? howToClaim;

  PromoBannerModel({
    required this.id,
    required this.title,
    this.description,
    this.linkUrl,
    this.linkText,
    this.startDate,
    this.endDate,
    this.priority,
    required this.status,
    this.createdBy,
    this.updatedBy,
    this.thumbnail,
    required this.images,
    this.howToClaim,
  });

  factory PromoBannerModel.fromJson(Map<String, dynamic> json) {
    return PromoBannerModel(
      id: json['idrec'] as int,  // Backend uses 'idrec' not 'id'
      title: json['title'] as String,
      description: json['descriptions'] as String?,  // Backend uses 'descriptions' not 'description'
      linkUrl: json['link_url'] as String?,
      linkText: json['link_text'] as String?,
      startDate: json['start_date'] as String?,
      endDate: json['end_date'] as String?,
      priority: json['priority'] as int?,
      status: json['status'] as int,  // Backend uses int (1=active, 0=inactive)
      createdBy: json['created_by'] as int?,
      updatedBy: json['updated_by'] as int?,
      thumbnail: json['thumbnail'] as String?,
      images: (json['images'] as List?)
              ?.map((e) => PromoBannerImageModel.fromJson(e as Map<String, dynamic>))
              .toList() ??
          [],
      howToClaim: (json['how_to_claim'] as List?)
              ?.map((e) => e as String)
              .toList(),
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'idrec': id,
      'title': title,
      'descriptions': description,
      'link_url': linkUrl,
      'link_text': linkText,
      'start_date': startDate,
      'end_date': endDate,
      'priority': priority,
      'status': status,
      'created_by': createdBy,
      'updated_by': updatedBy,
      'thumbnail': thumbnail,
      'images': images.map((e) => e.toJson()).toList(),
      'how_to_claim': howToClaim,
    };
  }

  /// Helper: Get primary image URL
  /// Priority: 1. Root thumbnail, 2. Primary image thumbnail, 3. First image thumbnail, 4. First image URL
  String? get primaryImageUrl {
    // First, try root-level thumbnail
    if (thumbnail != null && thumbnail!.isNotEmpty) {
      return thumbnail;
    }

    // Fallback to images array
    if (images.isEmpty) return null;

    try {
      final primary = images.firstWhere(
        (img) => img.isPrimary,
        orElse: () => images.first,
      );
      return primary.thumbnailUrl ?? primary.imageUrl;
    } catch (e) {
      return null;
    }
  }
}
