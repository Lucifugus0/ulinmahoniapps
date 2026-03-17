import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/promo_banner_repository.dart';
import '../model/promo_banner_model.dart';
import '../../../core/network/api_result.dart';

/// Repository provider for promo banner
final promoBannerRepositoryProvider = Provider<PromoBannerRepository>((ref) {
  return PromoBannerRepository();
});

/// Provider for fetching active promo banners
/// Returns list of active banners sorted by priority (descending)
final activeBannersProvider = FutureProvider<List<PromoBannerModel>>((ref) async {
  final repository = ref.read(promoBannerRepositoryProvider);
  final result = await repository.getActiveBanners();

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});

/// Provider for fetching specific banner by ID
/// Usage: ref.watch(bannerByIdProvider(bannerId))
final bannerByIdProvider = FutureProvider.family<PromoBannerModel, int>((ref, bannerId) async {
  final repository = ref.read(promoBannerRepositoryProvider);
  final result = await repository.getBannerById(bannerId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});
