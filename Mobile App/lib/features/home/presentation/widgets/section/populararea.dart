import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/core/widgets/card/propertycard.dart';
import 'package:ulinmahoniapps/features/searchresult/provider/searchresult_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';

class AreaPopularSection extends ConsumerWidget {
  final Color? backgroundColor;
  final String? sectionTitle;

  const AreaPopularSection({
    Key? key,
    this.backgroundColor,
    this.sectionTitle,
  }) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;

    List<Map<String, String>> popularAreasData = [
      {
        "label": localizations.popularAreaJakarta,
        "name": "DKI Jakarta",
        "imageUrl": AppImage.jakartaUrl,
        "detail": localizations.detailJakarta,
        "price": ""
      },
      {
        "label": localizations.popularAreaBogor,
        "name": "Bogor",
        "imageUrl": AppImage.bogorUrl,
        "detail": localizations.detailBogor,
        "price": ""
      },
    ];

    return Container(
      color: backgroundColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 20),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: Text(
              localizations.popularArea,
              style: const TextStyle(fontSize: 23, fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(height: 20),
          popularAreasData.isEmpty
              ? const Center(
            child: Padding(
              padding: EdgeInsets.all(20.0),
              child: Text(
                'Tidak ada area populer ditemukan.',
                style: TextStyle(color: Colors.grey),
              ),
            ),
          )
              : SizedBox(
            height: MediaQuery.of(context).size.height * 0.25,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: popularAreasData.length,
              itemBuilder: (context, index) {
                final area = popularAreasData[index];
                return Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    PropertyCard(
                      image: area['imageUrl']!,
                      title: area['name']!,
                      detail: area['detail']!,
                      onTap: () {
                        ref.read(searchFilterProvider.notifier).resetFilter();
                        ref.read(searchFilterProvider.notifier).updateFilterPartial(province: area['name']!.toLowerCase());
                        context.push('/search');
                        AppLogger.d('Navigating to /search with category filter: ${area['name']!.toLowerCase()}', 'POPULARAREA');
                      },
                    ),
                    if (index < popularAreasData.length - 1)
                      const SizedBox(width: 16),
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}