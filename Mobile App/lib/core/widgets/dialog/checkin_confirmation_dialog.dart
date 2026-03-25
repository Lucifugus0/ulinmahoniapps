import 'package:flutter/material.dart';
import 'package:image_picker/image_picker.dart';
import 'dart:io';
import '../../constants/appcolor_constants.dart';
import '../../utils/formatcurrency.dart';
import '../../utils/formatdate.dart';
import '../../utils/image_to_base64_utils.dart';
import '../../constants/app_asset_constants.dart';
import 'imagesourcedialog.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

/// Shows a check-in confirmation dialog with ID card upload
/// Returns true if confirmed, false if cancelled
Future<CheckInConfirmationResult?> showCheckInConfirmationDialog(
  BuildContext context, {
  required String propertyName,
  required String roomName,
  required String bookingType,
  required String checkIn,
  required String checkOut,
  required double roomPrice,
  required double serviceFee,
  required double grandTotal,
  required int duration,
  String? paymentProofBase64,
}) {
  return showDialog<CheckInConfirmationResult>(
    context: context,
    barrierDismissible: false,
    builder: (ctx) => _CheckInConfirmationDialog(
      propertyName: propertyName,
      roomName: roomName,
      bookingType: bookingType,
      checkIn: checkIn,
      checkOut: checkOut,
      roomPrice: roomPrice,
      serviceFee: serviceFee,
      grandTotal: grandTotal,
      duration: duration,
      paymentProofBase64: paymentProofBase64,
    ),
  );
}

class _CheckInConfirmationDialog extends StatefulWidget {
  final String propertyName;
  final String roomName;
  final String bookingType;
  final String checkIn;
  final String checkOut;
  final double roomPrice;
  final double serviceFee;
  final double grandTotal;
  final int duration;
  final String? paymentProofBase64;

  const _CheckInConfirmationDialog({
    required this.propertyName,
    required this.roomName,
    required this.bookingType,
    required this.checkIn,
    required this.checkOut,
    required this.roomPrice,
    required this.serviceFee,
    required this.grandTotal,
    required this.duration,
    this.paymentProofBase64,
  });

  @override
  State<_CheckInConfirmationDialog> createState() =>
      _CheckInConfirmationDialogState();
}

class _CheckInConfirmationDialogState
    extends State<_CheckInConfirmationDialog> {
  File? _idCardImage;
  bool _agreedToTerms = false;

  Future<void> _pickIdCardImage() async {
    final localizations = AppLocalizations.of(context)!;
    final result = await showImageSourceDialog(
      context,
      title: localizations.checkInDialogUploadIdCard,
      cameraButtonText: localizations.myBookingDetailTakePhoto,
      galleryButtonText: localizations.myBookingDetailPickFromGallery,
      cancelButtonText: localizations.cancelButtonLabel,
    );

    if (result != null) {
      final ImagePicker picker = ImagePicker();
      XFile? image;

      if (result == ImageSourceOption.camera) {
        image = await picker.pickImage(source: ImageSource.camera);
      } else if (result == ImageSourceOption.gallery) {
        image = await picker.pickImage(source: ImageSource.gallery);
      }

      if (image != null) {
        setState(() {
          _idCardImage = File(image!.path);
        });
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
      insetPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 24),
      child: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(20),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              // Close button at top right
              Align(
                alignment: Alignment.topRight,
                child: IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.of(context).pop(null),
                  padding: EdgeInsets.zero,
                  constraints: const BoxConstraints(),
                ),
              ),

              // Logo
              Image.asset(
                AppImage.logo,
                width: 80,
                height: 80,
                errorBuilder: (context, error, stackTrace) {
                  return const Icon(
                    Icons.login,
                    color: AppColors.primaryColor,
                    size: 80,
                  );
                },
              ),
              const SizedBox(height: 16),

              // Title
              Text(
                localizations.checkInDialogTitle,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black87,
                ),
              ),
              const SizedBox(height: 24),

              // ID Card Upload Section
              Align(
                alignment: Alignment.centerLeft,
                child: Text(
                  localizations.checkInDialogIdCardSection,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
              ),
              const SizedBox(height: 12),
              GestureDetector(
                onTap: _pickIdCardImage,
                child: Container(
                  width: double.infinity,
                  height: 180,
                  decoration: BoxDecoration(
                    color: isDark ? const Color(0xFF374151) : Colors.grey[100],
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      color: _idCardImage == null
                          ? Colors.grey[400]!
                          : AppColors.primaryColor,
                      width: 2,
                    ),
                  ),
                  child: _idCardImage == null
                      ? Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Icon(Icons.cloud_upload,
                                size: 48, color: Colors.grey[600]),
                            const SizedBox(height: 8),
                            Text(
                              localizations.checkInDialogUploadIdCard,
                              style: TextStyle(
                                color: Colors.grey[700],
                                fontSize: 14,
                              ),
                            ),
                            const SizedBox(height: 4),
                            Text(
                              localizations.checkInDialogTapToUpload,
                              style: TextStyle(
                                color: Colors.grey[500],
                                fontSize: 12,
                              ),
                            ),
                          ],
                        )
                      : ClipRRect(
                          borderRadius: BorderRadius.circular(10),
                          child: Stack(
                            children: [
                              Positioned.fill(
                                child: Image.file(
                                  _idCardImage!,
                                  fit: BoxFit.cover,
                                ),
                              ),
                              Positioned(
                                top: 8,
                                right: 8,
                                child: Container(
                                  decoration: BoxDecoration(
                                    color: Colors.black54,
                                    borderRadius: BorderRadius.circular(20),
                                  ),
                                  padding: const EdgeInsets.all(4),
                                  child: const Icon(
                                    Icons.check_circle,
                                    color: Colors.white,
                                    size: 20,
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                ),
              ),
              const SizedBox(height: 20),

              // Booking Details Section
              Align(
                alignment: Alignment.centerLeft,
                child: Text(
                  localizations.checkInDialogBookingDetails,
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                    color: isDark ? Colors.white : Colors.black87,
                  ),
                ),
              ),
              const SizedBox(height: 12),
              Container(
                padding: const EdgeInsets.all(16),
                decoration: BoxDecoration(
                  color: isDark ? const Color(0xFF374151) : Colors.grey[50],
                  borderRadius: BorderRadius.circular(12),
                  border: Border.all(color: isDark ? Colors.grey[600]! : Colors.grey[300]!),
                ),
                child: Column(
                  children: [
                    _buildDetailRow(
                        localizations.confirmationDialogProperty,
                        widget.propertyName),
                    const Divider(height: 20),
                    _buildDetailRow(
                        localizations.confirmationDialogRoom, widget.roomName),
                    const Divider(height: 20),
                    _buildDetailRow(
                      localizations.confirmationDialogRentType,
                      widget.bookingType == 'daily'
                          ? localizations.dailyRentType
                          : localizations.monthlyRentType,
                    ),
                    const Divider(height: 20),
                    _buildDetailRow(
                      localizations.confirmationDialogDuration,
                      widget.bookingType == 'daily'
                          ? '${widget.duration} ${localizations.dailyDurationUnit}'
                          : '${widget.duration} ${localizations.monthlyDurationUnit}',
                    ),
                    const Divider(height: 20),
                    _buildDetailRow(localizations.confirmationDialogCheckInDate,
                        formatDate(widget.checkIn) ?? '-'),
                    const Divider(height: 20),
                    _buildDetailRow(
                        localizations.confirmationDialogCheckOutDate,
                        formatDate(widget.checkOut) ?? '-'),
                    const Divider(height: 20),
                    _buildDetailRow(
                      widget.bookingType == 'daily'
                          ? localizations.confirmationDialogDailyPrice
                          : localizations.confirmationDialogMonthlyPrice,
                      formatCurrency(widget.roomPrice) ?? '0',
                    ),
                    const Divider(height: 20),
                    _buildDetailRow(
                      localizations.myBookingDetailServiceFee,
                      formatCurrency(widget.serviceFee) ?? '0',
                    ),
                    const Divider(height: 20),
                    _buildDetailRow(
                      localizations.confirmationDialogTotalPrice,
                      formatCurrency(widget.grandTotal) ?? '0',
                      isBold: true,
                      valueColor: AppColors.primaryColor,
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 20),

              // Terms and Conditions Checkbox
              Container(
                padding: const EdgeInsets.all(12),
                decoration: BoxDecoration(
                  color: Colors.amber[50],
                  borderRadius: BorderRadius.circular(8),
                  border: Border.all(color: Colors.amber[200]!),
                ),
                child: Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Checkbox(
                      value: _agreedToTerms,
                      onChanged: (value) {
                        setState(() {
                          _agreedToTerms = value ?? false;
                        });
                      },
                      activeColor: AppColors.primaryColor,
                    ),
                    Expanded(
                      child: Padding(
                        padding: const EdgeInsets.only(top: 12),
                        child: Text(
                          localizations.checkInDialogTermsAgreement,
                          style: const TextStyle(
                            fontSize: 13,
                            color: Colors.black87,
                          ),
                        ),
                      ),
                    ),
                  ],
                ),
              ),
              const SizedBox(height: 24),

              // Action Buttons
              Row(
                children: [
                  Expanded(
                    child: OutlinedButton(
                      onPressed: () => Navigator.of(context).pop(null),
                      style: OutlinedButton.styleFrom(
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        side: BorderSide(color: Colors.grey[400]!),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      child: Text(
                        localizations.confirmationDialogCancelButton,
                        style: TextStyle(
                          color: isDark ? Colors.grey[300] : Colors.black87,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),
                  Expanded(
                    child: ElevatedButton(
                      onPressed: (_idCardImage != null && _agreedToTerms)
                          ? () async {
                              if (_idCardImage != null) {
                                try {
                                  final base64String = await ImageToBase64Utils.fileToBase64(_idCardImage!);
                                  if (context.mounted) {
                                    Navigator.of(context).pop(
                                      CheckInConfirmationResult(
                                        confirmed: true,
                                        idCardBase64: base64String,
                                      ),
                                    );
                                  }
                                } catch (e) {
                                  if (context.mounted) {
                                    ScaffoldMessenger.of(context).showSnackBar(
                                      SnackBar(
                                        content: Text('Failed to process image: $e'),
                                        backgroundColor: Colors.red,
                                      ),
                                    );
                                  }
                                }
                              }
                            }
                          : null,
                      style: ElevatedButton.styleFrom(
                        backgroundColor: AppColors.primaryColor,
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 14),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(8),
                        ),
                        disabledBackgroundColor: Colors.grey[300],
                      ),
                      child: Text(
                        localizations.checkInDialogConfirmButton,
                        style: const TextStyle(
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

  Widget _buildDetailRow(
    String label,
    String value, {
    bool isBold = false,
    Color? valueColor,
  }) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Expanded(
          flex: 2,
          child: Text(
            label,
            style: TextStyle(
              fontSize: 13,
              color: isDark ? Colors.grey[400] : Colors.black54,
            ),
          ),
        ),
        Expanded(
          flex: 3,
          child: Text(
            value,
            style: TextStyle(
              fontSize: 13,
              fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
              color: valueColor ?? (isDark ? Colors.grey[200] : Colors.black87),
            ),
            textAlign: TextAlign.right,
          ),
        ),
      ],
    );
  }
}

/// Result class for check-in confirmation
class CheckInConfirmationResult {
  final bool confirmed;
  final String? idCardBase64;

  CheckInConfirmationResult({
    required this.confirmed,
    this.idCardBase64,
  });
}
