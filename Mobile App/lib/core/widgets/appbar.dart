import 'package:flutter/material.dart';
import 'button/backbutton.dart';
import '../constants/appcolor_constants.dart';
import '../constants/appfontweight_constants.dart';
import '../utils/formatdate.dart';

class CustomAppBar extends StatelessWidget implements PreferredSizeWidget {
  final String title;
  final String? subtitle;
  final Color backgroundColor;
  final Color textColor;
  final Color backButtonColor;
  final bool showBackButton;
  final String? redirectRoute;
  final bool isSearch;
  final String? category;
  final String? rentalType;
  final int? duration;
  final String? checkInDate;
  final String? checkOutDate;
  final String? province;
  final double? customToolbarHeight;
  final double? customSearchDetailsHeight;

  const CustomAppBar({
    Key? key,
    this.title = 'Default',
    this.subtitle,
    this.backgroundColor = AppColors.primaryColor,
    this.textColor = AppColors.white,
    this.backButtonColor = Colors.white,
    this.showBackButton = true,
    this.redirectRoute,
    this.isSearch = false,
    this.category,
    this.rentalType,
    this.duration,
    this.checkInDate,
    this.checkOutDate,
    this.province,
    this.customToolbarHeight,
    this.customSearchDetailsHeight,
  }) : super(key: key);

  @override
  Size get preferredSize {
    final double baseHeight = customToolbarHeight ?? kToolbarHeight;
    double searchHeight = 0;

    
    if (isSearch &&
        (category != null && category!.isNotEmpty ||
            rentalType != null && rentalType!.isNotEmpty ||
            duration != null && duration! > 0 ||
            checkInDate != null && checkInDate!.isNotEmpty ||
            checkOutDate != null && checkOutDate!.isNotEmpty ||
            province != null && province!.isNotEmpty 
        )) {
      searchHeight = customSearchDetailsHeight ?? (kToolbarHeight * 2.5);
    }
    return Size.fromHeight(baseHeight + searchHeight);
  }

  
  Widget _buildFilterChip(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 5, vertical: 3), 
      margin: const EdgeInsets.only(right: 5, bottom: 5), 
      decoration: BoxDecoration(
        color: color.withValues(alpha: 0.1),
        borderRadius: BorderRadius.circular(20),
        border: Border.all(color: color),
      ),
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontSize: 10, 
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  
  Widget _buildSimpleText(String label, {Color color = AppColors.white, double fontSize = 12}) {
    return Padding(
      padding: const EdgeInsets.only(right: 8.0, bottom: 5), 
      child: Text(
        label,
        style: TextStyle(
          color: color,
          fontSize: fontSize,
          fontWeight: FontWeight.w500,
        ),
      ),
    );
  }

  
  String _capitalizeEachWord(String text) {
    if (text.isEmpty) return text;
    return text.split(' ').map((word) {
      if (word.isEmpty) return '';
      return word[0].toUpperCase() + word.substring(1).toLowerCase();
    }).join(' ');
  }

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: backgroundColor,
      elevation: 0,
      toolbarHeight: preferredSize.height, 
      leading: showBackButton
          ? CustomBackButton(
        iconColor: backButtonColor,
        redirectRoute: redirectRoute,
        isInAppBar: true,
      )
          : null,
      title: Align(
        alignment: Alignment.centerLeft,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Text(
              title,
              style: TextStyle(
                fontSize: 20,
                fontWeight: AppFontWeight.semiBold,
                color: textColor,
              ),
              textAlign: TextAlign.left,
            ),
            // Add subtitle if provided
            if (subtitle != null && subtitle!.isNotEmpty)
              Padding(
                padding: const EdgeInsets.only(top: 2.0),
                child: Text(
                  subtitle!,
                  style: TextStyle(
                    fontSize: 12,
                    fontWeight: FontWeight.w400,
                    color: textColor.withValues(alpha: 0.85),
                  ),
                  textAlign: TextAlign.left,
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                ),
              ),

          if (isSearch &&
              (category != null && category!.isNotEmpty ||
                  rentalType != null && rentalType!.isNotEmpty ||
                  duration != null && duration! > 0 ||
                  checkInDate != null && checkInDate!.isNotEmpty ||
                  checkOutDate != null && checkOutDate!.isNotEmpty ||
                  province != null && province!.isNotEmpty 
              ))
          if (isSearch &&
              (category != null && category!.isNotEmpty ||
                  rentalType != null && rentalType!.isNotEmpty ||
                  duration != null && duration! > 0 ||
                  checkInDate != null && checkInDate!.isNotEmpty ||
                  checkOutDate != null && checkOutDate!.isNotEmpty ||
                  province != null && province!.isNotEmpty 
              ))
            Padding( 
              padding: const EdgeInsets.only(top: 4.0),
              child: Wrap( 
                spacing: 8.0, 
                runSpacing: 4.0, 
                children: [
                  
                  if (rentalType != null && rentalType!.isNotEmpty)
                    _buildSimpleText(rentalType!),
                  
                  if (category != null && category!.isNotEmpty)
                    _buildSimpleText(category!),
                  
                  if (duration != null && duration! > 0)
                    _buildSimpleText('$duration ${rentalType == 'Monthly' ? 'Bulan' : 'Hari'}'),
                  
                  if (checkInDate != null && checkInDate!.isNotEmpty)
                    _buildSimpleText('CheckIn : ${formatDate(checkInDate)}'),
                  
                  if (checkOutDate != null && checkOutDate!.isNotEmpty)
                    _buildSimpleText('CheckOut : ${formatDate(checkOutDate)}'),
                  
                  if (province != null && province!.isNotEmpty)
                    _buildSimpleText(_capitalizeEachWord(province!)),
                ],
              ),
            ),
          ],
        ),
      ),
      centerTitle: false,
      /* When back button is shown, remove the extra gap so title sits flush after it.
         When no back button, keep default 16px left margin for breathing room. */
      titleSpacing: showBackButton ? 0 : 16.0,
    );
  }
}