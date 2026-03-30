import 'package:flutter/material.dart';
import '../../../../roomdetails/model/facility_model.dart';
import '../../../../../../core/utils/facility_icon_mapper.dart';

/// Widget untuk menampilkan property facilities dengan icon
/// Mendukung format baru dengan icon field yang di-mapping ke Material Icons
class PropertyFacilitiesIconifyGrid extends StatelessWidget {
  // Terima 3 list terpisah sesuai model baru
  final List<FacilityModel>? general;
  final List<FacilityModel>? security;
  final List<FacilityModel>? amenities;

  const PropertyFacilitiesIconifyGrid({
    super.key,
    this.general,
    this.security,
    this.amenities,
  });

  @override
  Widget build(BuildContext context) {
    // Gabungkan semua facilities
    final List<FacilityModel> allFacilities = [
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
    const double childAspectRatio = 4.5; // Same as room details for consistency

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        crossAxisSpacing: crossAxisSpacing,
        mainAxisSpacing: mainAxisSpacing,
        childAspectRatio: childAspectRatio,
      ),
      itemCount: allFacilities.length,
      itemBuilder: (context, index) {
        final facility = allFacilities[index];

        return Align(
          alignment: Alignment.centerLeft,
          child: Row(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              // Icon mapped from string
              Icon(
                facility.icon.isNotEmpty
                    ? FacilityIconMapper.getIcon(facility.icon)
                    : Icons.check_circle_outline,
                size: 24, // Same as room details
                color: const Color(0xFF134E3A), // Dark green for all icons
              ),
              const SizedBox(width: 10),

              // <!-- Multi-language: show facility name in app's current locale -->
              Flexible(
                child: Text(
                  facility.getLocalizedName(Localizations.localeOf(context).languageCode),
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontSize: 15, // Consistent with room details
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
