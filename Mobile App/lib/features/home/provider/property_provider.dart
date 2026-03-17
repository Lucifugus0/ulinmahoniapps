import 'package:flutter_riverpod/flutter_riverpod.dart';
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


final bestSellerPropertiesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  AppLogger.i('Loading Best Seller properties', 'PROPERTY-PROVIDER');

  final allProperties = await ref.watch(propertiesProvider('').future);

  // Take first 3 as best sellers
  final limitedProperties = allProperties.take(3).toList();

  AppLogger.s('Loaded ${limitedProperties.length} Best Seller properties', 'PROPERTY-PROVIDER');
  return limitedProperties;
});


final cheapestPropertiesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  final repository = ref.watch(propertyRepositoryProvider);

  final result = await repository.fetchTop3CheapestProperties();

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});

final propertyByIdProvider = FutureProvider.family<PropertyModel?, int>((ref, propertyId) async {
  final repository = ref.watch(propertyRepositoryProvider);

  final result = await repository.fetchPropertyById(propertyId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});