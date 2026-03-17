import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../model/detailproperty_model.dart';
import '../data/repositories/detailproperty_repository.dart';
import '../../../../core/network/api_result.dart';
import 'package:flutter/foundation.dart';
import '../../../../core/utils/app_logger.dart';

final detailPropertyRepositoryProvider = Provider<DetailPropertyRepository>((ref) {
  return DetailPropertyRepository();
});


final detailPropertyProvider = FutureProvider.family<DetailPropertyModel, int>((ref, propertyId) async {
  AppLogger.d('🔵 PROVIDER: Fetching detail property for ID: $propertyId', 'DETAIL-PROPERTY-PROVIDER');
  final repository = ref.read(detailPropertyRepositoryProvider);

  final result = await repository.fetchDetailProperty(propertyId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});
