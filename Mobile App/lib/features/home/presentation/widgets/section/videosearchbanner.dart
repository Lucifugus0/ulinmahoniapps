import 'dart:io';
import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:video_player/video_player.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/constants/app_asset_constants.dart';
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
          // Tagline moved out of the greeting block — it now floats,
          // centered, just above the search bar over the video banner.
          child: Text(
            greeting,
            style: TextStyle(
              fontSize: 24,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
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
            // Floating tagline — centered, sits just above the search bar.
            // search bar: bottom 16 + height 50 + 12 gap = 78
            Positioned(
              left: 24,
              right: 24,
              bottom: 78,
              child: Builder(builder: (context) {
                final taglineAsync = ref.watch(taglineProvider);
                final apiTagline = taglineAsync.value;
                final text = (apiTagline != null && apiTagline.isNotEmpty)
                    ? apiTagline
                    : localizations.homeSubtitle;
                return Text(
                  text,
                  textAlign: TextAlign.center,
                  style: const TextStyle(
                    // Inter font (matches Frontend web hero tagline), bold weight
                    fontFamily: 'Inter',
                    fontSize: 16,
                    fontWeight: FontWeight.bold,
                    color: Colors.white,
                    // Shadow keeps the tagline legible over any video frame
                    shadows: [
                      Shadow(
                        color: Colors.black54,
                        blurRadius: 8,
                        offset: Offset(0, 1),
                      ),
                    ],
                  ),
                );
              }),
            ),
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
            // Only the greeting remains here — tagline skeleton now floats
            // above the search bar to match the real layout.
            child: Container(
              height: 24,
              width: 200,
              color: Colors.grey,
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
              // Floating tagline skeleton — centered above the search bar
              Positioned(
                left: 24,
                right: 24,
                bottom: 78,
                child: Center(
                  child: Container(
                    height: 16,
                    width: 250,
                    color: Colors.grey,
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