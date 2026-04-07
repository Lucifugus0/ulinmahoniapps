import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/widgets/appbar/custom_appbar.dart';
import '../../../../core/widgets/card/propertycard.dart';
import '../../../../core/utils/formatcurrency.dart';
import '../../provider/property_provider.dart';

/// Page showing all properties with available rooms, sorted by highest availability.
class AvailableNowPage extends ConsumerWidget {
  const AvailableNowPage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;
    final allPropertiesAsync = ref.watch(propertiesProvider(''));

    return MainLayout(
      currentIndex: 0,
      showNavBar: false,
      showBottomNav: true,
      showContactBar: false,
      child: Scaffold(
        backgroundColor: Colors.transparent,
        extendBodyBehindAppBar: true,
        appBar: CustomAppBar(
          title: localizations.availableNow,
        ),
        body: SafeArea(
          top: false,
          child: allPropertiesAsync.when(
            data: (properties) {
              // Filter properties with available rooms, sort by most available first
              final available = properties
                  .where((p) => (p.availableRooms ?? 0) > 0)
                  .toList()
                ..sort((a, b) => (b.availableRooms ?? 0).compareTo(a.availableRooms ?? 0));

              if (available.isEmpty) {
                return Center(
                  child: Text(
                    localizations.roomTypeNoRoomsAvailable,
                    style: const TextStyle(fontSize: 16, color: Colors.grey),
                  ),
                );
              }

              return ListView.builder(
                // Top padding accounts for app bar height + status bar
                padding: EdgeInsets.fromLTRB(
                  16, MediaQuery.of(context).padding.top + kToolbarHeight + 16, 16, 32,
                ),
                itemCount: available.length,
                itemBuilder: (context, index) {
                  final property = available[index];
                  final monthlyPrice = double.tryParse(property.priceOriginalMonthly) ?? 0;
                  final dailyPrice = double.tryParse(property.priceOriginalDaily) ?? 0;
                  final displayPrice = monthlyPrice > 0 ? monthlyPrice : dailyPrice;
                  final priceLabel = monthlyPrice > 0
                      ? localizations.roomDetailsPerMonth
                      : localizations.roomDetailsPerDay;

                  return Padding(
                    padding: const EdgeInsets.only(bottom: 20.0),
                    child: PropertyCard(
                      image: property.thumbnail ?? property.image,
                      title: property.name,
                      location: '${property.subdistrict ?? ''}, ${property.city}'
                          .trim()
                          .replaceAll(RegExp(r'^,\s*|,\s*$'), ''),
                      price: displayPrice > 0
                          ? '${formatCurrency(displayPrice.toString())}$priceLabel'
                          : null,
                      imageHeight: 200,
                      totalRooms: property.totalRooms,
                      availableRooms: property.availableRooms,
                      gender: property.gender,
                      onTap: () {
                        context.push('/detailproperty/${property.idrec}');
                      },
                    ),
                  );
                },
              );
            },
            loading: () => Padding(
              padding: EdgeInsets.only(top: MediaQuery.of(context).padding.top + kToolbarHeight + 16),
              child: Skeletonizer(
                enabled: true,
                child: ListView.builder(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  itemCount: 4,
                  itemBuilder: (context, index) => Padding(
                    padding: const EdgeInsets.only(bottom: 20),
                    child: PropertyCard(
                      image: '',
                      title: 'Loading Property Name',
                      location: 'Loading Address',
                      price: 'Rp 0',
                      imageHeight: 200,
                      onTap: () {},
                    ),
                  ),
                ),
              ),
            ),
            error: (err, stack) => Center(
              child: Text(
                'Error: $err',
                style: const TextStyle(color: Colors.red),
              ),
            ),
          ),
        ),
      ),
    );
  }
}
