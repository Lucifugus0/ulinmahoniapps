import 'dart:io';
import 'package:dio/dio.dart';
import 'package:path_provider/path_provider.dart';
import 'package:shared_preferences/shared_preferences.dart';
import '../utils/app_logger.dart';

/// Service to cache hero video files locally for offline/fast playback.
/// Downloads new videos in the background and stores them in the app documents directory.
/// On next launch, the cached video is used instead of the bundled asset.
class VideoCacheService {
  static const _cachedUrlKey = 'cached_hero_video_url';
  static const _cachedPathKey = 'cached_hero_video_path';

  /// Returns the path to the cached video file, or null if no cached video exists.
  static Future<String?> getCachedVideoPath() async {
    final prefs = await SharedPreferences.getInstance();
    final path = prefs.getString(_cachedPathKey);
    if (path != null && File(path).existsSync()) {
      return path;
    }
    return null;
  }

  /// Checks if the remote video URL differs from the cached one.
  /// If different, downloads the new video in the background for next launch.
  static Future<void> checkAndCacheVideo(String? remoteUrl) async {
    if (remoteUrl == null || remoteUrl.isEmpty) return;

    final prefs = await SharedPreferences.getInstance();
    final cachedUrl = prefs.getString(_cachedUrlKey);

    // Already cached this URL — skip download
    if (cachedUrl == remoteUrl) {
      final cachedPath = prefs.getString(_cachedPathKey);
      if (cachedPath != null && File(cachedPath).existsSync()) {
        AppLogger.s('Hero video already cached: $cachedPath', 'VIDEO-CACHE');
        return;
      }
    }

    // Download new video in background
    try {
      AppLogger.s('Downloading new hero video: $remoteUrl', 'VIDEO-CACHE');
      final dir = await getApplicationDocumentsDirectory();
      final filePath = '${dir.path}/hero_video_${DateTime.now().millisecondsSinceEpoch}.mp4';

      final dio = Dio();
      await dio.download(remoteUrl, filePath);

      // Clean up old cached file if exists
      final oldPath = prefs.getString(_cachedPathKey);
      if (oldPath != null && File(oldPath).existsSync() && oldPath != filePath) {
        try {
          File(oldPath).deleteSync();
        } catch (_) {}
      }

      // Save new cache references
      await prefs.setString(_cachedUrlKey, remoteUrl);
      await prefs.setString(_cachedPathKey, filePath);

      AppLogger.s('Hero video cached at: $filePath', 'VIDEO-CACHE');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to cache hero video', e, stackTrace, 'VIDEO-CACHE');
    }
  }
}
