import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/features/home/model/properties_model.dart';
import '../model/searchfilter_model.dart';
import '../data/repositories/searchresult_repository.dart';
import '../../home/data/repositories/property_repository.dart';
import '../../../core/network/api_result.dart';
import '../../../core/utils/app_logger.dart';

// Property repository provider for fetching all properties
final propertyRepositoryProvider = Provider<PropertyRepository>((ref) {
  return PropertyRepository();
});

class SearchFilterNotifier extends StateNotifier<SearchFilter> {
  SearchFilterNotifier() : super(
    SearchFilter(
      category: null,
      rentType: null,
      checkInDate: null,
      durationRaw: null,
      durationInDays: null,
      checkOutDate: null,
      city: null,
      province: null,
    ),
  ) {
    AppLogger.d('SearchFilterNotifier initialized - Category: ${state.category}, RentType: ${state.rentType}', 'SEARCH-FILTER');
  }

  void updateFilter(SearchFilter newFilter) {
    state = newFilter;

    AppLogger.i('Filter updated - Category: ${state.category}, RentType: ${state.rentType}, CheckIn: ${state.checkInDate}, CheckOut: ${state.checkOutDate}', 'SEARCH-FILTER');
  }

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


final searchFilterProvider = StateNotifierProvider<SearchFilterNotifier, SearchFilter>((ref) {
  AppLogger.d('searchFilterProvider created', 'SEARCH-FILTER');
  return SearchFilterNotifier();
});


final searchResultRepositoryProvider = Provider<SearchResultRepository>((ref) {
  return SearchResultRepository();
});


final searchResultsProvider = FutureProvider<List<PropertyModel>>((ref) async {
  final repository = ref.watch(searchResultRepositoryProvider);
  final currentFilter = ref.watch(searchFilterProvider);

  AppLogger.i('Search with filter - Category: ${currentFilter.category}, Province: ${currentFilter.province}', 'SEARCH-RESULT');

  ApiResult<List<PropertyModel>> result;

  /* Daily Multi Tier Pricing: use search/rooms API for daily rent type with dates */
  /* This gives availability checking and per-date pricing from m_room_prices */
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
    // Use PropertyRepository for fetching all properties
    final propertyRepository = ref.read(propertyRepositoryProvider);
    result = await propertyRepository.fetchProperties();
  }

  final properties = switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };

  // Sort by cheapest price: monthly first, then daily
  properties.sort((a, b) {
    // Get prices for property A
    final aMonthly = double.tryParse(a.priceOriginalMonthly) ?? double.infinity;
    final aDaily = double.tryParse(a.priceOriginalDaily) ?? double.infinity;
    final aPriceToCompare = aMonthly != double.infinity && aMonthly > 0 ? aMonthly : aDaily;

    // Get prices for property B
    final bMonthly = double.tryParse(b.priceOriginalMonthly) ?? double.infinity;
    final bDaily = double.tryParse(b.priceOriginalDaily) ?? double.infinity;
    final bPriceToCompare = bMonthly != double.infinity && bMonthly > 0 ? bMonthly : bDaily;

    // Sort ascending (cheapest first)
    return aPriceToCompare.compareTo(bPriceToCompare);
  });

  AppLogger.i('Properties sorted by cheapest price', 'SEARCH-RESULT');
  return properties;
});