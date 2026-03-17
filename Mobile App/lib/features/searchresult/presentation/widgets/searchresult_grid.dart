import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/widgets/card/propertycard.dart';
import 'package:ulinmahoniapps/features/home/model/properties_model.dart';
import 'package:ulinmahoniapps/core/utils/formatcurrency.dart';
import 'package:ulinmahoniapps/features/searchresult/model/searchfilter_model.dart';
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

              // Calculate cheapest price: monthly first, then daily
              final monthlyPrice = double.tryParse(property.priceOriginalMonthly) ?? 0;
              final dailyPrice = double.tryParse(property.priceOriginalDaily) ?? 0;
              final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
              final priceLabel = monthlyPrice > 0 ? '/bulan' : '/hari';

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