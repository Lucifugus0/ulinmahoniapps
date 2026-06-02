import 'dart:io';
import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:url_launcher/url_launcher.dart';
import '../network/dio_client.dart';
import '../utils/app_logger.dart';

/// Service to check app version against server minimum and prompt force update.
/// Compares local VERSION_NAME from .env with min_app_version from health check API.
class VersionCheckService {
  static const String _playStoreUrl = 'https://play.google.com/store/apps/details?id=com.ulinmahoni.apps';
  static const String _appStoreUrl = 'https://apps.apple.com/app/ulin-mahoni/id6740891498';

  /// Check version on app launch. Shows force update dialog if outdated.
  static Future<void> checkVersion(BuildContext context) async {
    try {
      final dioClient = DioClient();
      final response = await dioClient.get('/health-check');

      if (response.statusCode == 200 || response.statusCode == 503) {
        final data = response.data;
        final minVersion = data['min_app_version'] as String?;

        if (minVersion == null || minVersion.isEmpty) return;

        final currentVersion = dotenv.env['VERSION_NAME']?.replaceAll('Release ', '') ?? '0.0.0';

        if (_isVersionOutdated(currentVersion, minVersion)) {
          AppLogger.w('App version $currentVersion is below minimum $minVersion', 'VERSION');
          if (context.mounted) {
            await _showForceUpdateDialog(context, currentVersion, minVersion);
          }
        } else {
          AppLogger.s('App version $currentVersion meets minimum $minVersion', 'VERSION');
        }
      }
    } catch (e) {
      // Don't block app launch if version check fails
      AppLogger.e('Version check failed', e, null, 'VERSION');
    }
  }

  /// Compare version strings (e.g., "2.0.10" < "2.0.11")
  static bool _isVersionOutdated(String current, String minimum) {
    final currentParts = current.split('.').map((e) => int.tryParse(e) ?? 0).toList();
    final minimumParts = minimum.split('.').map((e) => int.tryParse(e) ?? 0).toList();

    // Pad shorter list with zeros
    while (currentParts.length < 3) currentParts.add(0);
    while (minimumParts.length < 3) minimumParts.add(0);

    for (int i = 0; i < 3; i++) {
      if (currentParts[i] < minimumParts[i]) return true;
      if (currentParts[i] > minimumParts[i]) return false;
    }
    return false; // Equal versions
  }

  /// Show non-dismissible force update dialog
  static Future<void> _showForceUpdateDialog(
      BuildContext context, String currentVersion, String minVersion) async {
    await showDialog(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => PopScope(
        canPop: false,
        child: AlertDialog(
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
          title: const Row(
            children: [
              Icon(Icons.system_update, color: Colors.orange, size: 28),
              SizedBox(width: 12),
              Expanded(
                child: Text('Update Required', style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
              ),
            ],
          ),
          content: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              const Text(
                'A new version of Ulin Mahoni is available. Please update to continue using the app.',
                style: TextStyle(fontSize: 14),
              ),
              const SizedBox(height: 12),
              Text(
                'Your version: $currentVersion\nMinimum required: $minVersion',
                style: TextStyle(fontSize: 12, color: Colors.grey[600]),
              ),
            ],
          ),
          actions: [
            ElevatedButton.icon(
              onPressed: () => _openStore(),
              icon: const Icon(Icons.download),
              label: const Text('Update Now'),
              style: ElevatedButton.styleFrom(
                backgroundColor: Colors.green[700],
                foregroundColor: Colors.white,
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Open the appropriate app store
  static Future<void> _openStore() async {
    final url = Platform.isIOS ? _appStoreUrl : _playStoreUrl;
    try {
      await launchUrl(Uri.parse(url), mode: LaunchMode.externalApplication);
    } catch (e) {
      AppLogger.e('Failed to open store', e, null, 'VERSION');
    }
  }
}
