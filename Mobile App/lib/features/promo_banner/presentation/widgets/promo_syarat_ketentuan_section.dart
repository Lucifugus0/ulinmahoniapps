import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../l10n/app_localizations.dart';

/// Widget to display "Terms & Conditions" — dark/light mode aware with translations
class PromoSyaratKetentuanSection extends StatelessWidget {
  const PromoSyaratKetentuanSection({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final localizations = AppLocalizations.of(context)!;

    final terms = [
      localizations.promoTerm1,
      localizations.promoTerm2,
      localizations.promoTerm3,
      localizations.promoTerm4,
    ];

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
            localizations.promoTermsTitle,
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 16),
          ...List.generate(terms.length, (index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Icon(
                    Icons.check,
                    color: AppColors.primaryAdaptive(context),
                    size: 20,
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: Text(
                      terms[index],
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
