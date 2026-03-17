import 'dart:async';
import 'package:flutter/material.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import '../utils/ping_helper.dart';
import '../utils/app_logger.dart';
import '../widgets/dialog/notificationdialog.dart';
import '../../l10n/app_localizations.dart';

/// Enum untuk connection quality status
enum ConnectionQuality {
  normal,  // 0-150ms
  slow,    // 150-500ms
  lost,    // >500ms or timeout
}

/// Service untuk background ping monitoring
/// Melakukan ping periodic ke DNS servers dan show dialog jika koneksi lambat/hilang
class PingMonitorService {
  static final PingMonitorService _instance = PingMonitorService._internal();
  factory PingMonitorService() => _instance;
  PingMonitorService._internal();

  // Configuration constants
  static const Duration _checkInterval = Duration(seconds: 10);
  static const Duration _notificationCooldown = Duration(minutes: 2);
  static const int _normalThreshold = 150;   // ms
  static const int _warningThreshold = 500;  // ms

  // Ping targets (DNS publik)
  static const List<String> _pingTargets = [
    '8.8.8.8',        // Google DNS
    '1.1.1.1',        // Cloudflare DNS
    '208.67.222.222', // OpenDNS
  ];

  // State management
  Timer? _periodicTimer;
  DateTime? _lastNotificationTime;
  ConnectionQuality _lastQuality = ConnectionQuality.normal;
  bool _isMonitoring = false;
  BuildContext? _context;

  /// Initialize dan start ping monitoring
  Future<void> initialize(BuildContext context) async {
    if (_isMonitoring) {
      // AppLogger.w('Ping monitoring already running', 'PING-MONITOR');
      return;
    }

    _context = context;
    _isMonitoring = true;

    // AppLogger.i('🔵 Ping monitoring service initialized', 'PING-MONITOR');
    // AppLogger.i('Check interval: ${_checkInterval.inSeconds}s', 'PING-MONITOR');
    // AppLogger.i('Thresholds: Normal <${_normalThreshold}ms, Warning <${_warningThreshold}ms', 'PING-MONITOR');

    _startMonitoring();
  }

  /// Start periodic monitoring
  void _startMonitoring() {
    _periodicTimer = Timer.periodic(_checkInterval, (_) async {
      await _performCheck();
    });

    // Perform initial check immediately
    _performCheck();
  }

  /// Perform connection quality check
  Future<void> _performCheck() async {
    if (_context == null || !_context!.mounted) {
      // AppLogger.w('Context not available, skipping check', 'PING-MONITOR');
      return;
    }

    try {
      final quality = await _checkConnectionQuality();

      // AppLogger.d('Connection quality: $quality (was: $_lastQuality)', 'PING-MONITOR');

      // Check if should show notification
      if (_shouldShowNotification(quality)) {
        _showQualityDialog(_context!, quality);
      }
    } catch (e) {
      // AppLogger.e('Error during ping check', e, StackTrace.current, 'PING-MONITOR');
    }
  }

  /// Check connection quality based on device connectivity + ping
  Future<ConnectionQuality> _checkConnectionQuality() async {
    // STEP 1: Check device connectivity first (WiFi/Mobile data)
    final connectivityResult = await Connectivity().checkConnectivity();
    final hasDeviceConnection = connectivityResult.isNotEmpty &&
                                 !connectivityResult.contains(ConnectivityResult.none);

    if (!hasDeviceConnection) {
      // Device tidak terhubung ke WiFi atau Mobile data
      // AppLogger.w('Device tidak terhubung ke jaringan (WiFi/Mobile OFF)', 'PING-MONITOR');
      return ConnectionQuality.lost;
    }

    // AppLogger.d('Device terhubung via: ${connectivityResult.map((e) => e.name).join(", ")}', 'PING-MONITOR');

    // STEP 2: Ping untuk measure connection quality
    final latency = await PingHelper.averagePing(_pingTargets);

    if (latency == null) {
      // Device terhubung tapi ping gagal - internet issue atau device signal lemah
      // AppLogger.w('Device terhubung tapi ping gagal - kemungkinan signal lemah atau no internet', 'PING-MONITOR');
      return ConnectionQuality.lost;
    } else if (latency <= _normalThreshold) {
      // Good connection
      return ConnectionQuality.normal;
    } else if (latency <= _warningThreshold) {
      // Slow connection
      return ConnectionQuality.slow;
    } else {
      // Very slow - consider as lost
      return ConnectionQuality.lost;
    }
  }

  /// Check if should show notification (debouncing)
  bool _shouldShowNotification(ConnectionQuality quality) {
    // Don't show for normal quality
    if (quality == ConnectionQuality.normal) {
      // Update last quality but don't show notification
      _lastQuality = quality;
      return false;
    }

    // Check if quality changed
    if (_lastQuality == quality) {
      // Same quality, don't spam
      return false;
    }

    // Check cooldown
    if (_lastNotificationTime != null) {
      final elapsed = DateTime.now().difference(_lastNotificationTime!);
      if (elapsed < _notificationCooldown) {
        // AppLogger.d('Notification skipped (cooldown: ${elapsed.inSeconds}s / ${_notificationCooldown.inSeconds}s)', 'PING-MONITOR');
        return false;
      }
    }

    return true;
  }

  /// Show dialog notification based on quality
  void _showQualityDialog(BuildContext context, ConnectionQuality quality) {
    if (!context.mounted) return;

    final localizations = AppLocalizations.of(context);

    switch (quality) {
      case ConnectionQuality.slow:
        // AppLogger.w('⚠️ Showing SLOW connection dialog', 'PING-MONITOR');
        showNotificationDialog(
          context,
          localizations?.pingSlowConnectionMessage ??
            'Koneksi internet Anda lambat. Ini mungkin mempengaruhi pengalaman Anda.',
          title: localizations?.pingSlowConnectionTitle ?? 'Koneksi Lambat',
          defaultIcon: Icons.warning_amber_rounded,
          iconColor: Colors.orange,
          okButtonText: localizations?.pingDialogOkButton ?? 'OK',
          barrierDismissible: true,
        );
        break;

      case ConnectionQuality.lost:
        // AppLogger.e('❌ Showing LOST connection dialog', 'PING-MONITOR');
        showNotificationDialog(
          context,
          localizations?.pingNoConnectionMessage ??
            'Tidak dapat terhubung ke internet. Mohon periksa koneksi Anda.',
          title: localizations?.pingNoConnectionTitle ?? 'Tidak Ada Koneksi Internet',
          defaultIcon: Icons.wifi_off,
          iconColor: Colors.red,
          okButtonText: localizations?.pingDialogOkButton ?? 'OK',
          barrierDismissible: false,
        );
        break;

      case ConnectionQuality.normal:
        // No notification for normal connection
        break;
    }

    // Update state
    _lastNotificationTime = DateTime.now();
    _lastQuality = quality;
  }

  /// Pause monitoring (optional for battery saving)
  void pause() {
    if (!_isMonitoring) return;

    // AppLogger.i('⏸️ Ping monitoring paused', 'PING-MONITOR');
    _periodicTimer?.cancel();
    _isMonitoring = false;
  }

  /// Resume monitoring
  void resume(BuildContext context) {
    if (_isMonitoring) return;

    // AppLogger.i('▶️ Ping monitoring resumed', 'PING-MONITOR');
    _context = context;
    _isMonitoring = true;
    _startMonitoring();
  }

  /// Dispose and cleanup
  void dispose() {
    // AppLogger.i('🔴 Ping monitoring service disposed', 'PING-MONITOR');
    _periodicTimer?.cancel();
    _periodicTimer = null;
    _context = null;
    _isMonitoring = false;
  }
}
