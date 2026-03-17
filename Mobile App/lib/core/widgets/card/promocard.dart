import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:skeletonizer/skeletonizer.dart';

/// Promo Banner Card Widget
/// Displays promotional banner image in 16:9 aspect ratio
/// Full-width with 16px padding, image-only (no text overlay)
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
    return GestureDetector(
      onTap: onTap,
      child: Container(
        decoration: BoxDecoration(
          borderRadius: borderRadius ?? BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: ClipRRect(
          borderRadius: borderRadius ?? BorderRadius.circular(12),
          child: imageUrl.isEmpty
              ? _buildPlaceholder()
              : CachedNetworkImage(
                  imageUrl: imageUrl,
                  fit: BoxFit.cover,
                  placeholder: (context, url) => _buildPlaceholder(),
                  errorWidget: (context, url, error) => _buildErrorWidget(),
                ),
        ),
      ),
    );
  }

  /// Placeholder widget while loading
  Widget _buildPlaceholder() {
    return Skeletonizer(
      enabled: true,
      child: Container(
        color: Colors.grey[300],
        child: const Center(
          child: Icon(
            Icons.image,
            size: 48,
            color: Colors.grey,
          ),
        ),
      ),
    );
  }

  /// Error widget if image fails to load
  Widget _buildErrorWidget() {
    return Container(
      color: Colors.grey[200],
      child: const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.broken_image,
              size: 48,
              color: Colors.grey,
            ),
            SizedBox(height: 8),
            Text(
              'Failed to load image',
              style: TextStyle(
                color: Colors.grey,
                fontSize: 12,
              ),
            ),
          ],
        ),
      ),
    );
  }
}
