import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/widgets/card/propertycard.dart';
import '../../../../../core/widgets/animated_list_item.dart';
import '../../../provider/property_provider.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../core/widgets/comingsoon_widgets.dart';
import '../../../../../core/widgets/dialog/error_widgets.dart';
import '../../../../../core/utils/app_logger.dart';

class FilteredPropertyListView extends ConsumerWidget {
  final String selectedFilterLabel;
  
  final String? sectionTitle;
  final Color? backgroundColor; 

  const FilteredPropertyListView({
    super.key,
    required this.selectedFilterLabel,
    this.sectionTitle, 
    this.backgroundColor, 
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final propertiesAsync = ref.watch(propertiesProvider(selectedFilterLabel));
    final textTheme = Theme.of(context).textTheme;
    final localizations = AppLocalizations.of(context)!;

    return Container(
      color: backgroundColor, 
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          
          if (sectionTitle != null) ...[
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Text(
                sectionTitle!,
                style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
              ),
            ),
            const SizedBox(height: 15), 
          ],
          propertiesAsync.when(
            data: (propertyList) {
              AppLogger.d("selectedFilterLabel: $selectedFilterLabel", 'FILTEREDPROP');

              final activeProperties = propertyList
                  .where((property) => property.status == 1)
                  .toList();

              final displayableProperties = activeProperties
                  .where((property) {
                if (selectedFilterLabel.isEmpty) {
                  return true;
                }
                AppLogger.d("databasefilterlabel: ${property.tags.toLowerCase()}", 'FILTEREDPROP');
                return property.tags.toLowerCase().contains(selectedFilterLabel);
              }).toList();

              if (displayableProperties.isEmpty) {
                final cardHeight = MediaQuery.of(context).size.height * 0.22;
                return ComingSoonWidget(customHeight: cardHeight + 5);
              }

              return LayoutBuilder(
                builder: (context, constraints) {
                  final cardHeight = MediaQuery.of(context).size.height * 0.22;
                  return SizedBox(
                    height: cardHeight + 5, // Extra space for shadow
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      itemCount: displayableProperties.length,
                      itemBuilder: (context, index) {
                        final property = displayableProperties[index];

                        // Prioritas harga: monthly > daily
                        final monthlyPrice = double.tryParse(property.priceOriginalMonthly) ?? 0;
                        final dailyPrice = double.tryParse(property.priceOriginalDaily) ?? 0;
                        final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
                        // Use localized price suffix (bulan/hari in ID, month/day in EN, 月/天 in ZH)
                        final priceLabel = monthlyPrice > 0 ? localizations.roomDetailsPerMonth : localizations.roomDetailsPerDay;

                        return AnimatedListItem(
                          index: index,
                          totalItems: displayableProperties.length,
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              PropertyCard(
                                image: property.thumbnail ?? property.image,
                                title: property.name,
                                location: '${property.subdistrict ?? ''}, ${property.city}'.trim().replaceAll(RegExp(r'^,\s*|,\s*$'), ''),
                                detail: property.distance,
                                type: property.tags,
                                roomStatus: property.status,
                                price: displayPrice > 0 ? '${formatCurrency(displayPrice.toString())}$priceLabel' : null,
                                totalRooms: property.totalRooms,
                                availableRooms: property.availableRooms,
                                gender: property.gender,
                                onTap: () {
                                  AppLogger.d('Price: ${formatCurrency(displayPrice.toString())}', 'FILTEREDPROP');
                                  AppLogger.d('Navigating to detail for: ${property.name}', 'FILTEREDPROP');
                                  GoRouter.of(context).push('/detailproperty/${property.idrec}');
                                },
                              ),
                              if (index < displayableProperties.length - 1)
                                const SizedBox(width: 16),
                            ],
                          ),
                        );
                      },
                    ),
                  );
                },
              );
            },
            loading: () => LayoutBuilder(
              builder: (context, constraints) {
                final cardHeight = MediaQuery.of(context).size.height * 0.22;
                return SizedBox(
                  height: cardHeight + 5,
                  child: Skeletonizer(
                    enabled: true,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      itemCount: 3,
                      itemBuilder: (context, index) {
                        return Row(
                          children: [
                            PropertyCard(
                              image: '',
                              title: 'Loading Property Name',
                              location: 'Loading Address',
                              detail: 'Loading Distance',
                              type: 'Loading',
                              roomStatus: 1,
                              price: 'Rp 0',
                              onTap: () {},
                            ),
                            if (index < 2) const SizedBox(width: 16),
                          ],
                        );
                      },
                    ),
                  ),
                );
              },
            ),
            error: (error, stack) {
              return ErrorDisplayWidget(
                error: error,
                stackTrace: stack,
              );
            },
          ),
        ],
      ),
    );
  }
}