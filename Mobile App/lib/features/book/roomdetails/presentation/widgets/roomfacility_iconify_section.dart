import 'package:flutter/material.dart';
import '../../model/facility_model.dart';
import '../../../../../core/utils/facility_icon_mapper.dart';

/// Widget untuk menampilkan facilities dengan icon
/// Mendukung format baru dengan icon field yang di-mapping ke Material Icons
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

    const int crossAxisCount = 2;
    final double mainAxisSpacing = 16.0;
    final double crossAxisSpacing = 16.0;
    const double childAspectRatio = 4.5; // Increased height for better visibility

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        crossAxisSpacing: crossAxisSpacing,
        mainAxisSpacing: mainAxisSpacing,
        childAspectRatio: childAspectRatio,
      ),
      itemCount: facilities.length,
      itemBuilder: (context, index) {
        final facility = facilities[index];

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
                size: 24, // Increased from 20 to 24
                color: const Color(0xFF134E3A), // Dark green for all icons
              ),
              const SizedBox(width: 10), // Increased from 8 to 10

              // Facility name
              Flexible(
                child: Text(
                  facility.name,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontSize: 15, // Explicitly set font size to 15
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
