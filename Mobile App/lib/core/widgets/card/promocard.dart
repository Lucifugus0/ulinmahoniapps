import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../theme/glass_theme.dart';

/// Promo Banner Card Widget with glass-style border.
/// Displays promotional banner image in 16:9 aspect ratio.
/// Border and shadow adapt to dark/light mode.
class PromoCard extends StatelessWidget {
  final String imageUrl;
  final VoidCallback? onTap;
  final double? width;
  final BorderRadius? borderRadius;

  const PromoCard({
    Key? key,
    required this.imageUrl,
    this.onTap,
    this.width,
    this.borderRadius,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final effectiveBorderRadius = borderRadius ?? BorderRadius.circular(GlassTheme.radiusMedium);

    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          borderRadius: effectiveBorderRadius,
          // Glass-style subtle border
          border: Border.all(
            color: isDark ? GlassTheme.glassBorderDark : GlassTheme.glassBorderLight,
            width: GlassTheme.borderWidth,
          ),
          boxShadow: isDark ? GlassTheme.glassShadowDark : GlassTheme.glassShadowLight,
        ),
        child: ClipRRect(
          borderRadius: effectiveBorderRadius,
          child: imageUrl.isEmpty
              ? _buildPlaceholder(isDark)
              : CachedNetworkImage(
                  imageUrl: imageUrl,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => _buildPlaceholder(isDark),
                  errorWidget: (context, url, error) => _buildErrorWidget(isDark),
                ),
        ),
      ),
    );
  }

  /// Placeholder widget while loading (adapts to dark/light)
  Widget _buildPlaceholder(bool isDark) {
    return Skeletonizer(
      enabled: true,
      child: Container(
        color: isDark ? Colors.grey[800] : Colors.grey[300],
        child: Center(
          child: Icon(
            Icons.image,
            size: 48,
            color: isDark ? Colors.grey[600] : Colors.grey,
          ),
        ),
      ),
    );
  }

  /// Error widget if image fails to load (adapts to dark/light)
  Widget _buildErrorWidget(bool isDark) {
    return Container(
      color: isDark ? Colors.grey[850] : Colors.grey[200],
      child: Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.broken_image,
              size: 48,
              color: isDark ? Colors.grey[600] : Colors.grey,
            ),
            const SizedBox(height: 8),
            Text(
              'Failed to load image',
              style: TextStyle(
                color: isDark ? Colors.grey[500] : Colors.grey,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
