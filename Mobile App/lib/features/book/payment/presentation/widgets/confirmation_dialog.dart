import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/utils/formatcurrency.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class ConfirmationDialog extends StatefulWidget {
  final Map<String, dynamic> roomData;
  final List<Map<String, dynamic>> itemDetails;
  final String rentType;
  final int duration;
  final VoidCallback onConfirm;
  final double totalHarga;
  final String? voucherCode;
  final double? voucherDiscount;
  final double? originalTotal;
  final double? depositFee;
  final double? parkingFee;
  final String? parkingType;
  final int? parkingDuration;

  const ConfirmationDialog({
    super.key,
    required this.roomData,
    required this.itemDetails,
    required this.rentType,
    required this.duration,
    required this.onConfirm,
    required this.totalHarga,
    this.voucherCode,
    this.voucherDiscount,
    this.originalTotal,
    this.depositFee,
    this.parkingFee,
    this.parkingType,
    this.parkingDuration,
  });

  @override
  State<ConfirmationDialog> createState() => _ConfirmationDialogState();
}

class _ConfirmationDialogState extends State<ConfirmationDialog> {
  final ScrollController _scrollController = ScrollController();

  @override
  void dispose() {
    _scrollController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final textTheme = Theme.of(context).textTheme;
    // Dark mode detection for dialog background
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return AlertDialog(
      backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      contentPadding: const EdgeInsets.all(24),
      title: Column(
        children: [
          Image.asset(AppImage.logo, height: 60),
          const SizedBox(height: 16),
          Text(
            localizations.confirmationDialogTitle,
            style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
          ),
        ],
      ),
      content: Theme(
        data: Theme.of(context).copyWith(
          scrollbarTheme: ScrollbarThemeData(
            thumbColor: WidgetStateProperty.all(Colors.grey.shade400),
            trackColor: WidgetStateProperty.all(Colors.grey.shade200),
            thickness: WidgetStateProperty.all(6.0),
            radius: const Radius.circular(10),
          ),
        ),
        child: Scrollbar(
          controller: _scrollController,
          thumbVisibility: true,
          trackVisibility: true,
          child: SingleChildScrollView(
            controller: _scrollController,
            child: Padding(
              padding: const EdgeInsets.only(right: 20.0),
              child: ListBody(
                children: <Widget>[
                const Divider(),
                Text(
                  localizations.confirmationDialogBookingDetails,
                  style: textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),
                _buildDetailRow(
                  localizations.confirmationDialogProperty,
                  widget.roomData['propertyName']?.toString() ?? '-',
                ),
                _buildDetailRow(
                  localizations.confirmationDialogRoom,
                  widget.roomData['name']?.toString() ?? '-',
                ),
                _buildDetailRow(
                  localizations.confirmationDialogRentType,
                  widget.rentType.toString(),
                ),
                _buildDetailRow(
                  localizations.confirmationDialogDuration,
                  localizations.paymentDurationValue(
                    widget.duration,
                    widget.rentType == 'daily' || widget.rentType == 'Daily'
                        ? 'daily'
                        : 'monthly',
                  ),
                ),
                _buildDetailRow(
                  localizations.confirmationDialogCheckInDate,
                  widget.roomData['checkIn']?.toString() ?? '-',
                ),
                _buildDetailRow(
                  localizations.confirmationDialogCheckOutDate,
                  widget.roomData['checkOut']?.toString() ?? '-',
                ),
                const Divider(),
                Text(
                  localizations.confirmationDialogPriceDetails,
                  style: textTheme.titleMedium?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                const SizedBox(height: 8),

                // 1. Harga Bulanan/Harian
                _buildPriceRow(
                  widget.rentType == 'daily' || widget.rentType == 'Daily'
                      ? localizations.confirmationDialogDailyPrice
                      : localizations.confirmationDialogMonthlyPrice,
                  widget.rentType == 'daily' || widget.rentType == 'Daily'
                      ? widget.roomData['daily_price'] ?? 0.0
                      : widget.roomData['monthly_price'] ?? 0.0,
                ),

                // 2. Durasi
                _buildDetailRow(
                  localizations.confirmationDialogDuration,
                  localizations.paymentDurationValue(
                    widget.duration,
                    widget.rentType == 'daily' || widget.rentType == 'Daily' ? 'daily' : 'monthly',
                  ),
                ),

                const Divider(height: 16, thickness: 0.5, color: Colors.grey),

                // 3. Subtotal
                Builder(
                  builder: (context) {
                    final basePrice = widget.rentType == 'daily' || widget.rentType == 'Daily'
                        ? widget.roomData['daily_price'] ?? 0.0
                        : widget.roomData['monthly_price'] ?? 0.0;
                    final subtotal = basePrice * widget.duration;

                    return _buildPriceRow(
                      'Subtotal',
                      subtotal,
                      isBold: true,
                    );
                  },
                ),

                // 4. Voucher Discount (if applied)
                if (widget.voucherCode != null && widget.voucherDiscount != null && widget.voucherDiscount! > 0) ...[
                  _buildPriceRow(
                    '${localizations.paymentVoucherTitle} (${widget.voucherCode})',
                    -widget.voucherDiscount!,
                    // Use primaryAdaptive for the voucher discount row color
                    color: AppColors.primaryAdaptive(context),
                  ),
                ],

                const Divider(height: 16, thickness: 0.5, color: Colors.grey),

                // 5. Service Fee only (filter by type='tax')
                ...widget.itemDetails.where((item) {
                  String itemType = item['type']?.toString() ?? '';
                  return itemType == 'tax'; // Only show tax/service fee
                }).map((item) {
                  String priceString = item['price']?.toString() ?? '0';
                  String cleanPriceString = priceString.replaceAll(RegExp(r'[^\d]'), '');
                  double priceValue = double.tryParse(cleanPriceString) ?? 0.0;

                  return _buildPriceRow(
                    item['name']?.toString() ?? localizations.confirmationDialogFee,
                    priceValue,
                  );
                }),

                // 6. Deposit Fee (only for monthly bookings)
                if ((widget.rentType.toLowerCase() == 'monthly') &&
                    widget.depositFee != null &&
                    widget.depositFee! > 0) ...[
                  _buildPriceRow(
                    'Deposit',
                    widget.depositFee!.toDouble(),
                  ),
                ],

                // 7. Parking Fee (only for monthly bookings)
                if ((widget.rentType.toLowerCase() == 'monthly') &&
                    widget.parkingFee != null &&
                    widget.parkingFee! > 0) ...[
                  _buildPriceRow(
                    widget.parkingDuration != null && widget.parkingDuration! > 0
                        ? 'Parkir ${widget.parkingType ?? ''} (${widget.parkingDuration} bulan)'
                        : 'Parkir ${widget.parkingType ?? ''}',
                    widget.parkingFee!.toDouble(),
                  ),
                ],

                const Divider(height: 24, thickness: 1),
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Text(
                      localizations.confirmationDialogTotalPrice,
                      style: textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    Text(
                      formatCurrency(widget.totalHarga),
                      style: textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: AppColors.secondaryColor,
                        fontSize: 15,
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 24),
                Text(
                  localizations.confirmationDialogConfirmationMessage,
                  style: textTheme.bodyLarge,
                  textAlign: TextAlign.center,
                ),
              ],
            ),
          ),
        ),
      ),
      ),
      actions: <Widget>[
        TextButton(
          onPressed: () => Navigator.of(context).pop(),
          style: TextButton.styleFrom(foregroundColor: Colors.red),
          child: Text(localizations.confirmationDialogCancelButton),
        ),
        ElevatedButton(
          onPressed: () {
            widget.onConfirm();
            Navigator.of(context).pop();
          },
          style: ElevatedButton.styleFrom(
            // Use primaryAdaptive for the confirm button background
            backgroundColor: AppColors.primaryAdaptive(context),
            foregroundColor: Colors.white,
          ),
          child: Text(localizations.confirmationDialogConfirmButton),
        ),
      ],
    );
  }

  Widget _buildDetailRow(String title, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        // PENTING: Agar jika teksnya 2 baris, judul tetap di posisi atas (tidak di tengah vertikal)
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          // Widget Judul (Kiri)
          Text(title, style: const TextStyle(fontWeight: FontWeight.w500)),

          // Opsional: Beri jarak sedikit agar tidak pernah menempel ketat
          const SizedBox(width: 16),

          // PENTING: Bungkus value dengan Expanded
          Expanded(
            child: Text(
              value,
              // PENTING: Agar teks tetap terlihat menempel di kanan
              textAlign: TextAlign.right,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildPriceRow(String title, double price, {Color? color, bool isBold = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 8.0),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(
            title,
            style: color != null
                ? TextStyle(color: color, fontWeight: FontWeight.bold)
                : isBold
                    ? const TextStyle(fontWeight: FontWeight.w600)
                    : null,
          ),
          Text(
            formatCurrency(price),
            style: color != null
                ? TextStyle(color: color, fontWeight: FontWeight.bold)
                : isBold
                    ? const TextStyle(fontWeight: FontWeight.w600)
                    : null,
          ),
        ],
      ),
    );
  }
}
