import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../l10n/app_localizations.dart';
import '../../model/promo_banner_model.dart';

/// Widget to display promo code (with copy button) and description — dark/light mode aware
class PromoDetailDescription extends StatefulWidget {
  final PromoBannerModel banner;

  const PromoDetailDescription({
    super.key,
    required this.banner,
  });

  @override
  State<PromoDetailDescription> createState() => _PromoDetailDescriptionState();
}

class _PromoDetailDescriptionState extends State<PromoDetailDescription> {
  bool _copied = false;

  void _copyCode() {
    if (widget.banner.promoCode == null) return;
    Clipboard.setData(ClipboardData(text: widget.banner.promoCode!));
    setState(() => _copied = true);
    ScaffoldMessenger.of(context).showSnackBar(
      const SnackBar(
        content: Text('Promo code copied!'),
        duration: Duration(seconds: 2),
        backgroundColor: Colors.green,
      ),
    );
    Future.delayed(const Duration(seconds: 2), () {
      if (mounted) setState(() => _copied = false);
    });
  }

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
          // Promo Code section — bigger, stands out, with copy button
          if (widget.banner.promoCode != null && widget.banner.promoCode!.isNotEmpty) ...[
            Text(
              localizations.promoCodeLabel,
              style: TextStyle(
                fontSize: 18,
                fontWeight: AppFontWeight.bold,
                color: isDark ? AppColors.fontColorDark : AppColors.fontcolor,
              ),
            ),
            const SizedBox(height: 10),
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
              decoration: BoxDecoration(
                color: isDark ? const Color(0xFF0D3B3B) : const Color(0xFFF0FDFA),
                borderRadius: BorderRadius.circular(12),
                border: Border.all(
                  color: isDark ? Colors.teal.shade700 : Colors.teal.shade300,
                  width: 2,
                  // Dashed effect simulated via solid border
                ),
              ),
              child: Row(
                children: [
                  Expanded(
                    child: Text(
                      widget.banner.promoCode!,
                      style: TextStyle(
                        fontSize: 22,
                        fontWeight: FontWeight.w800,
                        letterSpacing: 2,
                        color: isDark ? Colors.teal.shade300 : Colors.teal.shade700,
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  SizedBox(
                    height: 36,
                    child: ElevatedButton.icon(
                      onPressed: _copyCode,
                      icon: Icon(
                        _copied ? Icons.check : Icons.copy,
                        size: 16,
                      ),
                      label: Text(
                        _copied ? 'Copied!' : 'Copy',
                        style: const TextStyle(fontSize: 13, fontWeight: FontWeight.w600),
                      ),
                      style: ElevatedButton.styleFrom(
                        backgroundColor: _copied ? Colors.green : Colors.teal.shade600,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(horizontal: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),
          ],

          // Description section
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
            widget.banner.description != null && widget.banner.description!.isNotEmpty
                ? widget.banner.description!
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
