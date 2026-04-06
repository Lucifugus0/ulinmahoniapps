import 'package:flutter/material.dart';
import '../../../../roomdetails/model/facility_model.dart';
import '../../../../../../core/utils/facility_icon_mapper.dart';

/// Widget untuk menampilkan property facilities dengan icon in a 2-column layout.
/// Uses Column+Row instead of GridView to avoid excess vertical whitespace.
class PropertyFacilitiesIconifyGrid extends StatelessWidget {
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
    final List<FacilityModel> allFacilities = [
      ...?general,
      ...?security,
      ...?amenities,
    ];

    if (allFacilities.isEmpty) {
      return const SizedBox.shrink();
    }

    // Build rows of 2 items each
    final List<Widget> rows = [];
    for (int i = 0; i < allFacilities.length; i += 2) {
      rows.add(Row(
        children: [
          Expanded(child: _buildItem(context, allFacilities[i])),
          if (i + 1 < allFacilities.length)
            Expanded(child: _buildItem(context, allFacilities[i + 1]))
          else
            const Expanded(child: SizedBox.shrink()),
        ],
      ));
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: rows,
    );
  }

  Widget _buildItem(BuildContext context, FacilityModel facility) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Icon(
            facility.icon.isNotEmpty
                ? FacilityIconMapper.getIcon(facility.icon)
                : Icons.check_circle_outline,
            size: 24,
            color: const Color(0xFF134E3A),
          ),
          const SizedBox(width: 10),
          // Multi-language: show facility name in app's current locale
          Flexible(
            child: Text(
              facility.getLocalizedName(Localizations.localeOf(context).languageCode),
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(fontSize: 15),
              overflow: TextOverflow.ellipsis,
              maxLines: 2,
            ),
          ),
        ],
      ),
    );
  }
}
