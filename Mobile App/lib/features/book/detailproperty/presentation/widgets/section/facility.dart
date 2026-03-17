import 'package:flutter/material.dart';

class PropertyFacilitiesTextGrid extends StatelessWidget {

  // Terima 3 list terpisah sesuai model baru
  final List<String>? general;
  final List<String>? security;
  final List<String>? amenities;

  const PropertyFacilitiesTextGrid({
    Key? key,
    this.general,
    this.security,
    this.amenities,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {

    // 1. GABUNGKAN DATA:
    // Menggunakan spread operator (...?) untuk menggabungkan list
    // dan secara otomatis melewati list yang null.
    final List<String> allFacilities = [
      ...?general,
      ...?security,
      ...?amenities,
    ];

    // Cek jika gabungan kosong
    if (allFacilities.isEmpty) {
      return const SizedBox.shrink();
    }

    const int crossAxisCount = 2;
    final double mainAxisSpacing = 16.0;
    final double crossAxisSpacing = 16.0;
    const double childAspectRatio = 4.5;

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        crossAxisSpacing: crossAxisSpacing,
        mainAxisSpacing: mainAxisSpacing,
        childAspectRatio: childAspectRatio,
      ),
      itemCount: allFacilities.length, // Gunakan jumlah total gabungan
      itemBuilder: (context, index) {
        final featureName = allFacilities[index];

        return Align(
          alignment: Alignment.centerLeft,
          child: Row(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '• ',
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  fontSize: 15,
                  fontWeight: FontWeight.bold,
                ),
              ),
              Flexible(
                child: Text(
                  featureName,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontSize: 15, // Consistent font size
                  ),
                  softWrap: true,
                  overflow: TextOverflow.ellipsis,
                  maxLines: 2,
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}