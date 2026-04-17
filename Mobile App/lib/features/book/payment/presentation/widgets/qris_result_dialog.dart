import 'dart:async';
import 'dart:ui' as ui;
import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter/services.dart';
import 'package:gal/gal.dart';
import 'package:go_router/go_router.dart';
import 'package:qr_flutter/qr_flutter.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../l10n/app_localizations.dart';
import '../../../../../router/route_constants.dart';
import '../../model/doku_qris_model.dart';

/// QRIS Result Dialog with QR Code display and download
class QRISResultDialog extends StatefulWidget {
  final DokuQRISData qrisData;
  final double amount;
  final VoidCallback onClose;

  const QRISResultDialog({
    Key? key,
    required this.qrisData,
    required this.amount,
    required this.onClose,
  }) : super(key: key);

  @override
  State<QRISResultDialog> createState() => _QRISResultDialogState();
}

class _QRISResultDialogState extends State<QRISResultDialog> {
  late Timer _timer;
  late Duration _remainingTime;
  final GlobalKey _qrKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    _calculateRemainingTime();
    _startCountdown();
  }

  void _calculateRemainingTime() {
    try {
      final validityPeriod = DateTime.parse(widget.qrisData.validityPeriod);
      final now = DateTime.now();
      _remainingTime = validityPeriod.difference(now);

      // If already expired, set to 0
      if (_remainingTime.isNegative) {
        _remainingTime = Duration.zero;
      }

      // Force maximum 30 minutes countdown
      if (_remainingTime.inMinutes > 30) {
        _remainingTime = const Duration(minutes: 30);
      }
    } catch (e) {
      // Default to 30 minutes if parsing fails
      _remainingTime = const Duration(minutes: 30);
    }
  }

  void _startCountdown() {
    _timer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (_remainingTime.inSeconds > 0) {
        setState(() {
          _remainingTime = _remainingTime - const Duration(seconds: 1);
        });
      } else {
        timer.cancel();
      }
    });
  }

  @override
  void dispose() {
    _timer.cancel();
    super.dispose();
  }

  String _formatDuration(Duration duration) {
    final hours = duration.inHours;
    final minutes = duration.inMinutes.remainder(60);
    final seconds = duration.inSeconds.remainder(60);
    return '${hours.toString().padLeft(2, '0')}:${minutes.toString().padLeft(2, '0')}:${seconds.toString().padLeft(2, '0')}';
  }

  Future<void> _downloadQRCode() async {
    final localizations = AppLocalizations.of(context)!;

    try {
      // Find the RenderRepaintBoundary
      final RenderRepaintBoundary boundary =
          _qrKey.currentContext!.findRenderObject() as RenderRepaintBoundary;

      // Capture the image
      final ui.Image image = await boundary.toImage(pixelRatio: 3.0);
      final ByteData? byteData =
          await image.toByteData(format: ui.ImageByteFormat.png);
      final Uint8List pngBytes = byteData!.buffer.asUint8List();

      // Save to gallery using Gal package
      await Gal.putImageBytes(pngBytes);

      if (mounted) {
        showNotificationDialog(
          context,
          localizations.qrisResultDialogDownloadSuccess,
          defaultIcon: Icons.check_circle_outline,
          // Use primaryAdaptive for the download success notification icon color
          iconColor: AppColors.primaryAdaptive(context),
        );
      }
    } catch (e) {
      if (mounted) {
        showNotificationDialog(
          context,
          '${localizations.qrisResultDialogDownloadError}: ${e.toString()}',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;

    // Dark mode detection for dialog colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Dialog(
      backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      child: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(24),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              // Logo Ulin Mahoni
              Image.asset(
                AppImage.logo,
                width: 75,
                height: 75,
                fit: BoxFit.contain,
                errorBuilder: (context, error, stackTrace) {
                  // Use primaryAdaptive for the fallback icon color
                  return Icon(
                    Icons.check_circle,
                    size: 75,
                    color: AppColors.primaryAdaptive(context),
                  );
                },
              ),
              const SizedBox(height: 16),

              // Title
              Text(
                localizations.qrisResultDialogTitle,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 8),

              // Subtitle
              Text(
                localizations.qrisResultDialogScanQR,
                style: TextStyle(
                  fontSize: 14,
                  color: Colors.grey.shade600,
                ),
                textAlign: TextAlign.center,
              ),
              const SizedBox(height: 24),

              // QR Code with RepaintBoundary for download
              Center(
                child: RepaintBoundary(
                  key: _qrKey,
                  child: Container(
                    constraints: BoxConstraints(
                      maxWidth: MediaQuery.of(context).size.width - 80, // 24px padding * 2 + extra margin
                    ),
                    padding: const EdgeInsets.all(16),
                    decoration: BoxDecoration(
                      color: Colors.white,
                      borderRadius: BorderRadius.circular(12),
                      border: Border.all(color: Colors.grey.shade300, width: 2),
                    ),
                    child: LayoutBuilder(
                      builder: (context, constraints) {
                        // Calculate QR size based on available space
                        final maxQRSize = constraints.maxWidth - 32; // minus padding
                        final qrSize = maxQRSize > 250 ? 250.0 : maxQRSize;

                        return QrImageView(
                          data: widget.qrisData.qrContent,
                          version: QrVersions.auto,
                          size: qrSize,
                          backgroundColor: Colors.white,
                          errorCorrectionLevel: QrErrorCorrectLevel.H,
                        );
                      },
                    ),
                  ),
                ),
              ),
              const SizedBox(height: 24),

              // Info Container
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: isDark ? const Color(0xFF374151) : Colors.grey.shade50,
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: isDark ? Colors.grey.shade600 : Colors.grey.shade200),
                ),
                child: Column(
                  children: [
                    // Amount
                    _buildInfoRow(
                      localizations.qrisResultDialogAmount,
                      formatCurrency(widget.amount) ?? 'Rp 0',
                    ),
                    const Divider(height: 20),

                    // Order ID
                    _buildInfoRow(
                      'Order ID',
                      widget.qrisData.referenceNo,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 16),

              // Countdown Timer
              Container(
                padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
                decoration: BoxDecoration(
                  // Use dark-aware amber/warning box background and border
                  color: isDark ? AppColors.surfaceDarkElevated : Colors.orange.shade50,
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: isDark ? Colors.white24 : Colors.orange.shade200),
                ),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Icon(Icons.access_time, size: 20, color: Colors.orange.shade700),
                    const SizedBox(width: 8),
                    Text(
                      '${localizations.qrisResultDialogValidUntil}: ${_formatDuration(_remainingTime)}',
                      style: TextStyle(
                        fontSize: 14,
                        fontWeight: FontWeight.w600,
                        color: Colors.orange.shade700,
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Buttons
              Column(
                children: [
                  // Download QR Code Button (Primary)
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: _downloadQRCode,
                      icon: const Icon(Icons.download, size: 20),
                      label: Text(
                        localizations.qrisResultDialogDownloadQR,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        // Use primaryAdaptive for the download button background
                        backgroundColor: AppColors.primaryAdaptive(context),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        elevation: 0,
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Pesan Lagi Button (Green)
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () {
                        Navigator.of(context).pop();
                        context.go(RoutePaths.home);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                        elevation: 0,
                      ),
                      child: Text(
                        localizations.qrisResultDialogOrderAgainButton,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(height: 12),

                  // Tutup Button (Outline)
                  SizedBox(
                    width: double.infinity,
                    child: OutlinedButton(
                      onPressed: widget.onClose,
                      style: OutlinedButton.styleFrom(
                        // Use primaryAdaptive for the close button foreground and border
                        foregroundColor: AppColors.primaryAdaptive(context),
                        side: BorderSide(color: AppColors.primaryAdaptive(context)),
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(10),
                        ),
                      ),
                      child: Text(
                        localizations.qrisResultDialogCloseButton,
                        style: const TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 14,
            color: isDark ? Colors.grey.shade400 : Colors.grey.shade600,
          ),
        ),
        Flexible(
          child: Text(
            value,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
            textAlign: TextAlign.right,
            maxLines: 2,
            overflow: TextOverflow.ellipsis,
          ),
        ),
      ],
    );
  }
}
