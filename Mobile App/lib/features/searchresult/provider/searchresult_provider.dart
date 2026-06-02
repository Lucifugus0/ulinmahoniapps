import 'dart:math';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import 'package:ulinmahoniapps/features/home/model/properties_model.dart';
import 'package:ulinmahoniapps/features/home/provider/property_provider.dart';
import '../model/searchfilter_model.dart';
import '../data/repositories/searchresult_repository.dart';
import '../../home/data/repositories/property_repository.dart';
import '../../../core/network/api_result.dart';
import '../../../core/utils/app_logger.dart';

/// Property repository provider for fetching all properties.
final propertyRepositoryProvider = Provider<PropertyRepository>((ref) {
  return PropertyRepository();
});

/// Notifier for search filter state.
/// Migrated from StateNotifier to Notifier for Riverpod 3.x.
class SearchFilterNotifier extends Notifier<SearchFilter> {
  /// build() returns the initial empty filter state.
  @override
  SearchFilter build() {
    final initialFilter = SearchFilter(
      category: null,
      rentType: null,
      checkInDate: null,
      durationRaw: null,
      durationInDays: null,
      checkOutDate: null,
      city: null,
      province: null,
    );
    AppLogger.d('SearchFilterNotifier initialized - Category: ${initialFilter.category}, RentType: ${initialFilter.rentType}', 'SEARCH-FILTER');
    return initialFilter;
  }

  /// Replace the entire filter with a new one.
  void updateFilter(SearchFilter newFilter) {
    state = newFilter;

    AppLogger.i('Filter updated - Category: ${state.category}, RentType: ${state.rentType}, CheckIn: ${state.checkInDate}, CheckOut: ${state.checkOutDate}', 'SEARCH-FILTER');
  }

  /// Partially update filter fields — only non-null params are applied.
  void updateFilterPartial({
    String? category,
    String? rentType,
    String? checkInDate,
    int? durationRaw,
    int? durationInDays,
    String? checkOutDate,
    String? city,
    String? province
  }) {
    state = state.copyWith(
        category: category,
        rentType: rentType,
        checkInDate: checkInDate,
        durationRaw: durationRaw,
        durationInDays: durationInDays,
        checkOutDate: checkOutDate,
        city: city,
        province: province
    );

    AppLogger.i('Filter partially updated - Category: ${state.category}, Rent: ${state.rentType}, Province: ${state.province}, CheckIn: ${state.checkInDate}, Duration: ${state.durationRaw}, CheckOut: ${state.checkOutDate}', 'SEARCH-FILTER');
  }

  /// Reset filter to default empty state.
  void resetFilter() {
    state = SearchFilter(
        category: null,
        rentType: null,
        checkInDate: null,
        durationRaw: null,
        durationInDays: null,
        checkOutDate: null,
        city: null,
        province: null
    );
    AppLogger.i('Filter reset to default', 'SEARCH-FILTER');
  }
}

/// Provider for search filter state — Riverpod 3.x NotifierProvider.
final searchFilterProvider = NotifierProvider<SearchFilterNotifier, SearchFilter>(
  SearchFilterNotifier.new,
);

/// Provider for search result repository singleton.
final searchResultRepositoryProvider = Provider<SearchResultRepository>((ref) {
  return SearchResultRepository();
});

/// Calculate distance in km between two GPS coordinates using Haversine formula
double _searchCalcDistance(Position user, double? lat, double? lng) {
  if (lat == null || lng == null) return double.infinity;
  const earthRadius = 6371.0; // km
  final dLat = _toRad(lat - user.latitude);
  final dLng = _toRad(lng - user.longitude);
  final a = sin(dLat / 2) * sin(dLat / 2) +
      cos(_toRad(user.latitude)) * cos(_toRad(lat)) *
      sin(dLng / 2) * sin(dLng / 2);
  final c = 2 * atan2(sqrt(a), sqrt(1 - a));
  return earthRadius * c;
}

double _toRad(double degrees) => degrees * pi / 180;

/// FutureProvider that fetches search results, filters out unavailable properties,
/// calculates GPS distance, and sorts by distance (fallback: available rooms desc).
final searchResultsProvider = FutureProvider<List<PropertyWithDistance>>((ref) async {
  final repository = ref.watch(searchResultRepositoryProvider);
  final currentFilter = ref.watch(searchFilterProvider);

  AppLogger.i('Search with filter - Category: ${currentFilter.category}, Province: ${currentFilter.province}', 'SEARCH-RESULT');

  ApiResult<List<PropertyModel>> result;

  /* Daily Multi Tier Pricing: use search/rooms API for daily rent type with dates */
  if (currentFilter.rentType?.toLowerCase() == 'daily' &&
      currentFilter.checkInDate != null && currentFilter.checkInDate!.isNotEmpty &&
      currentFilter.checkOutDate != null && currentFilter.checkOutDate!.isNotEmpty) {
    AppLogger.i('Daily search with dates: ${currentFilter.checkInDate} to ${currentFilter.checkOutDate}', 'SEARCH-RESULT');
    result = await repository.searchRoomsDaily(
      type: currentFilter.category,
      checkIn: currentFilter.checkInDate!,
      checkOut: currentFilter.checkOutDate!,
      province: currentFilter.province,
    );
  } else if (currentFilter.province != null && currentFilter.province!.isNotEmpty) {
    AppLogger.i('Searching by province: ${currentFilter.province}', 'SEARCH-RESULT');
    result = await repository.fetchPropertiesByProvince(currentFilter.province!);
  } else if (currentFilter.category != null && currentFilter.category!.isNotEmpty) {
    AppLogger.i('Searching by tag: ${currentFilter.category}', 'SEARCH-RESULT');
    result = await repository.fetchPropertiesByTag(currentFilter.category!);
  } else {
    AppLogger.i('Fetching all properties (no filter)', 'SEARCH-RESULT');
    final propertyRepository = ref.read(propertyRepositoryProvider);
    result = await propertyRepository.fetchProperties();
  }

  final properties = switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };

  // Filter out properties with no available rooms
  final available = properties.where((p) => (p.availableRooms ?? 0) > 0).toList();
  AppLogger.i('Filtered: ${properties.length} → ${available.length} with available rooms', 'SEARCH-RESULT');

  // Get user GPS position (only if permission already granted)
  Position? userPosition;
  try {
    final permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.whileInUse ||
        permission == LocationPermission.always) {
      userPosition = await Geolocator.getLastKnownPosition();
      userPosition ??= await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.low,
          timeLimit: Duration(seconds: 5),
        ),
      );
    }
  } catch (e) {
    AppLogger.w('GPS check failed in search: $e', 'SEARCH-RESULT');
  }

  // Wrap properties with computed distance
  final withDistance = available.map((p) {
    final dist = userPosition != null
        ? _searchCalcDistance(userPosition, p.latitude, p.longitude)
        : null;
    return PropertyWithDistance(p, dist == double.infinity ? null : dist);
  }).toList();

  // Sort: by distance if GPS available, otherwise by available rooms descending
  if (userPosition != null) {
    withDistance.sort((a, b) =>
        (a.distanceKm ?? double.infinity).compareTo(b.distanceKm ?? double.infinity));
    AppLogger.i('Sorted by distance (closest first)', 'SEARCH-RESULT');
  } else {
    withDistance.sort((a, b) =>
        (b.property.availableRooms ?? 0).compareTo(a.property.availableRooms ?? 0));
    AppLogger.i('No GPS — sorted by available rooms desc', 'SEARCH-RESULT');
  }

  return withDistance;
});
