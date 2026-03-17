import 'dart:async';
import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:go_router/go_router.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../l10n/app_localizations.dart';
import '../../../../../router/route_constants.dart';
import '../../model/doku_va_model.dart';

/// VA Result Dialog with countdown timer
class VAResultDialog extends StatefulWidget {
  final DokuVAData vaData;
  final VoidCallback onClose;

  const VAResultDialog({
    Key? key,
    required this.vaData,
    required this.onClose,
  }) : super(key: key);

  @override
  State<VAResultDialog> createState() => _VAResultDialogState();
}

class _VAResultDialogState extends State<VAResultDialog> {
  late Timer _timer;
  late Duration _remainingTime;

  @override
  void initState() {
    super.initState();
    _calculateRemainingTime();
    _startCountdown();
  }

  void _calculateRemainingTime() {
    try {
      final expiredDate = DateTime.parse(widget.vaData.expiredDate);
      final now = DateTime.now();
      _remainingTime = expiredDate.difference(now);

      // If already expired, set to 0
      if (_remainingTime.isNegative) {
        _remainingTime = Duration.zero;
      }

      // Force maximum 15 minutes countdown
      if (_remainingTime.inMinutes > 15) {
        _remainingTime = const Duration(minutes: 15);
      }
    } catch (e) {
      // Default to 15 minutes if parsing fails
      _remainingTime = const Duration(minutes: 15);
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

  Future<void> _copyToClipboard(String text) async {
    final localizations = AppLocalizations.of(context)!;
    await Clipboard.setData(ClipboardData(text: text));
    if (mounted) {
      showNotificationDialog(
        context,
        localizations.vaResultDialogCopySuccess,
        defaultIcon: Icons.check_circle_outline,
        iconColor: AppColors.primaryColor,
      );
    }
  }

  Future<void> _openHowToPay() async {
    final localizations = AppLocalizations.of(context)!;
    final url = widget.vaData.howToPayPage;
    if (url.isEmpty) {
      if (mounted) {
        showNotificationDialog(
          context,
          localizations.vaResultDialogLinkUnavailable,
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
      return;
    }

    final Uri uri = Uri.parse(url);
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } else {
      if (mounted) {
        showNotificationDialog(
          context,
          localizations.vaResultDialogLinkError,
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final amount = double.tryParse(widget.vaData.totalAmount) ?? 0.0;

    return Dialog(
      backgroundColor: Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Logo Ulin Mahoni (50% bigger, no circle background)
            Image.asset(
              AppImage.logo,
              width: 75,
              height: 75,
              fit: BoxFit.contain,
              errorBuilder: (context, error, stackTrace) {
                return const Icon(
                  Icons.check_circle,
                  size: 75,
                  color: AppColors.primaryColor,
                );
              },
            ),
            const SizedBox(height: 16),

            // Title
            Text(
              localizations.vaResultDialogTitle,
              style: const TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),

            // Info Container
            Container(
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.grey.shade200),
              ),
              child: Column(
                children: [
                  // Bank
                  _buildInfoRow(localizations.vaResultDialogBank, widget.vaData.bank),
                  const Divider(height: 20),

                  // VA Number with copy button
                  Row(
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              localizations.vaResultDialogVANumber,
                              style: TextStyle(
                                fontSize: 14,
                                color: Colors.grey.shade600,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Row(
                              children: [
                                Expanded(
                                  child: Text(
                                    widget.vaData.virtualAccountNo,
                                    style: const TextStyle(
                                      fontSize: 16,
                                      fontWeight: FontWeight.bold,
                                      color: Colors.black87,
                                    ),
                                  ),
                                ),
                                IconButton(
                                  onPressed: () => _copyToClipboard(widget.vaData.virtualAccountNo),
                                  icon: const Icon(Icons.copy, size: 20),
                                  color: AppColors.primaryColor,
                                  padding: EdgeInsets.zero,
                                  constraints: const BoxConstraints(),
                                ),
                              ],
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                  const Divider(height: 20),

                  // Amount
                  _buildInfoRow(localizations.vaResultDialogAmount, formatCurrency(amount) ?? 'Rp 0'),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Countdown Timer
            Container(
              padding: const EdgeInsets.symmetric(vertical: 12, horizontal: 16),
              decoration: BoxDecoration(
                color: Colors.orange.shade50,
                borderRadius: BorderRadius.circular(8),
                border: Border.all(color: Colors.orange.shade200),
              ),
              child: Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  Icon(Icons.access_time, size: 20, color: Colors.orange.shade700),
                  const SizedBox(width: 8),
                  Text(
                    '${localizations.vaResultDialogValidUntil}: ${_formatDuration(_remainingTime)}',
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
                // Lihat Cara Pembayaran Button (Primary)
                SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: _openHowToPay,
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppColors.primaryColor,
                      foregroundColor: Colors.white,
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                      elevation: 0,
                    ),
                    child: Text(
                      localizations.vaResultDialogHowToPayButton,
                      style: const TextStyle(
                        fontSize: 16,
                        fontWeight: FontWeight.w600,
                      ),
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
                      localizations.vaResultDialogOrderAgainButton,
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
                      foregroundColor: AppColors.primaryColor,
                      side: const BorderSide(color: AppColors.primaryColor),
                      padding: const EdgeInsets.symmetric(vertical: 14),
                      shape: RoundedRectangleBorder(
                        borderRadius: BorderRadius.circular(10),
                      ),
                    ),
                    child: Text(
                      localizations.vaResultDialogCloseButton,
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
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Text(
          label,
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey.shade600,
          ),
        ),
        Text(
          value,
          style: const TextStyle(
            fontSize: 16,
            fontWeight: FontWeight.bold,
            color: Colors.black87,
          ),
        ),
      ],
    );
  }
}
