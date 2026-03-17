import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';

class Filtertype extends StatefulWidget {
  final String selectedLabel;
  final ValueChanged<String> onTabSelected;
  final Color activeColor;
  final Color inactiveColor;

  const Filtertype({
    Key? key,
    required this.selectedLabel,
    required this.onTabSelected,
    this.activeColor = Colors.white,
    this.inactiveColor = Colors.black,
  }) : super(key: key);

  @override
  State<Filtertype> createState() => _FiltertypeState();
}

class _FiltertypeState extends State<Filtertype> {
  @override
  Widget build(BuildContext context) {
    
    final localizations = AppLocalizations.of(context)!;

    
    final List<Map<String, dynamic>> filtertypedata = [
      {'label': localizations.filterCategoryKos, 'value': 'Kos', 'icon': Icons.home},
      {'label': localizations.filterCategoryApartment, 'value': 'Apartment', 'icon': Icons.apartment},
      {'label': localizations.filterCategoryHotel, 'value': 'Hotel', 'icon': Icons.hotel},
      {'label': localizations.filterCategoryVilla, 'value': 'Villa', 'icon': Icons.villa},
    ];

    return Container(
      height: 48,
      width: double.infinity,
      color: AppColors.backgroundColor,
      padding: const EdgeInsets.symmetric(vertical: 4, horizontal: 16),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: filtertypedata.map((tab) {
          final isActive = widget.selectedLabel.toLowerCase() == tab['value'].toLowerCase();

          return Expanded(
            child: GestureDetector(
              onTap: () => widget.onTabSelected(tab['value'].toString()),
              child: Container(
                margin: const EdgeInsets.symmetric(horizontal: 3),
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 8),
                decoration: BoxDecoration(
                  color: isActive ? AppColors.primaryColor : Colors.transparent,
                  borderRadius: BorderRadius.circular(24),
                ),
                child: Center(
                  child: Text(
                    tab['label'],
                    style: TextStyle(
                      color: isActive ? widget.activeColor : widget.inactiveColor,
                      fontWeight: isActive ? FontWeight.w600 : FontWeight.w300,
                      fontSize: 13,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ),
            ),
          );
        }).toList(),
      ),
    );
  }
}