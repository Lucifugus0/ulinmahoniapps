import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';

class CategoriesSection extends StatelessWidget {
  final String selectedLabel;
  final ValueChanged<String> onCategorySelected;

  const CategoriesSection({
    Key? key,
    required this.selectedLabel,
    required this.onCategorySelected,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;

    final List<Map<String, dynamic>> categories = [
      {
        'label': localizations.filterCategoryApartment,
        'value': 'Apartment',
        'emoji': '🏢',
      },
      {
        'label': localizations.filterCategoryKos,
        'value': 'Kos',
        'emoji': '🏠',
      },
      {
        'label': localizations.filterCategoryVilla,
        'value': 'Villa',
        'emoji': '🏡',
      },
      {
        'label': localizations.filterCategoryHotel,
        'value': 'Hotel',
        'emoji': '🏛️',
      },
    ];

    return Container(
      padding: const EdgeInsets.symmetric(vertical: 8),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.only(left: 16, bottom: 12),
            child: Row(
              children: [
                Text(
                  '🏷️ ',
                  style: TextStyle(fontSize: 20),
                ),
                Text(
                  localizations.categories,
                  style: TextStyle(
                    fontSize: 20,
                    fontWeight: FontWeight.bold,
                    color: Colors.black87,
                  ),
                ),
              ],
            ),
          ),
          Padding(
            padding: const EdgeInsets.only(top: 8, bottom: 12),
            child: SingleChildScrollView(
              scrollDirection: Axis.horizontal,
              child: Row(
                children: categories.asMap().entries.map((entry) {
                  final index = entry.key;
                  final category = entry.value;
                  final isActive = selectedLabel.toLowerCase() == category['value'].toLowerCase();
                  return Padding(
                    padding: EdgeInsets.only(
                      left: index == 0 ? 16 : 0,
                      right: index < categories.length - 1 ? 12 : 16,
                    ),
                    child: _PillButton(
                      label: category['label'],
                      emoji: category['emoji'],
                      isActive: isActive,
                      onTap: () => onCategorySelected(category['value'].toString()),
                    ),
                  );
                }).toList(),
              ),
            ),
          ),
        ],
      ),
    );
  }
}

/// Pill-shaped button for categories
class _PillButton extends StatelessWidget {
  final String label;
  final String emoji;
  final bool isActive;
  final VoidCallback onTap;

  const _PillButton({
    required this.label,
    required this.emoji,
    required this.isActive,
    required this.onTap,
  });

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: AnimatedContainer(
        duration: const Duration(milliseconds: 200),
        margin: const EdgeInsets.symmetric(vertical: 4),
        padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
        decoration: BoxDecoration(
          color: isActive ? AppColors.secondaryColor : Colors.grey.shade100,
          borderRadius: BorderRadius.circular(18),
        ),
        child: Row(
          mainAxisSize: MainAxisSize.min,
          children: [
            Text(
              emoji,
              style: const TextStyle(fontSize: 18),
            ),
            const SizedBox(width: 6),
            Text(
              label,
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w500,
                color: isActive ? Colors.white : Colors.black87,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
