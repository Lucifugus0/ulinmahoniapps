import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/widgets/card/propertycard.dart';
import '../../../../../core/widgets/animated_list_item.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../core/widgets/dialog/error_widgets.dart';
import '../../../../../core/widgets/comingsoon_widgets.dart';
import '../../../provider/property_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/appcolor_constants.dart';

class BestSellerSection extends ConsumerStatefulWidget {
  final Color? backgroundColor;
  const BestSellerSection({Key? key, this.backgroundColor}) : super(key: key);

  @override
  ConsumerState<BestSellerSection> createState() => _BestSellerSectionState();
}

class _BestSellerSectionState extends ConsumerState<BestSellerSection> {
  @override
  Widget build(BuildContext context) {
    final bestSellerPropertiesAsyncValue = ref.watch(bestSellerPropertiesProvider);
    final textTheme = Theme.of(context).textTheme;
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      color: widget.backgroundColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Expanded(
                  child: Text(
                    localizations.bestSeller,
                    style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                    overflow: TextOverflow.ellipsis,
                    maxLines: 1,
                  ),
                ),
                const SizedBox(width: 8),
                GestureDetector(
                  onTap: () {
                    // Navigate to search page with all properties sorted by cheapest price
                    context.push('/search');
                    AppLogger.d('Navigating to /search to show all properties sorted by price', 'BESTSELLER');
                  },
                  child: Text(
                    localizations.showAll,
                    style: TextStyle(
                      fontSize: 14,
                      // Use adaptive primary color for light mode; white for dark mode
                      color: isDark ? Colors.white : AppColors.primaryAdaptive(context),
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          bestSellerPropertiesAsyncValue.when(
            loading: () => LayoutBuilder(
              builder: (context, constraints) {
                final cardHeight = MediaQuery.of(context).size.height * 0.22;
                return SizedBox(
                  height: cardHeight + 10,
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
                              title: localizations.loadingBestSellerProperty,
                              location: localizations.loadingAddress,
                              detail: localizations.loadingDistance,
                              type: localizations.loadingType,
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
            error: (err, stack) =>
                ErrorDisplayWidget(
                  error: err,
                  stackTrace: stack,
                ),
            data: (properties) {
              if (properties.isEmpty) {
                return const ComingSoonWidget();
              }
              return LayoutBuilder(
                builder: (context, constraints) {
                  final cardHeight = MediaQuery.of(context).size.height * 0.22;
                  return SizedBox(
                    height: cardHeight + 10,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      itemCount: properties.length,
                      itemBuilder: (context, index) {
                        final item = properties[index];

                        // Prioritas harga: monthly > daily
                        final monthlyPrice = double.tryParse(item.priceOriginalMonthly) ?? 0;
                        final dailyPrice = double.tryParse(item.priceOriginalDaily) ?? 0;
                        final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
                        // Use localized price suffix (bulan/hari in ID, month/day in EN, 月/天 in ZH)
                        final priceLabel = monthlyPrice > 0 ? localizations.roomDetailsPerMonth : localizations.roomDetailsPerDay;

                        return AnimatedListItem(
                          index: index,
                          totalItems: properties.length,
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              PropertyCard(
                                image: item.thumbnail ?? item.image,
                                title: item.name,
                                location: '${item.subdistrict ?? ''}, ${item.city}'.trim().replaceAll(RegExp(r'^,\s*|,\s*$'), ''),
                                detail: item.distance,
                                type: item.tags,
                                roomStatus: item.status,
                                price: displayPrice > 0 ? '${formatCurrency(displayPrice.toString())}$priceLabel' : null,
                                totalRooms: item.totalRooms,
                                availableRooms: item.availableRooms,
                                gender: item.gender,
                                onTap: () {
                                  AppLogger.d('Navigasi ke detail untuk: ${item.name}', 'BESTSELLER');
                                  context.push('/detailproperty/${item.idrec}');
                                },
                              ),
                              if (index < properties.length - 1)
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
          ),
        ],
      ),
    );
  }
}