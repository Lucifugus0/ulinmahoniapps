import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:smooth_page_indicator/smooth_page_indicator.dart';
import '../../../../../core/widgets/card/promocard.dart';
import '../../../../../features/promo_banner/provider/promo_banner_provider.dart';
import '../../../../../features/promo_banner/model/promo_banner_model.dart';
import '../../../../../router/route_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';

class PromoBannerSection extends ConsumerWidget {
  final Color? backgroundColor;
  const PromoBannerSection({Key? key, this.backgroundColor}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final bannersAsyncValue = ref.watch(activeBannersProvider);
    final textTheme = Theme.of(context).textTheme;
    final localizations = AppLocalizations.of(context)!;

    return bannersAsyncValue.when(
      loading: () => Container(
        color: backgroundColor,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            const SizedBox(height: 12),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16.0),
              child: Text(
                localizations.promoBanner,
                style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
              ),
            ),
            const SizedBox(height: 16),
            LayoutBuilder(
              builder: (context, constraints) {
                final cardWidth = MediaQuery.of(context).size.width - 32;
                final cardHeight = cardWidth * 9 / 16;
                return SizedBox(
                  height: cardHeight + 10,
                  child: Skeletonizer(
                    enabled: true,
                    child: ListView.builder(
                      scrollDirection: Axis.horizontal,
                      padding: const EdgeInsets.symmetric(horizontal: 16.0),
                      itemCount: 3,
                      itemBuilder: (context, index) {
                        return Padding(
                          padding: EdgeInsets.only(right: index < 2 ? 16.0 : 0),
                          child: PromoCard(
                            imageUrl: '',
                            onTap: () {},
                          ),
                        );
                      },
                    ),
                  ),
                );
              },
            ),
          ],
        ),
      ),
      error: (err, stack) {
        AppLogger.e('Error loading promo banners', err, stack, 'PROMO-BANNER');
        return const SizedBox.shrink();
      },
      data: (banners) {
        if (banners.isEmpty) {
          return const SizedBox.shrink();
        }

        return _BannerCarousel(
          banners: banners,
          backgroundColor: backgroundColor,
        );
      },
    );
  }
}

class _BannerCarousel extends StatefulWidget {
  final List<PromoBannerModel> banners;
  final Color? backgroundColor;

  const _BannerCarousel({
    required this.banners,
    this.backgroundColor,
  });

  @override
  State<_BannerCarousel> createState() => _BannerCarouselState();
}

class _BannerCarouselState extends State<_BannerCarousel> {
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
    if (widget.banners.length <= 1) return;

    _autoScrollTimer = Timer.periodic(const Duration(seconds: 4), (timer) {
      if (_pageController.hasClients) {
        _currentPage = (_currentPage + 1) % widget.banners.length;
        _pageController.animateToPage(
          _currentPage,
          duration: const Duration(milliseconds: 400),
          curve: Curves.fastOutSlowIn,
        );
      }
    });
  }

  void _stopAutoScroll() {
    _autoScrollTimer?.cancel();
  }

  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme;
    final localizations = AppLocalizations.of(context)!;

    return Container(
      color: widget.backgroundColor,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const SizedBox(height: 12),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: Text(
              localizations.promoBanner,
              style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
            ),
          ),
          const SizedBox(height: 16),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: Column(
              children: [
                AspectRatio(
                  aspectRatio: 16 / 9,
                  child: ClipRect(
                    child: GestureDetector(
                      onPanDown: (_) => _stopAutoScroll(),
                      onPanEnd: (_) => _startAutoScroll(),
                      onPanCancel: () => _startAutoScroll(),
                      child: PageView.builder(
                        controller: _pageController,
                        clipBehavior: Clip.hardEdge,
                        padEnds: false,
                        onPageChanged: (index) {
                          setState(() {
                            _currentPage = index;
                          });
                        },
                        itemCount: widget.banners.length,
                        itemBuilder: (context, index) {
                          final banner = widget.banners[index];
                          final imageUrl = banner.primaryImageUrl ?? '';
                          return PromoCard(
                            imageUrl: imageUrl,
                            onTap: () {
                              // Navigate to detail page using route constant
                              final path = RoutePaths.promoBannerDetail
                                  .replaceAll(':id', banner.id.toString());
                              context.push(path);
                              AppLogger.d(
                                'Navigating to promo detail: ${banner.id}',
                                'PROMO-BANNER',
                              );
                            },
                          );
                        },
                      ),
                    ),
                  ),
                ),
                if (widget.banners.length > 1) ...[
                  const SizedBox(height: 12),
                  SmoothPageIndicator(
                    controller: _pageController,
                    count: widget.banners.length,
                    effect: ExpandingDotsEffect(
                      dotHeight: 8,
                      dotWidth: 8,
                      activeDotColor: Theme.of(context).primaryColor,
                      dotColor: Colors.grey.shade300,
                      expansionFactor: 3,
                      spacing: 6,
                    ),
                  ),
                ],
                const SizedBox(height: 10),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
