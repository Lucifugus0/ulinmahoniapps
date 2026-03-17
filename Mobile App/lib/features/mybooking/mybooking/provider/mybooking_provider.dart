import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../model/mybooking_model.dart';
import '../model/propertyimage_model.dart';
import '../data/repositories/mybooking_repository.dart';
import '../../../auth/login/provider/auth_provider.dart';
import '../../../../core/network/api_result.dart';

final myBookingRepositoryProvider = Provider<MyBookingRepository>((ref) {
  return MyBookingRepository();
});


final userBookingsProvider = FutureProvider<List<MyBookingModel>>((ref) async {
  final authState = ref.watch(authProvider);
  final user = authState.user.value;

  if (user == null) {
    throw Exception("User not logged in");
  }

  if (user.id == null) {
    throw Exception("User ID is null");
  }

  final repository = ref.read(myBookingRepositoryProvider);

  final result = await repository.fetchBookingByUserId(user.id);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});

final propertyImagesProvider = FutureProvider.family<List<PropertyImageModel>, int>((ref, propertyId) async {
  final repository = ref.read(myBookingRepositoryProvider);

  final result = await repository.fetchPropertyImageByPropertyId(propertyId);

  return switch (result) {
    Success(:final data) => data != null ? [data] : [],
    Failure(:final message) => throw Exception(message),
  };
});
