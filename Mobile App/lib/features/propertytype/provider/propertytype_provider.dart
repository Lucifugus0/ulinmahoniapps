import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/features/home/model/properties_model.dart';
import '../data/repositories/propertytype_repository.dart';
import '../../../core/network/api_result.dart';

/// Repository provider
final propertyTypeRepositoryProvider = Provider<PropertyTypeRepository>((ref) {
  return PropertyTypeRepository();
});

/// Fetch representative property types (one per type)
final representativePropertyTypesProvider = FutureProvider<List<PropertyModel>>((ref) async {
  final repository = ref.watch(propertyTypeRepositoryProvider);

  final result = await repository.fetchRepresentativePropertyTypes();

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});