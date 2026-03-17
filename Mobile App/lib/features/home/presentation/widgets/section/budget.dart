import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/widgets/card/propertycard.dart';
import '../../../../../core/widgets/animated_list_item.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../core/widgets/dialog/error_widgets.dart';
import '../../../../../core/widgets/comingsoon_widgets.dart';
import '../../../provider/property_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/appcolor_constants.dart';

class BudgetSection extends ConsumerStatefulWidget {
  final Color? backgroundColor;
  const BudgetSection({Key? key, this.backgroundColor}) : super(key: key);

  @override
  ConsumerState<BudgetSection> createState() => _BudgetSectionState();
}

class _BudgetSectionState extends ConsumerState<BudgetSection> {
  @override
  Widget build(BuildContext context) {
    final cheapestPropertiesAsyncValue = ref.watch(cheapestPropertiesProvider);
    final textTheme = Theme.of(context).textTheme;
    final localizations = AppLocalizations.of(context)!;

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
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      Text(
                        '💰 ',
                        style: const TextStyle(fontSize: 20),
                      ),
                      Flexible(
                        child: Text(
                          localizations.budget,
                          style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                          overflow: TextOverflow.ellipsis,
                          maxLines: 1,
                        ),
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                GestureDetector(
                  onTap: () {
                    // Navigate to search page with all properties sorted by cheapest price
                    context.push('/search');
                    AppLogger.d('Navigating to /search to show all properties sorted by price', 'BUDGET');
                  },
                  child: Text(
                    localizations.showAll,
                    style: const TextStyle(
                      fontSize: 14,
                      color: AppColors.primaryColor,
                      fontWeight: FontWeight.w600,
                    ),
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          cheapestPropertiesAsyncValue.when(
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
                              title: localizations.loadingBudgetProperty,
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
                        AppLogger.d('Image: ${item.image}', 'BUDGET');

                        // Prioritas harga: monthly > daily
                        final monthlyPrice = double.tryParse(item.priceOriginalMonthly) ?? 0;
                        final dailyPrice = double.tryParse(item.priceOriginalDaily) ?? 0;
                        final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
                        final priceLabel = monthlyPrice > 0 ? '/bulan' : '/hari';

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