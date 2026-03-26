import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';

class PaymentMethodItem extends StatefulWidget {
  final String iconUrl;
  final String title; 
  final String? subtitle; 
  final bool isSelected;
  final VoidCallback? onTap;

  const PaymentMethodItem({
    Key? key,
    required this.iconUrl,
    required this.title, 
    this.subtitle = '', 
    this.isSelected = false,
    this.onTap,
  }) : super(key: key);

  @override
  _PaymentMethodItemState createState() => _PaymentMethodItemState();
}

class _PaymentMethodItemState extends State<PaymentMethodItem> {
  Widget _buildIcon() {
    final isSvg = widget.iconUrl.toLowerCase().endsWith('.svg');

    if (isSvg) {
      // Load SVG
      return SvgPicture.network(
        widget.iconUrl,
        width: 60,
        height: 60,
        fit: BoxFit.contain,
        placeholderBuilder: (BuildContext context) => const Center(
          child: SizedBox(
            width: 24,
            height: 24,
            child: CircularProgressIndicator(strokeWidth: 2),
          ),
        ),
        errorBuilder: (context, error, stackTrace) =>
            const Center(child: Icon(Icons.payment, size: 32)),
      );
    } else {
      // Load PNG/JPG
      return Image.network(
        widget.iconUrl,
        width: 60,
        height: 60,
        fit: BoxFit.contain,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return const Center(
            child: SizedBox(
              width: 24,
              height: 24,
              child: CircularProgressIndicator(strokeWidth: 2),
            ),
          );
        },
        errorBuilder: (context, error, stackTrace) =>
            const Center(child: Icon(Icons.payment, size: 32)),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    // Dark mode detection for payment method text colors
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return GestureDetector(
      onTap: widget.onTap,
      child: Container(
        padding: const EdgeInsets.symmetric(vertical: 12.0, horizontal: 8.0),
        margin: const EdgeInsets.symmetric(vertical: 4.0),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // Radio button icon (MOVED TO LEFT)
            Icon(
              widget.isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
              // Use primaryAdaptive for the radio button icon color when selected
            color: widget.isSelected ? AppColors.primaryAdaptive(context) : Colors.grey,
            ),
            const SizedBox(width: 8),

            // Icon with fixed size container to prevent overflow
            Container(
              width: 60,
              height: 60,
              child: _buildIcon(),
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Text(
                    widget.title,
                    style: TextStyle(
                      fontWeight:
                      widget.isSelected ? FontWeight.bold : FontWeight.normal,
                      // Use primaryAdaptive for the payment method title color when selected
                      color: widget.isSelected
                          ? AppColors.primaryAdaptive(context)
                          : (isDark ? Colors.white : Colors.black87),
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  if (widget.subtitle != null && widget.subtitle!.isNotEmpty)
                    Padding(
                      padding: const EdgeInsets.only(top: 2.0),
                      child: Text(
                        widget.subtitle!,
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