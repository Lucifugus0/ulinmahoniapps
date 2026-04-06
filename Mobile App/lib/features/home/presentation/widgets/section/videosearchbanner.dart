import 'dart:io';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:video_player/video_player.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import 'searchfilter.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/utils/greeting_helper.dart';
import '../../../../../core/services/video_cache_service.dart';
import '../../../provider/content_provider.dart';

class VideoSearchBanner extends ConsumerStatefulWidget {
  const VideoSearchBanner({super.key});

  @override
  ConsumerState<VideoSearchBanner> createState() => _VideoSearchBannerState();
}

class _VideoSearchBannerState extends ConsumerState<VideoSearchBanner> {
  late VideoPlayerController _controller;
  final TextEditingController _searchController = TextEditingController();

  @override
  void initState() {
    super.initState();
    _initializeVideo();
  }

  /// Initialize video player — check for cached video first, fallback to bundled asset.
  /// After init, check for new video in background and cache for next launch.
  Future<void> _initializeVideo() async {
    // Check for cached video file from previous background download
    final cachedPath = await VideoCacheService.getCachedVideoPath();

    if (cachedPath != null) {
      _controller = VideoPlayerController.file(File(cachedPath));
    } else {
      _controller = VideoPlayerController.asset(AppVideo.homeVideo);
    }

    _controller.initialize().then((_) async {
      // CRITICAL FIX: Check if widget AND controller still valid
      if (!mounted) return;
      if (!_controller.value.isInitialized) return;

      try {
        await _controller.setLooping(true);
        await _controller.setVolume(0.0);

        // MEDIATEK FIX: Add delay before play to ensure codec ready
        await Future.delayed(const Duration(milliseconds: 100));

        if (mounted && _controller.value.isInitialized) {
          await _controller.play();
          if (mounted) {
            setState(() {});
          }
        }
      } catch (e) {
        AppLogger.e("Error setting up video playback", e, StackTrace.current, 'VIDEO-BANNER');
      }
    }).catchError((error) {
      AppLogger.e("Error loading video", error, StackTrace.current, 'VIDEO-BANNER');
    });

    // Background: check for new video URL and cache for next launch
    _checkAndCacheNewVideo();
  }

  /// Fetches active video URL from API and caches it in background for next launch
  void _checkAndCacheNewVideo() {
    Future.microtask(() async {
      try {
        final videoUrlAsync = ref.read(heroVideoUrlProvider.future);
        final videoUrl = await videoUrlAsync;
        if (videoUrl != null && videoUrl.isNotEmpty) {
          await VideoCacheService.checkAndCacheVideo(videoUrl);
        }
      } catch (e) {
        AppLogger.e('Error checking hero video', e, null, 'VIDEO-BANNER');
      }
    });
  }

  @override
  void dispose() {
    // CRITICAL FIX: Only dispose if initialized to prevent MediaCodec crash
    if (_controller.value.isInitialized) {
      _controller.pause();  // Pause before dispose for MediaTek stability
      _controller.dispose();
    }
    _searchController.dispose();
    super.dispose();
  }

  /// Builds tagline text spans — uses API tagline if available, otherwise falls back to localized 3-part text
  List<TextSpan> _buildTaglineSpans(AppLocalizations localizations) {
    final taglineAsync = ref.watch(taglineProvider);
    final apiTagline = taglineAsync.value;

    if (apiTagline != null && apiTagline.isNotEmpty) {
      // Single text span with API tagline
      return [TextSpan(text: apiTagline, style: const TextStyle(color: Colors.white))];
    }

    // Fallback: localized 3-part tagline with highlighted middle word
    return [
      TextSpan(text: localizations.homeBannerPart1, style: const TextStyle(color: Colors.white)),
      TextSpan(text: localizations.homeBannerPart2, style: TextStyle(color: AppColors.primaryAdaptive(context))),
      TextSpan(text: localizations.homeBannerPart3, style: const TextStyle(color: Colors.white)),
    ];
  }

  void _showFilterDialog() {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return const SearchFilterModal();
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    final screenHeight = MediaQuery.of(context).size.height;
    final double bannerHeight = screenHeight * 0.5;
    final localizations = AppLocalizations.of(context)!;
    final greeting = getTimeBasedGreeting(localizations);
    // Detect dark/light mode for theme-aware text colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return _controller.value.isInitialized
        ? Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // Greeting text — top padding accounts for navbar overlap since SafeArea top is off
        Padding(
          padding: EdgeInsets.fromLTRB(24, MediaQuery.of(context).padding.top + 16, 24, 8),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$greeting',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87,
                ),
              ),
              const SizedBox(height: 4),
              // Show dynamic tagline from API, fallback to localized subtitle
              Builder(builder: (context) {
                final taglineAsync = ref.watch(taglineProvider);
                final apiTagline = taglineAsync.value;
                final text = (apiTagline != null && apiTagline.isNotEmpty)
                    ? apiTagline
                    : localizations.homeSubtitle;
                return Text(
                  text,
                  style: TextStyle(
                    fontSize: 16,
                    color: isDark ? Colors.grey[400] : Colors.grey[600],
                  ),
                );
              }),
            ],
          ),
        ),
        // Video banner — natural 16:9 aspect ratio, no cropping
        Stack(
          children: [
            ClipRRect(
              child: SizedBox(
                width: double.infinity,
                child: AspectRatio(
                  aspectRatio: _controller.value.aspectRatio,
                  child: VideoPlayer(_controller),
                ),
              ),
            ),
            // Dark shadow gradient removed — video plays without overlay
            // Tagline removed from video overlay — now shown above video in greeting section
            // Glass-style search bar at bottom
            Positioned(
              left: 24,
              right: 24,
              bottom: 16,
              child: ClipRRect(
                borderRadius: BorderRadius.circular(25),
                child: BackdropFilter(
                  filter: ImageFilter.blur(sigmaX: 15, sigmaY: 15),
                  child: Container(
                    height: 50,
                    decoration: BoxDecoration(
                      color: isDark
                          ? Colors.white.withValues(alpha: 0.12)
                          : Colors.white.withValues(alpha: 0.85),
                      borderRadius: BorderRadius.circular(25),
                      border: Border.all(
                        color: isDark
                            ? Colors.white.withValues(alpha: 0.3)
                            : Colors.white.withValues(alpha: 0.4),
                        width: isDark ? 1.0 : 0.5,
                      ),
                      boxShadow: isDark
                          ? [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.4),
                                blurRadius: 16,
                                offset: const Offset(0, 4),
                              ),
                            ]
                          : null,
                    ),
                    padding: const EdgeInsets.symmetric(horizontal: 16),
                    child: Row(
                      children: [
                        Expanded(
                          child: TextFormField(
                            controller: _searchController,
                            readOnly: true,
                            onTap: _showFilterDialog,
                            cursorColor: Colors.grey,
                            textAlign: TextAlign.left,
                            style: TextStyle(
                              fontSize: 18,
                              fontWeight: FontWeight.normal,
                              color: isDark ? Colors.white70 : Colors.black45,
                            ),
                            decoration: InputDecoration(
                              hintText: localizations.searchBannerTitle,
                              hintStyle: TextStyle(
                                fontSize: 18,
                                fontWeight: FontWeight.normal,
                                color: isDark ? Colors.white54 : Colors.black45,
                              ),
                              border: InputBorder.none,
                              enabledBorder: InputBorder.none,
                              focusedBorder: InputBorder.none,
                              filled: false,
                              isCollapsed: true,
                              contentPadding: const EdgeInsets.symmetric(vertical: 12),
                            ),
                          ),
                        ),
                        IconButton(
                          icon: Icon(Icons.search,
                              size: 28, color: isDark ? Colors.white : Colors.black),
                          onPressed: _showFilterDialog,
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),
          ],
        ),
      ],
    )
        : Skeletonizer(
      enabled: true,
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Greeting skeleton — top padding accounts for navbar overlap
          Padding(
            padding: EdgeInsets.fromLTRB(24, MediaQuery.of(context).padding.top + 16, 24, 16),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 24,
                  width: 200,
                  color: Colors.grey,
                ),
                const SizedBox(height: 4),
                Container(
                  height: 16,
                  width: 250,
                  color: Colors.grey,
                ),
              ],
            ),
          ),
          // Banner skeleton
          Stack(
            children: [
              Container(
                height: bannerHeight,
                width: double.infinity,
                decoration: const BoxDecoration(
                  color: Colors.grey,
                  borderRadius: BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                ),
              ),
              Positioned(
                left: 24,
                right: 24,
                bottom: 16,
                child: Container(
                  height: 50,
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(25),
                  ),
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: Row(
                    children: [
                      Expanded(
                        child: Container(
                          height: 20,
                          color: Colors.grey,
                        ),
                      ),
                      const SizedBox(width: 8),
                      const Icon(Icons.search, size: 28),
                    ],
                  ),
                ),
              ),
            ],
          ),
        ],
      ),
    );
  }
}