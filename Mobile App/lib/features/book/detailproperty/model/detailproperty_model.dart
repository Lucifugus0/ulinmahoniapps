import '../../roomdetails/model/facility_model.dart';

class DetailPropertyModel {
  final int? id;
  final String? tags;
  final String? name;
  final String? description;
  final String? location;
  final String? distance;
  final String? address;
  final String? city;
  final String? village;
  final String? postalCode;
  final int? level_count;
  final List<ImageModel>? images;
  final List<String>? general; // Deprecated: for backward compatibility
  final List<String>? security; // Deprecated: for backward compatibility
  final List<String>? amenities; // Deprecated: for backward compatibility
  final List<FacilityModel>? generalFacilities; // New: with icons
  final List<FacilityModel>? securityFacilities; // New: with icons
  final List<FacilityModel>? amenitiesFacilities; // New: with icons
  final String? thumbnail;
  final double? depositFeeAmount;
  final List<ParkingFeeModel> parkingFees;
  final List<NearbyLocationModel>? nearbyLocations;
  final String? gender;

  DetailPropertyModel({
    this.id,
    this.tags,
    this.name,
    this.description,
    this.location,
    this.distance,
    this.address,
    this.city,
    this.village,
    this.postalCode,
    this.level_count,
    this.images,
    this.general,
    this.security,
    this.amenities,
    this.generalFacilities,
    this.securityFacilities,
    this.amenitiesFacilities,
    this.thumbnail,
    this.depositFeeAmount,
    this.parkingFees = const [],
    this.nearbyLocations,
    this.gender,
  });

  factory DetailPropertyModel.fromJson(Map<String, dynamic> json) {

    List<ImageModel>? parsedImages;
    if (json['images'] is List) {
      parsedImages = (json['images'] as List)
          .map((item) => ImageModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    // Helper to parse list that can be either string or object format
    Map<String, dynamic> parseFacilityList(dynamic list) {
      if (list == null || list is! List || list.isEmpty) {
        return {'strings': null, 'facilities': null};
      }

      // Check if first item is a Map (new format) or String (old format)
      if (list.first is Map) {
        // New format with icons
        return {
          'strings': null,
          'facilities': list
              .map((item) => FacilityModel.fromJson(item as Map<String, dynamic>))
              .toList()
        };
      } else {
        // Old format: List<String>
        return {
          'strings': list.map((item) => item.toString()).toList(),
          'facilities': null
        };
      }
    }

    List<ParkingFeeModel> parsedParkingFees = [];
    if (json['parking_fees'] is List) {
      parsedParkingFees = (json['parking_fees'] as List)
          .map((item) => ParkingFeeModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    List<NearbyLocationModel>? parsedNearbyLocations;
    if (json['nearby_locations'] is List) {
      parsedNearbyLocations = (json['nearby_locations'] as List)
          .map((item) => NearbyLocationModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    // Parse general facilities
    final generalData = parseFacilityList(json['general']);
    final securityData = parseFacilityList(json['security']);
    final amenitiesData = parseFacilityList(json['amenities']);

    return DetailPropertyModel(
      id: json['idrec'] as int?,
      tags: json['tags'] as String?,
      name: json['name'] as String?,
      description: json['description'] as String?,
      location: json['location'] as String?,
      distance: json['distance'] as String?,
      address: json['address'] as String?,
      city: json['city'] as String?,
      village: json['village'] as String?,
      postalCode: json['postal_code'] as String?,
      level_count: json['level_count'] as int?,
      images: parsedImages,
      general: generalData['strings'] as List<String>?,
      security: securityData['strings'] as List<String>?,
      amenities: amenitiesData['strings'] as List<String>?,
      generalFacilities: generalData['facilities'] as List<FacilityModel>?,
      securityFacilities: securityData['facilities'] as List<FacilityModel>?,
      amenitiesFacilities: amenitiesData['facilities'] as List<FacilityModel>?,
      thumbnail: json['thumbnail'] as String?,
      depositFeeAmount: json['deposit_fee_amount'] != null ? double.tryParse(json['deposit_fee_amount'].toString()) : null,
      parkingFees: parsedParkingFees,
      nearbyLocations: parsedNearbyLocations,
      gender: json['gender'] as String?,
    );
  }
}

class ImageModel {
  final int? id;
  final String? imageData;
  final String? thumbnail;

  ImageModel({
    this.id,
    this.imageData,
    this.thumbnail,
  });

  factory ImageModel.fromJson(Map<String, dynamic> json) {
    return ImageModel(
      id: json['id'] as int?,
      imageData: json['image_data'] as String?,
      thumbnail: json['thumbnail'] as String?,
    );
  }
}

class ParkingFeeModel {
  final String? parkingType;
  final double? fee;
  final int? capacity;
  final int? quotaUsed;

  ParkingFeeModel({
    this.parkingType,
    this.fee,
    this.capacity,
    this.quotaUsed,
  });

  factory ParkingFeeModel.fromJson(Map<String, dynamic> json) {
    return ParkingFeeModel(
      parkingType: json['parking_type'] as String?,
      fee: json['fee'] != null ? double.tryParse(json['fee'].toString()) : null,
      capacity: json['capacity'] as int?,
      quotaUsed: json['quota_used'] as int?,
    );
  }
}

class NearbyLocationModel {
  final String? name;
  final String? category;
  final int? distance;
  final String? distanceText;
  final double? lat;
  final double? lng;
  final bool? auto;

  NearbyLocationModel({
    this.name,
    this.category,
    this.distance,
    this.distanceText,
    this.lat,
    this.lng,
    this.auto,
  });

  factory NearbyLocationModel.fromJson(Map<String, dynamic> json) {
    return NearbyLocationModel(
      name: json['name'] as String?,
      category: json['category'] as String?,
      distance: json['distance'] as int?,
      distanceText: json['distance_text'] as String?,
      lat: json['lat'] as double?,
      lng: json['lng'] as double?,
      auto: json['auto'] as bool?,
    );
  }
}