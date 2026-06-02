import 'dart:async';
import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:smooth_page_indicator/smooth_page_indicator.dart';
import '../../../../core/constants/app_asset_constants.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../core/widgets/image_viewer_popup.dart';
import '../../../../core/utils/app_logger.dart';
import '../../model/promo_banner_model.dart';

/// Widget untuk menampilkan banner promo dengan title overlay
/// Support multiple images dengan carousel dan image viewer popup
class PromoDetailBanner extends StatefulWidget {
  final PromoBannerModel banner;

  const PromoDetailBanner({
    super.key,
    required this.banner,
  });

  @override
  State<PromoDetailBanner> createState() => _PromoDetailBannerState();
}

class _PromoDetailBannerState extends State<PromoDetailBanner> {
  late PageController _pageController;
  Timer? _autoScrollTimer;
  int _currentPage = 0;

  @override
  void initState() {
    super.initState();
    _pageController = PageController(viewportFraction: 1.0);
    _startAutoScroll();
  }

  @override
  void dispose() {
    _autoScrollTimer?.cancel();
    _pageController.dispose();
    super.dispose();
  }

  void _startAutoScroll() {
    _autoScrollTimer?.cancel();

    // Only auto-scroll if there are multiple images
    if (_getImageUrls().length <= 1) return;

    _autoScrollTimer = Timer.periodic(const Duration(seconds: 3), (timer) {
      if (_pageController.hasClients) {
        _currentPage = (_currentPage + 1) % _getImageUrls().length;
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 400),
          curve: Curves.easeInOut,
        );
      }
    });
  }

  void _stopAutoScroll() {
    _autoScrollTimer?.cancel();
  }

  /// Get list of image URLs from banner
  List<String> _getImageUrls() {
    final List<String> urls = [];

    AppLogger.d('Banner ID: ${widget.banner.id}', 'PROMO-DETAIL-BANNER');
    AppLogger.d('Banner images count: ${widget.banner.images.length}', 'PROMO-DETAIL-BANNER');
    AppLogger.d('Banner thumbnail: ${widget.banner.thumbnail}', 'PROMO-DETAIL-BANNER');

    // ONLY use images array - ignore root thumbnail
    if (widget.banner.images.isNotEmpty) {
      for (var image in widget.banner.images) {
        AppLogger.d('Image ${image.id}: ${image.imageUrl}', 'PROMO-DETAIL-BANNER');
        if (image.imageUrl.isNotEmpty) {
          urls.add(image.imageUrl);
        }
      }
    }

    // Fallback to thumbnail ONLY if images array is completely empty
    if (urls.isEmpty && widget.banner.thumbnail != null && widget.banner.thumbnail!.isNotEmpty) {
      AppLogger.w('No images in array, falling back to thumbnail', 'PROMO-DETAIL-BANNER');
      urls.add(widget.banner.thumbnail!);
    }

    AppLogger.d('Total image URLs extracted: ${urls.length}', 'PROMO-DETAIL-BANNER');
    for (int i = 0; i < urls.length; i++) {
      AppLogger.d('  [$i]: ${urls[i]}', 'PROMO-DETAIL-BANNER');
    }
    return urls;
  }

  /// Build image providers for viewer popup
  List<ImageProvider> _getImageProviders() {
    final imageUrls = _getImageUrls();
    AppLogger.d('Creating ${imageUrls.length} ImageProviders for popup', 'PROMO-DETAIL-BANNER');
    return imageUrls.map((url) => NetworkImage(url)).toList();
  }

  @override
  Widget build(BuildContext context) {
    final imageUrls = _getImageUrls();
    final hasMultipleImages = imageUrls.length > 1;

    return SizedBox(
      width: double.infinity,
      height: MediaQuery.of(context).size.height * 0.3, // 30% of screen height
      child: Stack(
        children: [
          // Image carousel
          imageUrls.isEmpty
              ? _buildPlaceholder()
              : GestureDetector(
                  onPanDown: (_) => _stopAutoScroll(),
                  onPanEnd: (_) => _startAutoScroll(),
                  onPanCancel: () => _startAutoScroll(),
                  child: PageView.builder(
                    controller: _pageController,
                    onPageChanged: (index) {
                      setState(() {
                        _currentPage = index;
                      });
                    },
                    itemCount: imageUrls.length,
                    itemBuilder: (context, index) {
                      return GestureDetector(
                        behavior: HitTestBehavior.opaque,
                        onTap: () {
                          // Show image viewer popup on tap
                          showImageViewerPopup(
                            context,
                            _getImageProviders(),
                            initialIndex: _currentPage,
                          );
                        },
                        child: CachedNetworkImage(
                          imageUrl: imageUrls[index],
                          fit: BoxFit.cover,
                          placeholder: (context, url) => Skeletonizer(
                            enabled: true,
                            child: Container(
                              color: Colors.grey[300],
                            ),
                          ),
                          errorWidget: (context, url, error) => Image.asset(
                            AppImage.defaultPropertyImage,
                            fit: BoxFit.cover,
                          ),
                        ),
                      );
                    },
                  ),
                ),

          // Gradient overlay (bottom) - ignore pointer events
          Positioned.fill(
            child: IgnorePointer(
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withValues(alpha: 0.7),
                    ],
                  ),
                ),
              ),
            ),
          ),

          // Page indicator (if multiple images) - ignore pointer events
          if (hasMultipleImages)
            Positioned(
              bottom: 60,
              left: 0,
              right: 0,
              child: IgnorePointer(
                child: Center(
                  child: SmoothPageIndicator(
                    controller: _pageController,
                    count: imageUrls.length,
                    effect: ExpandingDotsEffect(
                      dotHeight: 8,
                      dotWidth: 8,
                      // Use primaryAdaptive for dark/light mode compatibility
                      activeDotColor: AppColors.primaryAdaptive(context),
                      dotColor: Colors.white.withValues(alpha: 0.5),
                      expansionFactor: 3,
                      spacing: 6,
                    ),
                  ),
                ),
              ),
            ),

          // Title text (bottom-left) - ignore pointer events
          Positioned(
            bottom: 16,
            left: 16,
            right: 16,
            child: IgnorePointer(
              child: Text(
                widget.banner.title,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 24,
                  fontWeight: AppFontWeight.bold,
                ),
                maxLines: 2,
                overflow: TextOverflow.ellipsis,
              ),
            ),
          ),
        ],
      ),
    );
  }

  /// Build placeholder when no images available
  Widget _buildPlaceholder() {
    return Container(
      color: Colors.grey[300],
      child: Center(
        child: Icon(
          Icons.image_not_supported,
          size: 64,
          color: Colors.grey[500],
        ),
      ),
    );
  }
}
