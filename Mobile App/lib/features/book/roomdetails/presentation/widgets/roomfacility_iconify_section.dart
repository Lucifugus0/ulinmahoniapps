import 'package:flutter/material.dart';
import '../../model/facility_model.dart';
import '../../../../../core/utils/facility_icon_mapper.dart';

/// Room facilities with icons in a 2-column layout.
/// Uses Column+Row instead of GridView to avoid excess vertical whitespace.
class RoomFacilitiesIconifyGrid extends StatelessWidget {
  final List<FacilityModel> facilities;

  const RoomFacilitiesIconifyGrid({
    super.key,
    required this.facilities,
  });

  @override
  Widget build(BuildContext context) {
    if (facilities.isEmpty) {
      return const SizedBox.shrink();
    }

    final List<Widget> rows = [];
    for (int i = 0; i < facilities.length; i += 2) {
      rows.add(Row(
        children: [
          Expanded(child: _buildItem(context, facilities[i])),
          if (i + 1 < facilities.length)
            Expanded(child: _buildItem(context, facilities[i + 1]))
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
