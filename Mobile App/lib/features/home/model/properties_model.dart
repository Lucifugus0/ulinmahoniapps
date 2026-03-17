class PropertyModel {
  final int idrec;
  final String? slug;
  final String tags;
  final String name;
  final String? description;
  final String province;
  final String city;
  final String? subdistrict;
  final String? village;
  final String? postalCode;
  final String? address;
  final String location;
  final String distance;
  final String priceDiscountedDaily;
  final String priceDiscountedMonthly;
  final String priceOriginalDaily;
  final String priceOriginalMonthly;
  // final Features features;
  // final Attributes attributes;
  final String image;
  final String? thumbnail;
  final int status;
  final String? createdAt;
  final String? updatedAt;
  final String? createdBy;
  final String? updatedBy;
  final int? totalRooms;
  final int? availableRooms;
  final String? gender;

  PropertyModel({
    required this.idrec,
    required this.slug,
    required this.tags,
    required this.name,
    required this.description,
    required this.province,
    required this.city,
    required this.subdistrict,
    required this.village,
    required this.postalCode,
    required this.address,
    required this.location,
    required this.distance,
    required this.priceDiscountedMonthly,
    required this.priceDiscountedDaily,
    required this.priceOriginalMonthly,
    required this.priceOriginalDaily,
    // required this.features,
    // required this.attributes,
    required this.image,
    required this.thumbnail,
    required this.status,
    required this.createdAt,
    required this.updatedAt,
    this.createdBy,
    this.updatedBy,
    this.totalRooms,
    this.availableRooms,
    this.gender,
  });

  factory PropertyModel.fromJson(Map<String, dynamic> json) {
    final List<dynamic>? imagesList = json['images'];
    String validImageUrl = ''; // Inisialisasi dengan string kosong
    String validThumbnailUrl = ''; // Untuk thumbnail field

    // Loop melalui daftar gambar untuk menemukan thumbnail yang valid pertama
    if (imagesList != null && imagesList.isNotEmpty) {
      for (var imageEntry in imagesList) {
        // Pastikan imageEntry adalah Map dan memiliki 'thumbnail'
        if (imageEntry is Map<String, dynamic>) {
          // Prioritaskan thumbnail jika ada dan tidak null
          String? thumbnailUrl = imageEntry['thumbnail']?.toString();
          if (thumbnailUrl != null && thumbnailUrl.isNotEmpty) {
            validImageUrl = thumbnailUrl;
            break; // Berhenti looping karena sudah menemukan thumbnail yang valid
          }
          // Fallback ke image_data jika thumbnail tidak ada
          String? imageDataUrl = imageEntry['image_data']?.toString();
          if (imageDataUrl != null && imageDataUrl.isNotEmpty) {
            validImageUrl = imageDataUrl;
            break;
          }
        }
      }
    }

    // Untuk thumbnail field: cek json['thumbnail'] dulu, jika null ambil dari images[] array
    if (json['thumbnail'] != null && json['thumbnail'].toString().isNotEmpty) {
      validThumbnailUrl = json['thumbnail'].toString();
    } else {
      // Jika thumbnail null, ambil gambar pertama dari images[] array
      validThumbnailUrl = validImageUrl;
    }

    // final features = json['features'] ?? {};
    // final attributes = json['attributes'] ?? {};

    return PropertyModel(
      idrec: json['idrec'],
      slug: json['slug'],
      tags: json['tags'] ?? '',
      name: json['name'] ?? '',
      description: json['description'] ?? '',
      province: json['province'] ?? '',
      city: json['city'] ?? '',
      subdistrict: json['subdistrict'] ?? '',
      village: json['village'] ?? '',
      postalCode: json['postal_code'] ?? '',
      address: json['address'] ?? '',
      location: json['location'] ?? '',
      distance: json['distance']?.toString() ?? '',
      priceDiscountedDaily: json['price_discounted_daily']?.toString() ?? '0',
      priceDiscountedMonthly:
          json['price_discounted_monthly']?.toString() ?? '0',
      priceOriginalDaily: json['price_original_daily']?.toString() ?? '0',
      priceOriginalMonthly: json['price_original_monthly']?.toString() ?? '0',
      // features: Features.fromJson(features),
      // attributes: Attributes.fromJson(attributes),
      image: validImageUrl, // Gunakan gambar valid yang ditemukan
      thumbnail: validThumbnailUrl, // Gunakan thumbnail dari json atau fallback ke images[]
      status: json['status'] ?? 0,
      createdAt: json['created_at'] ?? '',
      updatedAt: json['updated_at'] ?? '',
      createdBy: json['created_by'],
      updatedBy: json['updated_by'],
      totalRooms: json['total_rooms'],
      availableRooms: json['available_rooms'],
      gender: json['gender'] as String?,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'idrec': idrec,
      'slug': slug,
      'tags': tags,
      'name': name,
      'description': description,
      'province': province,
      'city': city,
      'subdistrict': subdistrict,
      'village': village,
      'postal_code': postalCode,
      'address': address,
      'location': location,
      'distance': distance,
      'price_discounted_daily': priceDiscountedDaily,
      'price_discounted_monthly': priceDiscountedMonthly,
      'price_original_daily': priceOriginalDaily,
      'price_original_monthly': priceOriginalMonthly,
      // 'features': features,
      // 'attributes': attributes,
      'image': image,
      'thumbnail': thumbnail,
      'status': status,
      'created_at': createdAt,
      'updated_at': updatedAt,
      'created_by': createdBy,
      'updated_by': updatedBy,
      'total_rooms': totalRooms,
      'available_rooms': availableRooms,
      'gender': gender,
    };
  }
}

// Class Attributes dan Features (sesuai yang Anda berikan, dikomentari)
// class Attributes {
//   final List<String> amenities;
//   final List<String> roomFacilities;
//   final List<String> rules;

//   Attributes({
//     required this.amenities,
//     required this.roomFacilities,
//     required this.rules,
//   });

//   factory Attributes.fromJson(Map<String, dynamic> json) {
//     return Attributes(
//       amenities: List<String>.from(json['amenities'] ?? []),
//       roomFacilities: List<String>.from(json['room_facilities'] ?? []),
//       rules: List<String>.from(json['rules'] ?? []),
//     );
//   }

//   Map<String, dynamic> toJson() {
//     return {
//       'amenities': amenities,
//       'room_facilities': roomFacilities,
//       'rules': rules,
//     };
//   }
// }

// class Features {
//   final Map<String, String> featuresMap;

//   Features({
//     required this.featuresMap,
//   });

//   factory Features.fromJson(Map<String, dynamic> json) {
//     return Features(
//       featuresMap: Map<String, String>.from(json),
//     );
//   }

//   Map<String, dynamic> toJson() {
//     return featuresMap;
//   }

//   List<String> get featuresList => featuresMap.values.toList();
// }
