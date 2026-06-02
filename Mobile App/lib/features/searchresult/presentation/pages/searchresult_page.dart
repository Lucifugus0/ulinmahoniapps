import 'package:flutter/material.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/core/widgets/appbar.dart';
import '../../../../core/layout/mainlayout.dart';
import '../widgets/searchresult_grid.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../provider/searchresult_provider.dart';
import 'package:ulinmahoniapps/features/home/provider/property_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/widgets/card/propertycard.dart';

class SearchResult extends ConsumerWidget {
  const SearchResult({
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!; 
    final searchResults = ref.watch(searchResultsProvider);
    final currentFilter = ref.watch(searchFilterProvider);

    int activeFilterCount = 0;
    if (currentFilter.rentType != null && currentFilter.rentType!.isNotEmpty) {
      activeFilterCount++;
    }
    if (currentFilter.category != null && currentFilter.category!.isNotEmpty) {
      activeFilterCount++;
    }
    if (currentFilter.durationRaw != null && currentFilter.durationRaw! > 0) {
      activeFilterCount++;
    }
    if (currentFilter.checkInDate != null && currentFilter.checkInDate!.isNotEmpty) {
      activeFilterCount++;
    }
    if (currentFilter.checkOutDate != null && currentFilter.checkOutDate!.isNotEmpty) {
      activeFilterCount++;
    }
    if (currentFilter.province != null && currentFilter.province!.isNotEmpty) {
      activeFilterCount++;
    }

    double? appBarCustomToolbarHeight;
    double? appBarCustomSearchDetailsHeight;

    switch (activeFilterCount) {
      case 0:
        appBarCustomToolbarHeight = null;
        appBarCustomSearchDetailsHeight = null;
        break;
      case 1:
        appBarCustomToolbarHeight = 40;
        appBarCustomSearchDetailsHeight = 25;
        break;
      case 2:
        appBarCustomToolbarHeight = 40;
        appBarCustomSearchDetailsHeight = 25;
        break;
      case 3:
        appBarCustomToolbarHeight = 40;
        appBarCustomSearchDetailsHeight = 45;
        break;
      case 4:
        appBarCustomToolbarHeight = 60;
        appBarCustomSearchDetailsHeight = 45;
        break;
      case 5:
        appBarCustomToolbarHeight = 60;
        appBarCustomSearchDetailsHeight = 45;
        break;
      case 6:
        appBarCustomToolbarHeight = 60;
        appBarCustomSearchDetailsHeight = 45;
        break;
      default:
        appBarCustomToolbarHeight = 60;
        appBarCustomSearchDetailsHeight = 45;
        break;
    }

    return MainLayout(
      currentIndex: 0,
      showBottomNav: true,
      showNavBar: false,
      showContactBar: false,
      bottomcontactbar_pesansekarang: false,
      child: SafeArea(
        child: Column(
          children: [
            CustomAppBar(
              title: localizations.searchResultTitle, 
              isSearch: true,
              rentalType: currentFilter.rentType,
              category: currentFilter.category,
              duration: currentFilter.durationRaw,
              checkInDate: currentFilter.checkInDate,
              checkOutDate: currentFilter.checkOutDate,
              province: currentFilter.province,
              customToolbarHeight: appBarCustomToolbarHeight,
              customSearchDetailsHeight: appBarCustomSearchDetailsHeight,
            ),
            Expanded(
              child: searchResults.when(
                data: (properties) {
                  return SearchResultGrid(properties: properties, currentFilter: currentFilter);
                },
                loading: () {
                  return Skeletonizer(
                    enabled: true,
                    child: ListView.builder(
                      padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0),
                      itemCount: 5,
                      itemBuilder: (context, index) {
                        return Padding(
                          padding: const EdgeInsets.only(bottom: 20.0),
                          child: PropertyCard(
                            image: '',
                            title: 'Loading Property Name Here',
                            location: 'Loading Address Here',
                            detail: 'Loading Distance',
                            type: 'Loading',
                            roomStatus: 1,
                            price: 'Rp 0',
                            imageHeight: 200,
                            onTap: () {},
                          ),
                        );
                      },
                    ),
                  );
                },
                error: (error, stackTrace) {
                  AppLogger.e('Error in SearchResult', error, stackTrace, 'SEARCH-RESULT');
                  return Center(child: Text('${localizations.searchResultFailedToLoad}: $error'));
                },
              ),
            ),
          ],
        ),
      ),
    );
  }
}