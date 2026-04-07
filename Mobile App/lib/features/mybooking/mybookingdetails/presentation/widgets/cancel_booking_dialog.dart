import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/appfontweight_constants.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../model/cancel_refund_model.dart';
import '../../provider/cancel_booking_provider.dart';
import '../../../../mybooking/mybooking/provider/mybooking_provider.dart';
import '../../provider/mybookingdetails_provider.dart';

/// Dialog that shows refund breakdown and confirms booking cancellation.
/// For paid bookings: shows refund preview, optional bank fields for QRIS/VA.
/// For pending/waiting: simple confirmation with no refund.
class CancelBookingDialog extends ConsumerStatefulWidget {
  final String orderId;
  final String transactionStatus;
  final int idrec;

  const CancelBookingDialog({
    super.key,
    required this.orderId,
    required this.transactionStatus,
    required this.idrec,
  });

  @override
  ConsumerState<CancelBookingDialog> createState() => _CancelBookingDialogState();
}

class _CancelBookingDialogState extends ConsumerState<CancelBookingDialog> {
  CancelRefundPreview? _preview;
  bool _isLoadingPreview = true;
  bool _isCancelling = false;
  String? _errorMessage;

  // Bank account fields for QRIS/VA refunds
  final _bankNameController = TextEditingController();
  final _accountNoController = TextEditingController();
  final _accountHolderController = TextEditingController();

  final _currencyFormat = NumberFormat.currency(
    locale: 'id_ID',
    symbol: 'Rp ',
    decimalDigits: 0,
  );

  @override
  void initState() {
    super.initState();
    _loadPreview();
  }

  @override
  void dispose() {
    _bankNameController.dispose();
    _accountNoController.dispose();
    _accountHolderController.dispose();
    super.dispose();
  }

  Future<void> _loadPreview() async {
    final status = widget.transactionStatus.toLowerCase();

    // For pending/waiting, no refund preview needed
    if (status == 'pending' || status == 'waiting') {
      setState(() {
        _isLoadingPreview = false;
        _preview = null;
      });
      return;
    }

    // For paid bookings, fetch refund preview
    final notifier = ref.read(cancelBookingProvider.notifier);
    final response = await notifier.previewCancelRefund(orderId: widget.orderId);

    if (mounted) {
      setState(() {
        _isLoadingPreview = false;
        _preview = response?.refund;
        if (response == null) {
          _errorMessage = 'Gagal memuat preview refund';
        }
      });
    }
  }

  Future<void> _confirmCancel() async {
    // Validate bank fields if required
    if (_preview?.requiresBankAccount == true) {
      if (_bankNameController.text.trim().isEmpty ||
          _accountNoController.text.trim().isEmpty ||
          _accountHolderController.text.trim().isEmpty) {
        setState(() => _errorMessage = 'Semua field rekening bank wajib diisi');
        return;
      }
    }

    setState(() {
      _isCancelling = true;
      _errorMessage = null;
    });

    AppLogger.d('Starting cancel booking: ${widget.orderId}', 'CANCEL-DIALOG');

    // Use ref.read to get notifier — provider is kept alive via ref.watch in build()
    final notifier = ref.read(cancelBookingProvider.notifier);
    final result = await notifier.cancelBooking(
      orderId: widget.orderId,
      bankName: _preview?.requiresBankAccount == true ? _bankNameController.text.trim() : null,
      accountNo: _preview?.requiresBankAccount == true ? _accountNoController.text.trim() : null,
      accountHolder: _preview?.requiresBankAccount == true ? _accountHolderController.text.trim() : null,
    );

    AppLogger.d('Cancel result: ${result != null ? "SUCCESS (orderId=${result.orderId})" : "NULL (provider may have been disposed)"}', 'CANCEL-DIALOG');

    if (!mounted) return;

    if (result != null) {
      AppLogger.i('Cancel confirmed — showing success popup', 'CANCEL-DIALOG');

      // Invalidate booking providers to refresh the list
      ref.invalidate(userBookingsProvider);
      ref.invalidate(myBookingByIdProvider(widget.idrec));

      // Show success popup BEFORE closing cancel dialog so context is still mounted.
      // OK button closes both: the success popup then the cancel dialog (returning true).
      final totalRefund = result.refund?.totalRefund;
      final message = totalRefund != null && totalRefund > 0
          ? 'Booking berhasil dibatalkan.\n\nRefund sebesar ${_currencyFormat.format(totalRefund)} akan diproses dalam 14-30 hari kerja.'
          : 'Booking berhasil dibatalkan.';

      showDialog(
        context: context,
        barrierDismissible: false,
        builder: (ctx) {
          final isDark = Theme.of(ctx).brightness == Brightness.dark;
          return Dialog(
            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
            backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
            child: Padding(
              padding: const EdgeInsets.all(24),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  // Ulin Mahoni logo
                  Image.asset(
                    AppImage.logo,
                    width: 72,
                    height: 72,
                    fit: BoxFit.contain,
                  ),
                  const SizedBox(height: 16),
                  Text(
                    'Berhasil',
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.bold,
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                  ),
                  const SizedBox(height: 8),
                  Text(
                    message,
                    textAlign: TextAlign.center,
                    style: TextStyle(
                      fontSize: 14,
                      color: isDark ? Colors.white70 : Colors.black54,
                      height: 1.5,
                    ),
                  ),
                  const SizedBox(height: 24),
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton(
                      onPressed: () {
                        // Close success popup, then close cancel dialog with true
                        Navigator.of(ctx).pop();
                        Navigator.of(context).pop(true);
                      },
                      style: ElevatedButton.styleFrom(
                        backgroundColor: Colors.green,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 12),
                        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                      ),
                      child: const Text('OK'),
                    ),
                  ),
                ],
              ),
              ),
            );
          },
        );
    } else {
      // result is null — either provider was disposed (autoDispose race condition)
      // or the API returned an error. Check provider state for the actual error message.
      final cancelState = ref.read(cancelBookingProvider);
      final errorMsg = cancelState.hasError
          ? cancelState.error.toString()
          : 'Gagal membatalkan booking. Silakan coba lagi.';

      AppLogger.e('Cancel failed — provider state: $cancelState', errorMsg, null, 'CANCEL-DIALOG');

      setState(() {
        _isCancelling = false;
        _errorMessage = errorMsg;
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    // Watch the provider to keep the autoDispose provider alive while the dialog
    // is open — prevents it from being disposed during the async cancelBooking() call.
    ref.watch(cancelBookingProvider);

    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Dialog(
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
      backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
      child: ConstrainedBox(
        constraints: const BoxConstraints(maxWidth: 400, maxHeight: 600),
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Header
                Row(
                  children: [
                    Container(
                      padding: const EdgeInsets.all(8),
                      decoration: BoxDecoration(
                        color: Colors.red.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: const Icon(Icons.cancel_outlined, color: Colors.red, size: 24),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Text(
                        'Batalkan Booking',
                        style: TextStyle(
                          fontSize: 18,
                          fontWeight: AppFontWeight.bold,
                          color: isDark ? Colors.white : Colors.black87,
                        ),
                      ),
                    ),
                    IconButton(
                      icon: Icon(Icons.close, color: isDark ? Colors.white54 : Colors.grey),
                      onPressed: () => Navigator.of(context).pop(false),
                    ),
                  ],
                ),
                const SizedBox(height: 16),

                // Loading state
                if (_isLoadingPreview) ...[
                  const Center(
                    child: Padding(
                      padding: EdgeInsets.all(32),
                      child: CircularProgressIndicator(),
                    ),
                  ),
                ]
                // Error state
                else if (_errorMessage != null && _preview == null && widget.transactionStatus.toLowerCase() == 'paid') ...[
                  Container(
                    padding: const EdgeInsets.all(12),
                    decoration: BoxDecoration(
                      color: Colors.red.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Text(_errorMessage!, style: const TextStyle(color: Colors.red)),
                  ),
                ]
                // Content
                else ...[
                  // For pending/waiting — simple message
                  if (widget.transactionStatus.toLowerCase() != 'paid') ...[
                    Text(
                      'Apakah Anda yakin ingin membatalkan booking ${widget.orderId}?',
                      style: TextStyle(
                        fontSize: 14,
                        color: isDark ? Colors.white70 : Colors.black54,
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      'Booking belum dibayar sehingga tidak ada refund.',
                      style: TextStyle(
                        fontSize: 13,
                        color: isDark ? Colors.white54 : Colors.grey,
                      ),
                    ),
                  ]
                  // For paid — refund breakdown
                  else if (_preview != null) ...[
                    // Refund tier badge
                    Container(
                      width: double.infinity,
                      padding: const EdgeInsets.symmetric(vertical: 8, horizontal: 12),
                      decoration: BoxDecoration(
                        color: _tierColor(_preview!.refundPercentage).withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                        border: Border.all(color: _tierColor(_preview!.refundPercentage).withValues(alpha: 0.3)),
                      ),
                      child: Text(
                        '${_preview!.daysBeforeCheckin} hari sebelum check-in → ${_preview!.refundPercentage}% refund',
                        textAlign: TextAlign.center,
                        style: TextStyle(
                          fontWeight: AppFontWeight.semiBold,
                          color: _tierColor(_preview!.refundPercentage),
                          fontSize: 13,
                        ),
                      ),
                    ),
                    const SizedBox(height: 16),

                    // Breakdown
                    _buildRow('Refund Kamar (${_preview!.refundPercentage}%)',
                        _currencyFormat.format(_preview!.roomRefund), isDark),
                    _buildRow('Refund Deposit (100%)',
                        _currencyFormat.format(_preview!.depositRefund), isDark),
                    _buildRow('Refund Parkir (${_preview!.refundPercentage}%)',
                        _currencyFormat.format(_preview!.otherRefund), isDark),
                    const Divider(height: 24),
                    _buildRow('Total Refund',
                        _currencyFormat.format(_preview!.totalRefund), isDark,
                        isBold: true, valueColor: AppColors.primaryColor),

                    const SizedBox(height: 8),
                    Text(
                      'Waktu proses refund: 14-30 hari kerja',
                      style: TextStyle(fontSize: 12, color: isDark ? Colors.white38 : Colors.grey),
                    ),

                    // Bank account fields for QRIS/VA
                    if (_preview!.requiresBankAccount) ...[
                      const SizedBox(height: 16),
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          color: Colors.amber.withValues(alpha: 0.1),
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: Colors.amber.withValues(alpha: 0.3)),
                        ),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            const Text(
                              'Rekening tujuan refund (QRIS/VA)',
                              style: TextStyle(fontWeight: FontWeight.w600, color: Colors.amber, fontSize: 13),
                            ),
                            const SizedBox(height: 12),
                            _buildInput('Nama Bank', _bankNameController, 'BCA, Mandiri, BNI...', isDark),
                            const SizedBox(height: 8),
                            _buildInput('Nomor Rekening', _accountNoController, '1234567890', isDark,
                                keyboardType: TextInputType.number),
                            const SizedBox(height: 8),
                            _buildInput('Nama Pemilik Rekening', _accountHolderController, 'Nama lengkap', isDark),
                          ],
                        ),
                      ),
                    ],
                  ],

                  // Error message
                  if (_errorMessage != null) ...[
                    const SizedBox(height: 12),
                    Text(_errorMessage!, style: const TextStyle(color: Colors.red, fontSize: 13)),
                  ],

                  const SizedBox(height: 24),

                  // Action buttons
                  Row(
                    children: [
                      Expanded(
                        child: OutlinedButton(
                          onPressed: _isCancelling ? null : () => Navigator.of(context).pop(false),
                          style: OutlinedButton.styleFrom(
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                          ),
                          child: const Text('Tidak'),
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: ElevatedButton(
                          onPressed: _isCancelling ? null : _confirmCancel,
                          style: ElevatedButton.styleFrom(
                            backgroundColor: Colors.red,
                            foregroundColor: Colors.white,
                            padding: const EdgeInsets.symmetric(vertical: 12),
                            shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                          ),
                          child: _isCancelling
                              ? const SizedBox(
                                  width: 20,
                                  height: 20,
                                  child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white),
                                )
                              : const Text('Ya, Batalkan'),
                        ),
                      ),
                    ],
                  ),
                ],
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _buildRow(String label, String value, bool isDark,
      {bool isBold = false, Color? valueColor}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Flexible(
            child: Text(
              label,
              style: TextStyle(
                fontSize: 13,
                fontWeight: isBold ? AppFontWeight.bold : FontWeight.normal,
                color: isDark ? Colors.white70 : Colors.black87,
              ),
            ),
          ),
          Text(
            value,
            style: TextStyle(
              fontSize: isBold ? 15 : 13,
              fontWeight: isBold ? AppFontWeight.bold : AppFontWeight.semiBold,
              color: valueColor ?? (isDark ? Colors.white : Colors.black87),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildInput(String label, TextEditingController controller, String hint, bool isDark,
      {TextInputType? keyboardType}) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Text(label, style: TextStyle(fontSize: 12, color: isDark ? Colors.white54 : Colors.grey)),
        const SizedBox(height: 4),
        TextField(
          controller: controller,
          keyboardType: keyboardType,
          decoration: InputDecoration(
            hintText: hint,
            isDense: true,
            contentPadding: const EdgeInsets.symmetric(horizontal: 12, vertical: 10),
            border: OutlineInputBorder(borderRadius: BorderRadius.circular(6)),
          ),
          style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
        ),
      ],
    );
  }

  Color _tierColor(int percentage) {
    if (percentage >= 75) return Colors.green;
    if (percentage >= 50) return Colors.orange;
    if (percentage >= 25) return Colors.deepOrange;
    return Colors.red;
  }
}
