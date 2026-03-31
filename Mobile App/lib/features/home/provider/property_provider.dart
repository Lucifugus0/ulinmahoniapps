import 'dart:math';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import '../data/repositories/property_repository.dart';
import '../model/properties_model.dart';
import '../../../core/network/api_result.dart';
import '../../../core/utils/app_logger.dart';

final propertyRepositoryProvider = Provider<PropertyRepository>((ref) {
  return PropertyRepository();
});


final propertiesProvider = FutureProvider.family<List<PropertyModel>, String>((ref, tag) async {
  final repository = ref.watch(propertyRepositoryProvider);

  final result = tag.isEmpty
      ? await repository.fetchProperties()
      : await repository.fetchPropertiesByTag(tag);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});

final distinctCitiesProvider = FutureProvider<List<String>>((ref) async {
  final repository = ref.watch(propertyRepositoryProvider);

  final result = await repository.fetchDistinctCities(limit: 6);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});


final distinctCityPropertiesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  final repository = ref.watch(propertyRepositoryProvider);
  final List<String> distinctCities = await ref.watch(distinctCitiesProvider.future);

  final List<PropertyModel> resultProperties = [];
  for (final city in distinctCities) {
    final result = await repository.fetchPropertiesByCities([city]);

    switch (result) {
      case Success(:final data):
        if (data.isNotEmpty) {
          resultProperties.add(data.first);
        }
      case Failure(:final message):
        AppLogger.w('Error fetching property for city $city: $message', 'PROPERTY-PROVIDER');
    }
  }
  return resultProperties;
});


/// "Available Now" — properties with highest room availability, excluding full properties
final availableNowPropertiesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  AppLogger.i('Loading Available Now properties', 'PROPERTY-PROVIDER');

  final allProperties = await ref.watch(propertiesProvider('').future);

  // Filter out full properties (availableRooms == 0 or null) and sort by most available
  final available = allProperties
      .where((p) => (p.availableRooms ?? 0) > 0)
      .toList()
    ..sort((a, b) => (b.availableRooms ?? 0).compareTo(a.availableRooms ?? 0));

  final limitedProperties = available.take(3).toList();

  AppLogger.s('Loaded ${limitedProperties.length} Available Now properties', 'PROPERTY-PROVIDER');
  return limitedProperties;
});


/// "Near You" section — properties sorted by GPS distance, fallback to city name.
/// GPS permission is checked but never requested here (avoid blocking UI).
/// Only uses location if already granted.
final cheapestPropertiesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  final allProperties = await ref.watch(propertiesProvider('').future);
  final available = allProperties.where((p) => (p.availableRooms ?? 0) > 0).toList();

  // Only use GPS if permission was already granted — never request here
  Position? userPosition;
  try {
    final permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.whileInUse ||
        permission == LocationPermission.always) {
      // Use last known position (instant, no GPS wait)
      userPosition = await Geolocator.getLastKnownPosition();
    }
  } catch (e) {
    AppLogger.w('GPS check failed: $e', 'PROPERTY-PROVIDER');
  }

  if (userPosition != null) {
    available.sort((a, b) {
      final distA = _calculateDistance(userPosition!, a.latitude, a.longitude);
      final distB = _calculateDistance(userPosition!, b.latitude, b.longitude);
      return distA.compareTo(distB);
    });
  } else {
    // Fallback: sort by city name ascending
    available.sort((a, b) => a.city.toLowerCase().compareTo(b.city.toLowerCase()));
  }

  return available.take(3).toList();
});

/// Calculate distance in km between user position and property coordinates
double _calculateDistance(Position user, double? lat, double? lng) {
  if (lat == null || lng == null) return double.infinity;
  const earthRadius = 6371.0; // km
  final dLat = _toRadians(lat - user.latitude);
  final dLng = _toRadians(lng - user.longitude);
  final a = sin(dLat / 2) * sin(dLat / 2) +
      cos(_toRadians(user.latitude)) * cos(_toRadians(lat)) *
      sin(dLng / 2) * sin(dLng / 2);
  final c = 2 * atan2(sqrt(a), sqrt(1 - a));
  return earthRadius * c;
}

double _toRadians(double degrees) => degrees * pi / 180;

final propertyByIdProvider = FutureProvider.family<PropertyModel?, int>((ref, propertyId) async {
  final repository = ref.watch(propertyRepositoryProvider);

  final result = await repository.fetchPropertyById(propertyId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});