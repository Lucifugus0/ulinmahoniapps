import 'dart:math';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:geolocator/geolocator.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/widgets/appbar/custom_appbar.dart';
import '../../../../core/widgets/card/propertycard.dart';
import '../../../../core/utils/formatcurrency.dart';
import '../../../../core/utils/app_logger.dart';
import '../../provider/property_provider.dart';
import '../../model/properties_model.dart';

/// Provider that returns all properties sorted by GPS distance (ascending).
final nearYouAllProvider = FutureProvider<List<PropertyWithDistance>>((ref) async {
  final allProperties = await ref.watch(propertiesProvider('').future);

  Position? userPosition;
  try {
    final permission = await Geolocator.checkPermission();
    if (permission == LocationPermission.whileInUse ||
        permission == LocationPermission.always) {
      userPosition = await Geolocator.getLastKnownPosition();
      userPosition ??= await Geolocator.getCurrentPosition(
        locationSettings: const LocationSettings(
          accuracy: LocationAccuracy.low,
          timeLimit: Duration(seconds: 5),
        ),
      );
    }
  } catch (e) {
    AppLogger.w('GPS check failed: $e', 'NEAR-YOU-PAGE');
  }

  if (userPosition != null) {
    final withDistance = allProperties.map((p) {
      final dist = _calcDistance(userPosition!, p.latitude, p.longitude);
      return PropertyWithDistance(p, dist == double.infinity ? null : dist);
    }).toList()
      ..sort((a, b) =>
          (a.distanceKm ?? double.infinity).compareTo(b.distanceKm ?? double.infinity));
    return withDistance;
  } else {
    // Fallback: sort by city name, no distance
    final sorted = List<PropertyModel>.from(allProperties)
      ..sort((a, b) => a.city.toLowerCase().compareTo(b.city.toLowerCase()));
    return sorted.map((p) => PropertyWithDistance(p, null)).toList();
  }
});

/// Haversine distance calculation
double _calcDistance(Position user, double? lat, double? lng) {
  if (lat == null || lng == null) return double.infinity;
  const earthRadius = 6371.0;
  final dLat = _toRad(lat - user.latitude);
  final dLng = _toRad(lng - user.longitude);
  final a = sin(dLat / 2) * sin(dLat / 2) +
      cos(_toRad(user.latitude)) * cos(_toRad(lat)) *
      sin(dLng / 2) * sin(dLng / 2);
  final c = 2 * atan2(sqrt(a), sqrt(1 - a));
  return earthRadius * c;
}

double _toRad(double deg) => deg * pi / 180;

/// Page showing all properties sorted by distance from the user.
class NearYouPage extends ConsumerWidget {
  const NearYouPage({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;
    final nearYouAsync = ref.watch(nearYouAllProvider);

    return MainLayout(
      currentIndex: 0,
      showNavBar: false,
      showBottomNav: true,
      showContactBar: false,
      child: Scaffold(
        backgroundColor: Colors.transparent,
        extendBodyBehindAppBar: true,
        appBar: CustomAppBar(
          title: localizations.budget,
        ),
        body: SafeArea(
          top: false,
          child: nearYouAsync.when(
            data: (propertiesWithDistance) {
              if (propertiesWithDistance.isEmpty) {
                return Center(
                  child: Text(
                    localizations.roomTypeNoRoomsAvailable,
                    style: const TextStyle(fontSize: 16, color: Colors.grey),
                  ),
                );
              }

              return ListView.builder(
                padding: EdgeInsets.fromLTRB(
                  16, MediaQuery.of(context).padding.top + kToolbarHeight + 16, 16, 32,
                ),
                itemCount: propertiesWithDistance.length,
                itemBuilder: (context, index) {
                  final pwd = propertiesWithDistance[index];
                  final property = pwd.property;

                  // Format distance label
                  String? distanceLabel;
                  if (pwd.distanceKm != null) {
                    distanceLabel = '${pwd.distanceKm!.toStringAsFixed(1)} km';
                  }

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
                      // Distance badge shown at top-left of property photo
                      detail: distanceLabel,
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
