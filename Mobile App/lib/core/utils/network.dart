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
    _monitorNetworkChanges();
    // Perform initial check after a small delay to allow network to be ready
    Future.delayed(const Duration(milliseconds: 500), () {
      hasInternet().then((isOnline) {
        debugPrint('--- 📡 NetworkManager: Initial status = $isOnline ---');
      }).catchError((error) {
        debugPrint('--- ⚠️ NetworkManager: Initial check failed (network not ready) ---');
        // Don't crash, just assume offline and let the stream monitor handle it
        _updateStatus(false);
      });
    });
  }

  Future<bool> hasInternet() async {
    try {
      final connectivityResult = await (Connectivity().checkConnectivity());
      if (_isConnectivityNone(connectivityResult)) {
        _updateStatus(false);
        return false;
      }

      // Add timeout to prevent hanging
      final isConnected = await InternetConnection().hasInternetAccess
          .timeout(
            const Duration(seconds: 5),
            onTimeout: () {
              debugPrint('--- ⚠️ NetworkManager: Internet check timeout ---');
              return false;
            },
          );
      _updateStatus(isConnected);
      return isConnected;
    } catch (e) {
      debugPrint('--- ❌ NetworkManager Error: $e ---');
      _updateStatus(false);
      return false;
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

  void _monitorNetworkChanges() {
    // Use only InternetConnection for more accurate monitoring
    // This already handles both connectivity changes and actual internet access
    _internetSubscription = InternetConnection().onStatusChange.listen(
      (status) {
        final isConnected = status == InternetStatus.connected;
        debugPrint('--- 📡 NetworkManager: Status changed = $isConnected ---');
        _updateStatus(isConnected);
      },
      onError: (error) {
        debugPrint('--- ⚠️ NetworkManager: Stream error = $error ---');
        _updateStatus(false);
      },
    );

    // Optional: Keep connectivity for immediate "no connection" detection
    _connectivitySubscription = Connectivity().onConnectivityChanged.listen(
      (event) {
        if (_isConnectivityNone(event)) {
          debugPrint('--- 📡 NetworkManager: No connectivity detected ---');
          _updateStatus(false);
        }
        // Don't check hasInternetAccess here - let InternetConnection stream handle it
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
  // Kita simpan status koneksi lokal di state widget ini
  bool _isConnected = true;
  StreamSubscription<bool>? _connectivitySubscription;

  @override
  void initState() {
    super.initState();
    // 1. Dengarkan stream - FIXED: Simpan subscription untuk di-cancel nanti
    _connectivitySubscription = NetworkManager().connectivityStream.listen((isOnline) {
      if (mounted) {
        setState(() {
          _isConnected = isOnline;
        });
      }
    });

    // 2. Cek status awal (untuk sinkronisasi startup)
    WidgetsBinding.instance.addPostFrameCallback((_) async {
      final currentStatus = await NetworkManager().hasInternet();
      if (mounted) {
        setState(() {
          _isConnected = currentStatus;
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

        // LAYER 2: POPUP OFFLINE (Hanya muncul jika _isConnected == false)
        if (!_isConnected)
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