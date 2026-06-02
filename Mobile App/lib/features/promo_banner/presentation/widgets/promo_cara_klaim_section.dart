import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../l10n/app_localizations.dart';
import '../../model/promo_banner_model.dart';

/// Widget to display "How to Claim" steps — dark/light mode aware with translations
class PromoCaraKlaimSection extends StatelessWidget {
  final PromoBannerModel banner;

  const PromoCaraKlaimSection({
    super.key,
    required this.banner,
  });

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final localizations = AppLocalizations.of(context)!;

    // Default steps as fallback
    final defaultSteps = [
      localizations.promoClaimStep1,
      localizations.promoClaimStep2,
      localizations.promoClaimStep3,
      localizations.promoClaimStep4,
      localizations.promoClaimStep5,
    ];

    final List<String> caraKlaim =
        (banner.howToClaim != null && banner.howToClaim!.isNotEmpty)
            ? banner.howToClaim!
            : defaultSteps;

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: isDark ? AppColors.surfaceDark : AppColors.backgroundColor,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isDark ? Colors.grey[700]! : Colors.grey[300]!,
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            localizations.promoHowToClaim,
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 16),
          ...List.generate(caraKlaim.length, (index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 24,
                    height: 24,
                    decoration: BoxDecoration(
                      color: AppColors.primaryAdaptive(context),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Text(
                        '${index + 1}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: AppFontWeight.semiBold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      caraKlaim[index],
                      style: TextStyle(
                        fontSize: 14,
                        color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }
}
