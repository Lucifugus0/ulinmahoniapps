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
                  child: Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
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
                    // Navigate to Near You page — all properties sorted by GPS distance
                    context.push('/near-you');
                    AppLogger.d('Navigating to /near-you', 'BUDGET');
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
            data: (propertiesWithDistance) {
              if (propertiesWithDistance.isEmpty) {
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
                      itemCount: propertiesWithDistance.length,
                      itemBuilder: (context, index) {
                        final pwd = propertiesWithDistance[index];
                        final item = pwd.property;
                        AppLogger.d('Image: ${item.image}', 'BUDGET');

                        // Format distance in km for display (e.g. "2.3 km")
                        String? distanceLabel;
                        if (pwd.distanceKm != null) {
                          distanceLabel = '${pwd.distanceKm!.toStringAsFixed(1)} km';
                        }

                        // Prioritas harga: monthly > daily
                        final monthlyPrice = double.tryParse(item.priceOriginalMonthly) ?? 0;
                        final dailyPrice = double.tryParse(item.priceOriginalDaily) ?? 0;
                        final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
                        // Use localized price suffix (bulan/hari in ID, month/day in EN, 月/天 in ZH)
                        final priceLabel = monthlyPrice > 0 ? localizations.roomDetailsPerMonth : localizations.roomDetailsPerDay;

                        return AnimatedListItem(
                          index: index,
                          totalItems: propertiesWithDistance.length,
                          child: Row(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              PropertyCard(
                                image: item.thumbnail ?? item.image,
                                title: item.name,
                                location: '${item.subdistrict ?? ''}, ${item.city}'.trim().replaceAll(RegExp(r'^,\s*|,\s*$'), ''),
                                detail: distanceLabel,
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
                              if (index < propertiesWithDistance.length - 1)
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