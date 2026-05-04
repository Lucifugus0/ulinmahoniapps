import 'dart:convert';
import '../../../../core/utils/app_logger.dart';
import 'facility_model.dart';

class RoomModel {
  final int? id;
  final int? propertyId;
  final String? propertyName;
  final String? slug;
  final String? name;
  final String? descriptions;
  final Map<String, bool> periode;
  final int? periode_daily;
  final int? periode_monthly;
  final String? type;
  final String? level;
  final List<String>? facility; // Deprecated: for backward compatibility
  final List<FacilityModel>? facilities; // New: list of facilities with icons
  final Map<String, dynamic> price;
  final String? priceOriginalDaily;
  final String? priceOriginalMonthly;
  final String? adminfee;
  final dynamic attachment;
  final DateTime? createdAt;
  final DateTime? updatedAt;
  final String? createdBy;
  final String? updatedBy;
  final int? status;
  final List<ImageModel>? roomimages;
  final String? roomimageshow;
  final String? thumbnail;
  final int? capacity;
  final String? bed_type;
  final int? size;
  final String? no;
  final int? rentalStatus;
  final double? depositFee;
  final List<ParkingFeeModel> parkingFees;
  // Multi-Tier Pricing: additional pricing fields (nullable for backward compat with old API)
  final String? priceWeekday;
  final String? priceWeekend;
  final String? priceOriginalAnnual;
  final int? periodeAnnual;
  final bool? hasSeasonalPricing;
  /// Multi-language descriptions parsed from API as `{"id": "...", "en": "...", "zh": "..."}`
  final Map<String, String>? descriptionsParsed;
  /// Admin-controlled sort_priority for the room's type name (m_room_name_types).
  /// Drives ordering of the room-name filter dropdown — lower values come first.
  /// Null when the room's name isn't registered in m_room_name_types.
  final int? typeSortPriority;

  RoomModel({
    required this.id,
    this.propertyId,
    this.propertyName,
    this.slug,
    this.name,
    this.descriptions,
    required this.periode,
    this.periode_daily,
    this.periode_monthly,
    this.type,
    this.level,
    this.facility,
    this.facilities,
    required this.price,
    this.priceOriginalDaily,
    this.priceOriginalMonthly,
    this.adminfee,
    this.attachment,
    this.createdAt,
    this.updatedAt,
    this.createdBy,
    this.updatedBy,
    required this.status,
    this.roomimages,
    this.roomimageshow,
    this.thumbnail,
    this.capacity,
    this.bed_type,
    this.size,
    this.no,
    this.rentalStatus,
    this.depositFee,
    this.parkingFees = const [],
    // Multi-Tier Pricing: optional fields
    this.priceWeekday,
    this.priceWeekend,
    this.priceOriginalAnnual,
    this.periodeAnnual,
    this.hasSeasonalPricing,
    this.descriptionsParsed,
    this.typeSortPriority,
  });

  /* Daily Multi Tier Pricing: create a copy with overridden daily price */
  /* Used to pass effective average rate to payment so it calculates correct total */
  RoomModel copyWithDailyPrice(String newDailyPrice) {
    return RoomModel(
      id: id, propertyId: propertyId, propertyName: propertyName, slug: slug,
      name: name, descriptions: descriptions, periode: periode,
      periode_daily: periode_daily, periode_monthly: periode_monthly,
      type: type, level: level, facility: facility, facilities: facilities,
      price: price, priceOriginalDaily: newDailyPrice,
      priceOriginalMonthly: priceOriginalMonthly, adminfee: adminfee,
      attachment: attachment, createdAt: createdAt, updatedAt: updatedAt,
      createdBy: createdBy, updatedBy: updatedBy, status: status,
      roomimages: roomimages, roomimageshow: roomimageshow, thumbnail: thumbnail,
      capacity: capacity, bed_type: bed_type, size: size, no: no,
      rentalStatus: rentalStatus, depositFee: depositFee, parkingFees: parkingFees,
      priceWeekday: priceWeekday, priceWeekend: priceWeekend,
      priceOriginalAnnual: priceOriginalAnnual, periodeAnnual: periodeAnnual,
      hasSeasonalPricing: hasSeasonalPricing,
      descriptionsParsed: descriptionsParsed,
      typeSortPriority: typeSortPriority,
    );
  }

  factory RoomModel.fromJson(Map<String, dynamic> json) {
    final List<ImageModel>? parsedImages =
        (json['images'] as List<dynamic>?)
            ?.map((e) => ImageModel.fromJson(e as Map<String, dynamic>))
            .toList();

    List<ParkingFeeModel> parsedParkingFees = [];
    if (json['parking_fees'] is List) {
      parsedParkingFees = (json['parking_fees'] as List)
          .map((item) => ParkingFeeModel.fromJson(item as Map<String, dynamic>))
          .toList();
    }

    // Parse facilities (new format with icons)
    List<FacilityModel>? parsedFacilities;
    List<String>? parsedFacilityStrings;

    if (json['facility'] != null) {
      final facilityData = json['facility'];
      if (facilityData is List && facilityData.isNotEmpty) {
        // Check if first item is a Map (new format) or String (old format)
        if (facilityData.first is Map) {
          parsedFacilities = facilityData
              .map((item) => FacilityModel.fromJson(item as Map<String, dynamic>))
              .toList();
        } else {
          // Old format: List<String>
          parsedFacilityStrings = facilityData
              .map((e) => e.toString())
              .toList();
        }
      }
    }

    String validImageUrl = '';

    // Prioritas 1: Gunakan thumbnail dari root level
    final String? rootThumbnail = json['thumbnail']?.toString();
    if (rootThumbnail != null && rootThumbnail.isNotEmpty) {
      validImageUrl = rootThumbnail;
    } else {
      // Fallback: Gunakan image_data dari item pertama di images array
      if (parsedImages != null && parsedImages.isNotEmpty) {
        for (var imageEntry in parsedImages) {
          String? currentImageUrl = imageEntry.imageData;
          if (currentImageUrl != null && currentImageUrl.isNotEmpty) {
            validImageUrl = currentImageUrl;
            break;
          }
        }
      }
    }

    return RoomModel(
      id: json['idrec'] ?? 0,
      propertyId:
          json['property_id'] is int
              ? json['property_id']
              : int.tryParse(json['property_id']?.toString() ?? '') ?? 0,
      propertyName: json['property_name'],
      slug: json['slug'],
      name: json['name'],
      descriptions: json['descriptions'],
      periode: parseMapBool(json['periode']),
      periode_daily: json['periode_daily'] ?? 0,
      periode_monthly: json['periode_monthly'] ?? 0,
      type: json['type'],
      level: json['level']?.toString(),
      facility: parsedFacilityStrings,
      facilities: parsedFacilities,
      price: parseMapDynamic(json['price']),
      priceOriginalDaily: json['price_original_daily']?.toString(),
      priceOriginalMonthly: json['price_original_monthly']?.toString(),
      adminfee: json['admin_fees']?.toString(),
      attachment: json['attachment'],
      createdAt: parseDate(json['created_at']),
      updatedAt: parseDate(json['updated_at']),
      createdBy: json['created_by'],
      updatedBy: json['updated_by'],
      status: json['status'] ?? 0,
      roomimages: parsedImages,
      roomimageshow: validImageUrl.isNotEmpty ? validImageUrl : null,
      thumbnail: json['thumbnail']?.toString(),
      capacity: json['capacity'] ?? 0,
      bed_type: json['bed_type'],
      size: json['size'] ?? 0,
      no: json['no'].toString(),
      rentalStatus: json['rental_status'] ?? 0,
      depositFee: json['deposit_fee'] != null ? double.tryParse(json['deposit_fee'].toString()) : null,
      parkingFees: parsedParkingFees,
      // Multi-language: parse descriptions_parsed map for locale-aware display
      descriptionsParsed: json['descriptions_parsed'] != null
          ? Map<String, String>.from(
              (json['descriptions_parsed'] as Map).map(
                (key, value) => MapEntry(key.toString(), value?.toString() ?? ''),
              ),
            )
          : null,
      // Multi-Tier Pricing: parse new optional fields with null-safety fallback
      priceWeekday: json['price_weekday']?.toString(),
      priceWeekend: json['price_weekend']?.toString(),
      priceOriginalAnnual: json['price_original_annual']?.toString(),
      periodeAnnual: json['periode_annual'] ?? 0,
      hasSeasonalPricing: json['has_seasonal_pricing'] ?? false,
      // Admin-controlled room-type ordering (lower = comes first). Null when missing.
      typeSortPriority: json['type_sort_priority'] is int
          ? json['type_sort_priority'] as int
          : (json['type_sort_priority'] != null
              ? int.tryParse(json['type_sort_priority'].toString())
              : null),
    );
  }

  static DateTime parseDate(dynamic input) {
    if (input is String) {
      return DateTime.tryParse(input) ?? DateTime(2000);
    }
    return DateTime(2000);
  }

  static double? parseDouble(dynamic input) {
    if (input == null) return null;
    try {
      return double.tryParse(input.toString());
    } catch (_) {
      return null;
    }
  }
}

class ImageModel {
  final int? id;
  final String? imageData;
  final String? thumbnail;
  final String? caption;

  ImageModel({this.id, this.imageData, this.caption,this.thumbnail});

  factory ImageModel.fromJson(Map<String, dynamic> json) {
    return ImageModel(
      id: json['id'] as int?,
      imageData: json['image_data'] as String?,
      caption: json['caption'] as String?,
      thumbnail: json['thumbnail'] as String?
    );
  }
}

Map<String, bool> parseMapBool(dynamic raw) {
  if (raw == null) return {};
  try {
    if (raw is String) {
      final decoded = jsonDecode(raw);
      if (decoded is Map) {
        return decoded.map((key, value) {
          final k = key.toString();
          final v =
              value is bool ? value : value.toString().toLowerCase() == 'true';
          return MapEntry(k, v);
        });
      }
    }

    if (raw is Map) {
      return raw.map((key, value) {
        final k = key.toString();
        final v =
            value is bool ? value : value.toString().toLowerCase() == 'true';
        return MapEntry(k, v);
      });
    }
  } catch (e) {
    AppLogger.w("parseMapBool error: $e", 'ROOM-MODEL');
  }
  return {};
}

Map<String, dynamic> parseMapDynamic(dynamic raw) {
  if (raw == null) return {};
  try {
    if (raw is String) {
      final decoded = jsonDecode(raw);
      if (decoded is Map) {
        return decoded.map((key, value) => MapEntry(key.toString(), value));
      }
    }

    if (raw is Map) {
      return raw.map((key, value) => MapEntry(key.toString(), value));
    }
  } catch (e) {
    AppLogger.w("parseMapDynamic error: $e", 'ROOM-MODEL');
  }
  return {};
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
