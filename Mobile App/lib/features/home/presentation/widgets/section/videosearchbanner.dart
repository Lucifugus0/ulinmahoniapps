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
    _controller = VideoPlayerController.asset(AppVideo.homeVideo)
      ..initialize().then((_) async {
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
        // Greeting text (adapts to dark/light mode)
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 8, 24, 8),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                '$greeting 👋',
                style: TextStyle(
                  fontSize: 24,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                localizations.homeSubtitle,
                style: TextStyle(
                  fontSize: 16,
                  color: isDark ? Colors.grey[400] : Colors.grey[600],
                ),
              ),
            ],
          ),
        ),
        // Video banner with text overlay
        Stack(
          children: [
            ClipRRect(
              borderRadius: const BorderRadius.only(
                bottomLeft: Radius.circular(32),
                bottomRight: Radius.circular(32),
              ),
              child: SizedBox(
                height: bannerHeight,
                width: double.infinity,
                child: FittedBox(
                  fit: BoxFit.cover,
                  child: SizedBox(
                    width: _controller.value.size.width,
                    height: _controller.value.size.height,
                    child: AspectRatio(
                      aspectRatio: _controller.value.aspectRatio,
                      child: VideoPlayer(_controller),
                    ),
                  ),
                ),
              ),
            ),
            // Dark shadow gradient at bottom of video
            Positioned(
              left: 0,
              right: 0,
              bottom: 0,
              height: 250,
              child: Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withOpacity(0.4),
                      Colors.black.withOpacity(0.75),
                    ],
                    stops: const [0.0, 0.5, 1.0],
                  ),
                  borderRadius: const BorderRadius.only(
                    bottomLeft: Radius.circular(32),
                    bottomRight: Radius.circular(32),
                  ),
                ),
              ),
            ),
            // Banner title text above search bar - single line
            Positioned(
              left: 24,
              right: 24,
              bottom: 80,
              child: RichText(
                text: TextSpan(
                  style: const TextStyle(
                    fontSize: 32,
                    fontWeight: FontWeight.bold,
                    shadows: [
                      Shadow(
                        offset: Offset(2, 2),
                        blurRadius: 6,
                        color: Colors.black87,
                      ),
                    ],
                  ),
                  children: [
                    TextSpan(
                      text: localizations.homeBannerPart1,
                      style: const TextStyle(color: Colors.white),
                    ),
                    TextSpan(
                      text: localizations.homeBannerPart2,
                      style: const TextStyle(color: AppColors.primaryColor),
                    ),
                    TextSpan(
                      text: localizations.homeBannerPart3,
                      style: const TextStyle(color: Colors.white),
                    ),
                  ],
                ),
              ),
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
                      // Semi-transparent glass surface
                      color: isDark
                          ? Colors.white.withValues(alpha: 0.12)
                          : Colors.white.withValues(alpha: 0.85),
                      borderRadius: BorderRadius.circular(25),
                      border: Border.all(
                        color: Colors.white.withValues(alpha: isDark ? 0.2 : 0.4),
                        width: 0.5,
                      ),
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
                              isCollapsed: true,
                              contentPadding:
                              const EdgeInsets.symmetric(vertical: 12),
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
          // Greeting skeleton
          Padding(
            padding: const EdgeInsets.fromLTRB(24, 16, 24, 16),
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