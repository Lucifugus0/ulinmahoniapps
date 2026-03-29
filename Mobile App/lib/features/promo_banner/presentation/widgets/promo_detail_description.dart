import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../l10n/app_localizations.dart';
import '../../model/promo_banner_model.dart';

/// Widget to display promo description — dark/light mode aware
class PromoDetailDescription extends StatelessWidget {
  final PromoBannerModel banner;

  const PromoDetailDescription({
    super.key,
    required this.banner,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final localizations = AppLocalizations.of(context)!;

    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      color: isDark ? AppColors.backgroundDark : AppColors.backgroundColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            localizations.promoDescription,
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 12),
          Text(
            banner.description != null && banner.description!.isNotEmpty
                ? banner.description!
                : localizations.noDescription,
            style: TextStyle(
              fontSize: 14,
              color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
              height: 1.5,
            ),
          ),
        ],
      ),
    );
  }
}
