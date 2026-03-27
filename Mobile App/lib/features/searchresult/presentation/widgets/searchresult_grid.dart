import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/widgets/card/propertycard.dart';
import 'package:ulinmahoniapps/features/home/model/properties_model.dart';
import 'package:ulinmahoniapps/core/utils/formatcurrency.dart';
import 'package:ulinmahoniapps/features/searchresult/model/searchfilter_model.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../core/utils/app_logger.dart';

class SearchResultGrid extends StatelessWidget {
  final List<PropertyModel> properties;
  final SearchFilter currentFilter;

  const SearchResultGrid({
    super.key,
    required this.properties,
    required this.currentFilter,
  });

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    AppLogger.d('DEBUG: currentFilter.rentType: ${currentFilter.rentType}', 'SEARCH-RESULT');
    AppLogger.d('DEBUG: currentFilter.category: ${currentFilter.category}', 'SEARCH-RESULT');
    AppLogger.d('DEBUG: currentFilter.durationRaw: ${currentFilter.durationRaw}', 'SEARCH-RESULT');
    AppLogger.d('DEBUG: currentFilter.checkInDate: ${currentFilter.checkInDate}', 'SEARCH-RESULT');
    AppLogger.d('DEBUG: currentFilter.checkOutDate: ${currentFilter.checkOutDate}', 'SEARCH-RESULT');

    if (properties.isEmpty) {
      return const Center(
        child: Text(
          'Tidak ada properti ditemukan dengan filter ini.',
          style: TextStyle(fontSize: 16, color: Colors.grey),
        ),
      );
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          child: ListView.builder(
            scrollDirection: Axis.vertical, 
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0), 
            itemCount: properties.length,
            itemBuilder: (context, index) {
              final property = properties[index];

              /* Daily Multi Tier Pricing: show total price when daily search with dates */
              /* Otherwise fall back to existing logic: monthly first, then daily */
              final bool hasTotalPrice = property.lowestTotalPrice != null && property.lowestTotalPrice! > 0;
              final monthlyPrice = double.tryParse(property.priceOriginalMonthly) ?? 0;
              final dailyPrice = double.tryParse(property.priceOriginalDaily) ?? 0;

              final double displayPrice;
              final String priceLabel;

              if (hasTotalPrice && property.totalDays != null) {
                displayPrice = property.lowestTotalPrice!;
                priceLabel = '/ ${property.totalDays} malam';
              } else if (monthlyPrice > 0) {
                displayPrice = monthlyPrice;
                priceLabel = localizations.roomDetailsPerMonth;
              } else {
                displayPrice = dailyPrice;
                priceLabel = localizations.roomDetailsPerDay;
              }

              return Padding(
                padding: const EdgeInsets.only(bottom: 20.0),
                child: PropertyCard(
                  image: property.image,
                  title: property.name,
                  location: '${property.subdistrict ?? ''}, ${property.city}'.trim().replaceAll(RegExp(r'^,\s*|,\s*$'), ''),
                  detail: property.distance,
                  price: displayPrice > 0 ? '${formatCurrency(displayPrice.toString())}$priceLabel' : null,
                  imageHeight: 200,
                  onTap: () {
                    context.push('/detailproperty/${property.idrec}');
                  },
                ),
              );
            },
          ),
        ),
      ],
    );
  }
}