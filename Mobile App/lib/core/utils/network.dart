import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:connectivity_plus/connectivity_plus.dart';
import 'package:internet_connection_checker_plus/internet_connection_checker_plus.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../constants/app_asset_constants.dart';

class NetworkManager {
  static final NetworkManager _instance = NetworkManager._internal();
  factory NetworkManager() => _instance;
  NetworkManager._internal();

  final StreamController<bool> _connectivityStreamController = StreamController<bool>.broadcast();
  Stream<bool> get connectivityStream => _connectivityStreamController.stream;

  bool _isOnline = true;
  bool get isOnline => _isOnline;

  StreamSubscription? _connectivitySubscription;
  StreamSubscription? _internetSubscription;

  Future<void> initialize() async {
    debugPrint('--- 📡 NetworkManager: Initializing... ---');
    // Assume online at startup — only show dialog on confirmed connectivity loss
    _isOnline = true;
    _monitorNetworkChanges();
  }

  /// Check if device has network connectivity (WiFi or cellular connected).
  /// Does NOT perform deep internet ping to avoid false negatives on restricted networks.
  Future<bool> hasInternet() async {
    try {
      final connectivityResult = await (Connectivity().checkConnectivity());
      final connected = !_isConnectivityNone(connectivityResult);
      _updateStatus(connected);
      return connected;
    } catch (e) {
      debugPrint('--- ❌ NetworkManager Error: $e ---');
      return true; // Assume online on error to avoid false dialogs
    }
  }

  bool _isConnectivityNone(List<ConnectivityResult> results) {
    if (results.isEmpty) return true;
    if (results.contains(ConnectivityResult.none)) return true;
    return false;
  }

  void _updateStatus(bool newStatus) {
    _isOnline = newStatus;
    _connectivityStreamController.sink.add(newStatus);
  }

  /// Monitor network changes using Connectivity plugin only.
  /// Avoids InternetConnection deep ping which causes false negatives on restricted networks.
  void _monitorNetworkChanges() {
    _connectivitySubscription = Connectivity().onConnectivityChanged.listen(
      (event) {
        final connected = !_isConnectivityNone(event);
        debugPrint('--- 📡 NetworkManager: Connectivity changed = $connected ---');
        _updateStatus(connected);
      },
      onError: (error) {
        debugPrint('--- ⚠️ NetworkManager: Connectivity stream error = $error ---');
      },
    );
  }

  void dispose() {
    _connectivityStreamController.close();
    _connectivitySubscription?.cancel();
    _internetSubscription?.cancel();
  }
}

// ==========================================================
// 2. NETWORK MONITOR WIDGET (SOLUSI STACK MANUAL)
// ==========================================================

class NetworkConnectivityMonitor extends ConsumerStatefulWidget {
  final Widget child;
  const NetworkConnectivityMonitor({super.key, required this.child});

  @override
  ConsumerState<NetworkConnectivityMonitor> createState() => _NetworkConnectivityMonitorState();
}

class _NetworkConnectivityMonitorState extends ConsumerState<NetworkConnectivityMonitor> {
  bool _isConnected = true;
  bool _startupGracePeriod = true; // Suppress dialog during startup
  StreamSubscription<bool>? _connectivitySubscription;

  @override
  void initState() {
    super.initState();

    // Suppress dialog for first 5 seconds to avoid false positives on startup
    Future.delayed(const Duration(seconds: 5), () {
      if (mounted) setState(() => _startupGracePeriod = false);
    });

    _connectivitySubscription = NetworkManager().connectivityStream.listen((isOnline) {
      if (mounted) {
        setState(() {
          _isConnected = isOnline;
        });
      }
    });
  }

  @override
  void dispose() {
    // CRITICAL FIX: Cancel stream subscription untuk prevent memory leak
    _connectivitySubscription?.cancel();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context);
    final title = localizations?.noInternetTitle ?? "Koneksi Terputus";
    final message = localizations?.noInternetMessage ?? "Tidak ada koneksi internet.";
    final btnText = localizations?.retryButton ?? "OK";
    // Dark mode detection for dialog colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Stack(
      children: [
        // LAYER 1: APLIKASI UTAMA (Selalu di bawah)
        widget.child,

        // LAYER 2: POPUP OFFLINE (only after startup grace period)
        if (!_isConnected && !_startupGracePeriod)
          Positioned.fill(
            child: Material(
              color: Colors.transparent,
              child: Stack(
                children: [
                  // A. Layar Hitam Transparan (Barrier)
                  // Mencegah user klik aplikasi di belakangnya
                  Container(
                    color: Colors.black.withOpacity(0.6),
                    width: double.infinity,
                    height: double.infinity,
                  ),

                  // B. Dialog Box Custom
                  Center(
                    child: Container(
                      margin: const EdgeInsets.symmetric(horizontal: 40),
                      padding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
                      decoration: BoxDecoration(
                        color: isDark ? const Color(0xFF1F2937) : Colors.white,
                        borderRadius: BorderRadius.circular(28),
                        boxShadow: const [
                          BoxShadow(
                            color: Colors.black26,
                            blurRadius: 10,
                            offset: Offset(0, 4),
                          )
                        ],
                      ),
                      child: Column(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          Text(
                            title,
                            style: TextStyle(
                                fontWeight: FontWeight.bold,
                                fontSize: 18,
                                color: isDark ? Colors.white : Colors.black
                            ),
                            textAlign: TextAlign.center,
                          ),
                          const SizedBox(height: 16),
                          // Gambar dengan Fallback Icon
                          Image.asset(
                            AppImage.logo,
                            width: 80,
                            height: 80,
                            errorBuilder: (ctx, err, stack) => const Icon(
                                Icons.wifi_off,
                                color: Colors.red,
                                size: 80
                            ),
                          ),
                          const SizedBox(height: 16),
                          Text(
                            message,
                            style: TextStyle(
                                fontSize: 14,
                                color: isDark ? Colors.grey[300] : Colors.black87
                            ),
                            textAlign: TextAlign.center,
                          ),
                          const SizedBox(height: 24),
                          // Tombol Dummy (Koneksi dicek otomatis, tapi tombol memberi feedback visual)
                          TextButton(
                            onPressed: () async {
                              // Manual check saat tombol ditekan
                              await NetworkManager().hasInternet();
                            },
                            child: Text(
                                btnText,
                                style: const TextStyle(fontWeight: FontWeight.bold)
                            ),
                          )
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
      ],
    );
  }
}