import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';

/// Payment method list item with icon (local asset or network), title, subtitle.
class PaymentMethodItem extends StatelessWidget {
  final String? iconAsset;  // Local asset path (preferred)
  final String? iconUrl;    // Network URL (fallback)
  final String title;
  final String? subtitle;
  final bool isSelected;
  final VoidCallback? onTap;

  const PaymentMethodItem({
    Key? key,
    this.iconAsset,
    this.iconUrl,
    required this.title,
    this.subtitle = '',
    this.isSelected = false,
    this.onTap,
  }) : super(key: key);

  Widget _buildIcon() {
    // Prefer local asset over network URL
    if (iconAsset != null && iconAsset!.isNotEmpty) {
      return Image.asset(
        iconAsset!,
        width: 60,
        height: 60,
        fit: BoxFit.contain,
        errorBuilder: (context, error, stackTrace) =>
            const Center(child: Icon(Icons.payment, size: 32)),
      );
    }
    if (iconUrl != null && iconUrl!.isNotEmpty) {
      return Image.network(
        iconUrl!,
        width: 60,
        height: 60,
        fit: BoxFit.contain,
        errorBuilder: (context, error, stackTrace) =>
            const Center(child: Icon(Icons.payment, size: 32)),
      );
    }
    return const Center(child: Icon(Icons.payment, size: 32));
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12.0, horizontal: 8.0),
        margin: const EdgeInsets.symmetric(vertical: 4.0),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            Icon(
              isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
              color: isSelected ? AppColors.primaryAdaptive(context) : Colors.grey,
            ),
            const SizedBox(width: 8),
            // Icon with white background for visibility in dark mode
            Container(
              width: 60,
              height: 60,
              decoration: BoxDecoration(
                color: isDark ? Colors.white : Colors.transparent,
                borderRadius: BorderRadius.circular(8),
              ),
              padding: isDark ? const EdgeInsets.all(4) : EdgeInsets.zero,
              child: _buildIcon(),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      color: isSelected
                          ? AppColors.primaryAdaptive(context)
                          : (isDark ? Colors.white : Colors.black87),
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (subtitle != null && subtitle!.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 2.0),
                      child: Text(
                        subtitle!,
                        style: TextStyle(
                          fontWeight: FontWeight.w600,
                          color: isDark ? Colors.grey[300] : Colors.black54,
                          fontSize: 15,
                        ),
                      ),
                    ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
