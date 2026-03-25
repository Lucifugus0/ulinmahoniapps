import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter/rendering.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'dart:async';
import 'dart:convert';
import 'dart:io';
import 'dart:typed_data';
import 'package:image_picker/image_picker.dart';
import '../../../../../core/widgets/appbar.dart';
import '../../../../../core/layout/mainlayout.dart';
import '../../../../../core/utils/formatdate.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../widgets/components/info.dart';
import '../widgets/components/section_title.dart';
import '../widgets/section/imageattachment.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../provider/mybookingdetails_provider.dart';
import '../../../../../core/utils/imageconverter_utils.dart';
import '../../../../../core/widgets/button/customiconbutton_components.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/widgets/dialog/imagesourcedialog.dart';
import '../../../../../core/widgets/dialog/checkin_confirmation_dialog.dart';
import '../../model/roomimages_model.dart';
import '../../../mybooking/provider/mybooking_provider.dart';
import '../../../mybooking/model/mybooking_model.dart';
import '../../provider/updateattachment_provider.dart';
import '../../provider/checkin_provider.dart';
import '../../../../../core/network/api_result.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';
import '../widgets/renew_booking_dialog.dart';
import 'package:go_router/go_router.dart';
import 'package:qr_flutter/qr_flutter.dart';
import 'package:gal/gal.dart';
import 'package:path_provider/path_provider.dart';
import 'package:permission_handler/permission_handler.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../../core/utils/payment_cache_utils.dart';
import 'dart:ui' as ui;

class MyBookingDetail extends ConsumerStatefulWidget {
  final Map<String, dynamic> bookingData;

  const MyBookingDetail({Key? key, required this.bookingData}) : super(key: key);

  @override
  ConsumerState<MyBookingDetail> createState() => _MyBookingDetailState();
}

class _MyBookingDetailState extends ConsumerState<MyBookingDetail> {
  File? selectedImage;
  Uint8List? _decodedAttachmentImageBytes;

  final StateProvider<AsyncValue<bool>> _uploadStatusProvider = StateProvider<AsyncValue<bool>>((ref) => const AsyncData(false));

  // Payment cache state
  Map<String, String>? _cachedQRData;
  Map<String, String>? _cachedCCData;
  Timer? _countdownTimer;
  Duration _remainingTime = Duration.zero;
  final GlobalKey _qrKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    _loadPaymentCacheData();
  }

  @override
  void dispose() {
    _countdownTimer?.cancel();
    super.dispose();
  }

  void _updateDecodedAttachmentImageBytes(String? attachmentBase64) {
    if (attachmentBase64 != null && attachmentBase64.isNotEmpty) {
      try {
        _decodedAttachmentImageBytes = base64Decode(attachmentBase64);
      } catch (e) {
        _decodedAttachmentImageBytes = null;
      }
    } else {
      _decodedAttachmentImageBytes = null;
    }
  }

  Future<void> _uploadImage(int bookingIdrec, MyBookingModel? currentBookingData) async {
    final localizations = AppLocalizations.of(context)!;
    if (selectedImage == null) {
      showNotificationDialog(
        context,
        localizations.myBookingDetailSelectImageFirst,
        defaultIcon: Icons.info_outline,
      );
      return;
    }

    ref.read(_uploadStatusProvider.notifier).state = const AsyncLoading();

    try {
      List<int> imageBytes = await selectedImage!.readAsBytes();
      String base64Image = base64Encode(imageBytes);

      final repository = ref.read(updateAttachmentRepositoryProvider);
      final result = await repository.updateAttachmentById(bookingIdrec, base64Image);

      switch (result) {
        case Success(:final data):
          ref.read(_uploadStatusProvider.notifier).state = const AsyncData(true);
          showNotificationDialog(
            context,
            localizations.myBookingDetailUploadSuccess,
            defaultIcon: Icons.check_circle_outline,
            iconColor: AppColors.primaryColor,
          );

          ref.invalidate(myBookingByIdProvider(bookingIdrec));
          ref.invalidate(userBookingsProvider);

          setState(() {
            selectedImage = null;
          });

        case Failure(:final message):
          ref.read(_uploadStatusProvider.notifier).state = AsyncError(message, StackTrace.current);
          showNotificationDialog(
            context,
            '${localizations.myBookingDetailUploadFailed}: $message',
            defaultIcon: Icons.error_outline,
            iconColor: Colors.red,
          );
      }
    } catch (e, stackTrace) {
      ref.read(_uploadStatusProvider.notifier).state = AsyncError(e, stackTrace);
      showNotificationDialog(
        context,
        '${localizations.myBookingDetailUploadFailed}: $e',
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
    }
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

  /// Load QR content or CC payment link from cache
  Future<void> _loadPaymentCacheData() async {
    final bookingData = widget.bookingData;
    final transactionType = (bookingData['transaction_type'] as String?)?.toUpperCase();
    final transactionStatus = (bookingData['transaction_status'] as String?)?.toLowerCase();
    final userId = bookingData['user_id']?.toString() ?? '';
    final orderId = bookingData['order_id'] as String? ?? '';
    final bookingId = bookingData['idrec']?.toString() ?? '';

    // Only load if status is pending
    if (transactionStatus != 'pending') {
      return;
    }

    // Load QRIS QR content
    if (transactionType == 'QRIS') {
      final qrData = await PaymentCacheUtils.getQRContent(
        bookingId: bookingId,
        currentOrderId: orderId,
        currentUserId: userId,
      );

      if (qrData != null && mounted) {
        setState(() {
          _cachedQRData = qrData;
        });
        _startCountdownTimer(qrData['expiredAt']!);
      }
    }

    // Load Credit Card link
    if (transactionType == 'CREDIT CARD' || transactionType == 'CREDITCARD') {
      final ccData = await PaymentCacheUtils.getCCLink(
        bookingId: bookingId,
        currentUserId: userId,
      );

      if (ccData != null && mounted) {
        setState(() {
          _cachedCCData = ccData;
        });
        _startCountdownTimer(ccData['expiredAt']!);
      }
    }

    // For all other pending payment types (VA, Transfer Manual, etc.)
    // that have no specific expiry stored, start a flat 15-minute countdown
    if (mounted && _remainingTime == Duration.zero) {
      _startCountdownTimer(
        DateTime.now().add(const Duration(minutes: 15)).toIso8601String(),
      );
    }
  }

  /// Start countdown timer for QR/CC expiry
  void _startCountdownTimer(String expiredAtString) {
    final expiredAt = DateTime.parse(expiredAtString);
    _remainingTime = expiredAt.difference(DateTime.now());

    if (_remainingTime.isNegative) {
      _remainingTime = Duration.zero;
      return;
    }

    _countdownTimer = Timer.periodic(const Duration(seconds: 1), (timer) {
      if (!mounted) {
        timer.cancel();
        return;
      }

      setState(() {
        _remainingTime = expiredAt.difference(DateTime.now());

        if (_remainingTime.isNegative || _remainingTime.inSeconds <= 0) {
          _remainingTime = Duration.zero;
          timer.cancel();
          _cachedQRData = null;
          _cachedCCData = null;
        }
      });
    });
  }

  /// Download QR code as PNG to gallery
  Future<void> _downloadQRCode() async {
    final localizations = AppLocalizations.of(context)!;

    try {
      // Request storage permission
      final status = await Permission.photos.request();
      if (!status.isGranted) {
        if (mounted) {
          showNotificationDialog(
            context,
            localizations.downloadQRPermissionDenied ?? 'Permission denied to save images',
            defaultIcon: Icons.error_outline,
            iconColor: Colors.red,
          );
        }
        return;
      }

      // Capture QR code as image
      final boundary = _qrKey.currentContext?.findRenderObject() as RenderRepaintBoundary?;
      if (boundary == null) {
        throw Exception('Failed to get QR code render object');
      }

      final image = await boundary.toImage(pixelRatio: 3.0);
      final byteData = await image.toByteData(format: ui.ImageByteFormat.png);
      final pngBytes = byteData?.buffer.asUint8List();

      if (pngBytes == null) {
        throw Exception('Failed to convert QR code to PNG');
      }

      // Save to gallery using gal package (replaces deprecated image_gallery_saver)
      final tempDir = await getTemporaryDirectory();
      final filePath = '${tempDir.path}/ulinmahoni_qr_${DateTime.now().millisecondsSinceEpoch}.png';
      final file = File(filePath);
      await file.writeAsBytes(pngBytes);
      await Gal.putImage(filePath);

      if (mounted) {
        showNotificationDialog(
          context,
          localizations.downloadQRSuccess ?? 'QR code saved to gallery',
          defaultIcon: Icons.check_circle_outline,
          iconColor: AppColors.primaryColor,
        );
      }
    } catch (e) {
      AppLogger.e('Failed to download QR code', e, StackTrace.current, 'MYBOOKING-DETAILS');
      if (mounted) {
        showNotificationDialog(
          context,
          '${localizations.downloadQRFailed ?? 'Failed to save QR code'}: $e',
          defaultIcon: Icons.error_outline,
          iconColor: Colors.red,
        );
      }
    }
  }

  /// Helper function to check if transaction status indicates payment is complete
  ///
  /// Valid paid status:
  /// - Paid (ONLY)
  ///
  /// Invalid statuses:
  /// - Pending (belum bayar)
  /// - Waiting (menunggu konfirmasi admin)
  /// - Cancelled (dibatalkan)
  /// - Rejected (ditolak)
  bool _isTransactionPaid(String? transactionStatus) {
    if (transactionStatus == null || transactionStatus.isEmpty) {
      return false;
    }

    final status = transactionStatus.toLowerCase().trim();

    // Only 'paid' is considered as paid status
    return status == 'paid';
  }

  Future<void> _onRefresh(int bookingIdrec, int? roomId) async {
    ref.invalidate(myBookingByIdProvider(bookingIdrec));
    if (roomId != null) {
      ref.read(roomImageProvider(roomId).notifier).refreshRoomImage();
    }
    ref.invalidate(userBookingsProvider);
  }

  Future<void> _pickImageFromGallery(int bookingIdrec, MyBookingModel? currentBookingData) async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.gallery);
    if (image != null) {
      setState(() {
        selectedImage = File(image.path);
      });
      _uploadImage(bookingIdrec, currentBookingData);
    }
  }

  Future<void> _takePhotoWithCamera(int bookingIdrec, MyBookingModel? currentBookingData) async {
    final ImagePicker picker = ImagePicker();
    final XFile? image = await picker.pickImage(source: ImageSource.camera);
    if (image != null) {
      setState(() {
        selectedImage = File(image.path);
      });
      _uploadImage(bookingIdrec, currentBookingData);
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final int bookingIdrec = widget.bookingData['idrec'] as int;
    final AsyncValue<MyBookingModel?> bookingDataAsync = ref.watch(myBookingByIdProvider(bookingIdrec));

    return MainLayout(
      currentIndex: 1,
      showNavBar: false,
      showBottomNav: false,
      child: Stack(
        children: [
          bookingDataAsync.when(
            data: (bookingData) {
              if (bookingData == null) {
                return Center(child: Text(localizations.myBookingDetailDataNotFound)); 
              }
              _updateDecodedAttachmentImageBytes(bookingData.attachment);
              final int? roomId = bookingData.roomId;
              final AsyncValue<RoomImageModel?> roomImageAsyncValue =
              (roomId != null) ? ref.watch(roomImageProvider(roomId)) : const AsyncValue.data(null);
              final AsyncValue<bool> attachmentUploadStatus = ref.watch(_uploadStatusProvider);
              return RefreshIndicator(
                onRefresh: () => _onRefresh(bookingIdrec, roomId),
                child: SafeArea(
                  child: SingleChildScrollView(
                    physics: const AlwaysScrollableScrollPhysics(),
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        SizedBox(
                          height: 400,
                          child: Stack(
                            children: [
                              Positioned.fill(
                                child: roomImageAsyncValue.when(
                                  data: (roomImageModel) {
                                    return ImageConverter.convertStringToImageWidget(
                                      roomImageModel?.imageShow,
                                      width: double.infinity,
                                      height: double.infinity,
                                      fit: BoxFit.cover,
                                      alignment: Alignment.topCenter,
                                    );
                                  },
                                  loading: () => Skeletonizer(
                                    enabled: true,
                                    child: Container(
                                      color: Colors.grey[200],
                                      width: double.infinity,
                                      height: double.infinity,
                                    ),
                                  ),
                                  error: (err, stack) => Container(
                                    color: AppColors.secondaryColor,
                                    child: const Center(child: Icon(Icons.error, color: Colors.red, size: 50)),
                                  ),
                                ),
                              ),
                              Positioned.fill(
                                child: Container(
                                  decoration: BoxDecoration(
                                    gradient: LinearGradient(
                                      begin: Alignment.topCenter,
                                      end: Alignment.bottomCenter,
                                      colors: [Colors.black.withOpacity(0.0), Colors.black.withOpacity(0.5)],
                                    ),
                                  ),
                                ),
                              ),
                              Positioned(
                                bottom: 60,
                                left: 20,
                                right: 20,
                                child: Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  mainAxisSize: MainAxisSize.min,
                                  children: [
                                    Text(
                                      bookingData.propertyName ?? localizations.myBookingDetailPropertyNameDefault, 
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 24,
                                        fontWeight: FontWeight.bold,
                                        shadows: [Shadow(color: Colors.black, blurRadius: 4)],
                                      ),
                                    ),
                                    const SizedBox(height: 4),
                                    Text(
                                      bookingData.roomNo != null && bookingData.roomNo!.isNotEmpty
                                          ? '${bookingData.roomName ?? localizations.myBookingDetailRoomNameDefault} - No. ${bookingData.roomNo}'
                                          : bookingData.roomName ?? localizations.myBookingDetailRoomNameDefault,
                                      style: const TextStyle(
                                        color: Colors.white,
                                        fontSize: 16,
                                        shadows: [Shadow(color: Colors.black, blurRadius: 4)],
                                      ),
                                    ),
                                    // Status Bubbles
                                    Builder(
                                      builder: (context) {
                                        final hasCheckedIn = bookingData.checked_in_at != null && bookingData.checked_in_at!.isNotEmpty;
                                        final isRenewed = bookingData.renewalStatus == 1;
                                        AppLogger.d('=== STATUS BUBBLE DEBUG ===', 'MYBOOKING-DETAIL');
                                        AppLogger.d('checked_in_at: ${bookingData.checked_in_at}', 'MYBOOKING-DETAIL');
                                        AppLogger.d('hasCheckedIn: $hasCheckedIn', 'MYBOOKING-DETAIL');
                                        AppLogger.d('renewalStatus: ${bookingData.renewalStatus}', 'MYBOOKING-DETAIL');

                                        if (hasCheckedIn) {
                                          return Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              const SizedBox(height: 6),
                                              Row(
                                                mainAxisSize: MainAxisSize.min,
                                                children: [
                                                  // Check-in bubble (always shows when checked in)
                                                  Container(
                                                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                    decoration: BoxDecoration(
                                                      color: Colors.green.withValues(alpha: 0.9),
                                                      borderRadius: BorderRadius.circular(12),
                                                    ),
                                                    child: Text(
                                                      Localizations.localeOf(context).languageCode == 'id' ? 'Check-in' : 'Checked-in',
                                                      style: const TextStyle(
                                                        color: Colors.white,
                                                        fontSize: 11,
                                                        fontWeight: FontWeight.w600,
                                                      ),
                                                    ),
                                                  ),
                                                  // Diperpanjang bubble (shows when renewed)
                                                  if (isRenewed) ...[
                                                    const SizedBox(width: 6),
                                                    Container(
                                                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                                      decoration: BoxDecoration(
                                                        color: Colors.blue.withValues(alpha: 0.9),
                                                        borderRadius: BorderRadius.circular(12),
                                                      ),
                                                      child: Text(
                                                        Localizations.localeOf(context).languageCode == 'id' ? 'Diperpanjang' : 'Extended',
                                                        style: const TextStyle(
                                                          color: Colors.white,
                                                          fontSize: 11,
                                                          fontWeight: FontWeight.w600,
                                                        ),
                                                      ),
                                                    ),
                                                  ],
                                                ],
                                              ),
                                            ],
                                          );
                                        }
                                        return const SizedBox.shrink();
                                      },
                                    ),
                                  ],
                                ),
                              ),
                            ],
                          ),
                        ),
                        Transform.translate(
                          offset: const Offset(0, -50),
                          child: Container(
                            margin: const EdgeInsets.only(
                              left: 16,
                              right: 16,
                              bottom: 90,
                            ),
                            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 24),
                            decoration: BoxDecoration(
                              color: isDark ? AppColors.surfaceDark : Colors.white,
                              borderRadius: BorderRadius.circular(24),
                              boxShadow: [
                                BoxShadow(
                                  color: Colors.black.withValues(alpha: isDark ? 0.3 : 0.12),
                                  blurRadius: 16,
                                  offset: const Offset(0, 4),
                                ),
                              ],
                            ),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                // Warning banner: Only show for Pending status (not Waiting, Cancelled, Rejected)
                                if (bookingData.transactionType == 'BRI Manual'
                                    && bookingData.transactionStatus?.toLowerCase().trim() == 'pending'
                                    && (_decodedAttachmentImageBytes == null || _decodedAttachmentImageBytes!.isEmpty))
                                  Container(
                                    margin: const EdgeInsets.only(bottom: 20),
                                    padding: const EdgeInsets.all(16),
                                    decoration: BoxDecoration(
                                      color: Colors.amber.shade100,
                                      borderRadius: BorderRadius.circular(12),
                                      border: Border.all(color: Colors.amber.shade700, width: 1),
                                    ),
                                    child: Row(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        Icon(Icons.warning_amber_rounded, color: Colors.amber.shade800, size: 28),
                                        const SizedBox(width: 12),
                                        Expanded(
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Text(
                                                localizations.myBookingDetailPaymentProofWarningTitle,
                                                style: const TextStyle(
                                                  fontWeight: FontWeight.bold,
                                                  fontSize: 15,
                                                  color: Colors.black87,
                                                ),
                                              ),
                                              const SizedBox(height: 4),
                                              Text(
                                                localizations.myBookingDetailPaymentProofWarningMessage,
                                                style: const TextStyle(fontSize: 13, color: Colors.black87),
                                              ),
                                            ],
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                // 15-minute countdown — shown for all pending bookings, above Detail Pemesanan
                                if (bookingData.transactionStatus?.toLowerCase().trim() == 'pending' && _remainingTime.inSeconds > 0)
                                  Container(
                                    width: double.infinity,
                                    margin: const EdgeInsets.only(bottom: 20),
                                    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 10),
                                    decoration: BoxDecoration(
                                      color: Colors.orange.shade50,
                                      borderRadius: BorderRadius.circular(8),
                                      border: Border.all(color: Colors.orange.shade200),
                                    ),
                                    child: Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        Icon(Icons.timer, size: 18, color: Colors.orange.shade700),
                                        const SizedBox(width: 8),
                                        Text(
                                          '${localizations.expiresIn ?? 'Expires in'}: ${_remainingTime.inMinutes}:${(_remainingTime.inSeconds % 60).toString().padLeft(2, '0')}',
                                          style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.w600,
                                            color: Colors.orange.shade700,
                                          ),
                                        ),
                                      ],
                                    ),
                                  ),
                                sectionTitle(context, localizations.myBookingDetailTitle),
                                info(context, localizations.myBookingDetailOrderId, bookingData.orderId ?? "-"),
                                info(context, localizations.myBookingDetailPhoneNumber, bookingData.userPhoneNumber ?? "-"),
                                info(
                                  context,
                                  localizations.myBookingDetailBookingType,
                                  bookingData.bookingType == 'daily'
                                    ? localizations.dailyRentType
                                    : localizations.monthlyRentType,
                                ),
                                info(
                                  context,
                                  localizations.myBookingDetailDuration,
                                  bookingData.bookingType == 'daily'
                                      ? '${bookingData.bookingDays?.toString() ?? "0"} ${localizations.dailyDurationUnit}'
                                      : '${bookingData.bookingMonths?.toString() ?? "0"} ${localizations.monthlyDurationUnit}',
                                ),
                                const Divider(height: 30),
                                sectionTitle(context, localizations.myBookingDetailTimeTitle),
                                info(context, localizations.myBookingDetailCheckIn, formatDate(bookingData.checkIn) ?? "-"),
                                info(context, localizations.myBookingDetailCheckOut, formatDate(bookingData.checkOut) ?? "-"),
                                const Divider(height: 30),
                                sectionTitle(context, localizations.myBookingDetailBookingPriceTitle),
                                info(
                                  context,
                                  bookingData.bookingType == 'daily' ? localizations.myBookingDetailPricePerDay : localizations.myBookingDetailPricePerMonth,
                                  bookingData.bookingType == 'daily'
                                      ? formatCurrency(bookingData.dailyPrice) ?? "0"
                                      : formatCurrency(bookingData.monthlyPrice) ?? "0",
                                ),
                                info(context, localizations.myBookingDetailSubtotal, formatCurrency(bookingData.roomPrice) ?? "-"),

                                // Voucher Section (if voucher is applied)
                                if (bookingData.voucherCode != null && bookingData.voucherCode!.isNotEmpty) ...[
                                  const Divider(height: 20, thickness: 0.5, color: Colors.grey),
                                  info(
                                    context,
                                    'Subtotal Sebelum Diskon',
                                    formatCurrency(bookingData.subtotalBeforeDiscount),
                                  ),
                                  info(
                                    context,
                                    'Voucher (${bookingData.voucherCode})',
                                    '- ${formatCurrency(bookingData.discountAmount)}',
                                    color: AppColors.primaryColor,
                                    isBold: true,
                                  ),
                                  const Divider(height: 20, thickness: 0.5, color: Colors.grey),
                                ],

                          

                                // Deposit Fee (if not 0)
                                if (bookingData.depositFee != null && bookingData.depositFee! > 0)
                                  info(
                                    context,
                                    'Deposit',
                                    formatCurrency(bookingData.depositFee) ?? "0",
                                  ),

                                // Parking Fee (if not 0)
                                if (bookingData.parkingFee != null && bookingData.parkingFee! > 0)
                                  info(
                                    context,
                                    () {
                                      final parkingType = bookingData.parkingType?.toLowerCase();
                                      final parkingLabel = parkingType == 'car'
                                          ? localizations.bookingDetailsParkingCar
                                          : localizations.bookingDetailsParkingMotorcycle;

                                      if (bookingData.parkingDuration != null && bookingData.parkingDuration! > 0) {
                                        final duration = localizations.bookingDetailsParkingDuration(bookingData.parkingDuration!);
                                        return '$parkingLabel ($duration)';
                                      }
                                      return parkingLabel;
                                    }(),
                                    formatCurrency(bookingData.parkingFee) ?? "0",
                                  ),

                                info(
                                  context,
                                  localizations.myBookingDetailServiceFee,
                                  formatCurrency(bookingData.serviceFees) ?? "30.000",
                                ),

                                const Divider(height: 30),
                                sectionTitle(context, localizations.myBookingDetailTotalPriceTitle),
                                info(
                                  context,
                                  localizations.myBookingDetailGrandtotal,
                                  formatCurrency(bookingData.grandtotalPrice) ?? "0",
                                  isBold: true,
                                  color: AppColors.primaryColor,
                                ),
// Payment Method Section - Show for all transaction types
const Divider(height: 30),
sectionTitle(context, localizations.paymentMethodTitle),
const SizedBox(height: 8),

// Sub Header: Transaction Type (always show)
Text(
  bookingData.transactionType,
  style: TextStyle(
    fontSize: 15,
    fontWeight: FontWeight.w600,
    color: isDark ? Colors.white : Colors.black87,
  ),
),
const SizedBox(height: 12),

// VA Number with copy button (only for VA payment)
if (bookingData.virtualaccountnumber != null && bookingData.virtualaccountnumber!.isNotEmpty) ...[
  Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    crossAxisAlignment: CrossAxisAlignment.center,
    children: [
      Text(
        'VA Number:',
        style: TextStyle(
          fontSize: 14,
          color: Colors.grey.shade600,
        ),
      ),
      Row(
        children: [
          Text(
            bookingData.virtualaccountnumber!,
            style: TextStyle(
              fontSize: 16,
              fontWeight: FontWeight.bold,
              color: isDark ? Colors.white : Colors.black87,
            ),
          ),
          const SizedBox(width: 8),
          IconButton(
            onPressed: () => _copyToClipboard(bookingData.virtualaccountnumber!),
            icon: const Icon(Icons.copy, size: 20),
            color: AppColors.primaryColor,
            padding: EdgeInsets.zero,
            constraints: const BoxConstraints(),
          ),
        ],
      ),
    ],
  ),

  // Bank name if available
  if (bookingData.virtualaccountbank != null && bookingData.virtualaccountbank!.isNotEmpty) ...[
    const SizedBox(height: 12),
    Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      crossAxisAlignment: CrossAxisAlignment.center,
      children: [
        Text(
          'Bank:',
          style: TextStyle(
            fontSize: 14,
            color: Colors.grey.shade600,
          ),
        ),
        Text(
          bookingData.virtualaccountbank!,
          style: const TextStyle(
            fontSize: 14,
            fontWeight: FontWeight.w600,
            color: Colors.black87,
          ),
        ),
      ],
    ),
  ],
],

// QR Code Display (QRIS only, status pending)
if (_cachedQRData != null && _remainingTime.inSeconds > 0) ...[
  const SizedBox(height: 20),
  // QR Code
  Center(
    child: RepaintBoundary(
      key: _qrKey,
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withOpacity(0.1),
              blurRadius: 10,
              offset: const Offset(0, 4),
            ),
          ],
        ),
        child: QrImageView(
          data: _cachedQRData!['qrContent']!,
          version: QrVersions.auto,
          size: 250.0,
          backgroundColor: Colors.white,
        ),
      ),
    ),
  ),
  const SizedBox(height: 16),
  // Download button
  Center(
    child: ElevatedButton.icon(
      onPressed: _downloadQRCode,
      icon: const Icon(Icons.download, size: 20),
      label: Text(localizations.downloadQR ?? 'Download QR Code'),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primaryColor,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      ),
    ),
  ),
  const SizedBox(height: 8),
  // Scan instruction
  Center(
    child: Text(
      localizations.scanQRWithEwallet ?? 'Scan this QR code with your e-wallet app',
      style: TextStyle(
        fontSize: 13,
        color: Colors.grey.shade600,
        fontStyle: FontStyle.italic,
      ),
      textAlign: TextAlign.center,
    ),
  ),
],

// Credit Card Payment Link (CC only, status pending)
if (_cachedCCData != null && _remainingTime.inSeconds > 0) ...[
  const SizedBox(height: 20),
  // Continue Payment button
  Center(
    child: ElevatedButton.icon(
      onPressed: () async {
        final paymentUrl = _cachedCCData!['paymentUrl']!;
        final uri = Uri.parse(paymentUrl);
        if (await canLaunchUrl(uri)) {
          await launchUrl(uri, mode: LaunchMode.externalApplication);
        } else {
          if (mounted) {
            showNotificationDialog(
              context,
              'Cannot open payment link',
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          }
        }
      },
      icon: const Icon(Icons.credit_card, size: 20),
      label: Text(localizations.continuePayment ?? 'Continue Payment'),
      style: ElevatedButton.styleFrom(
        backgroundColor: AppColors.primaryColor,
        foregroundColor: Colors.white,
        padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
      ),
    ),
  ),
  const SizedBox(height: 8),
  // Note
  Center(
    child: Text(
      localizations.ccPaymentNote ?? 'Click the button above to complete your credit card payment',
      style: TextStyle(
        fontSize: 13,
        color: Colors.grey.shade600,
        fontStyle: FontStyle.italic,
      ),
      textAlign: TextAlign.center,
    ),
  ),
],

                                // Payment Proof Section - Only show if shouldShowUploadButton = true
                                Builder(
                                  builder: (context) {
                                    final isPaid = _isTransactionPaid(bookingData.transactionStatus);
                                    final isBRIManual = bookingData.transactionType == 'BRI Manual';
                                    // Only show for Pending status (not Waiting)
                                    final status = bookingData.transactionStatus?.toLowerCase().trim() ?? '';
                                    final isPending = status == 'pending';
                                    final shouldShowUploadButton = isPending && isBRIManual;

                                    AppLogger.d('=== UPLOAD BUTTON VALIDATION ===', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Transaction Status: "${bookingData.transactionStatus}"', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Transaction Type: "${bookingData.transactionType}"', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Paid: $isPaid', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is BRI Manual: $isBRIManual', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Should Show Upload Button: $shouldShowUploadButton', 'MYBOOKING-DETAIL');

                                    // Only show payment proof section if validation passes
                                    if (!shouldShowUploadButton) {
                                      return const SizedBox.shrink();
                                    }

                                    return Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Divider(height: 30),
                                        sectionTitle(context, localizations.myBookingDetailPaymentProofTitle),
                                        ImageViewerWidget(
                                          decodedAttachmentImageBytes: _decodedAttachmentImageBytes,
                                          shouldShowUpdateButton: shouldShowUploadButton,
                                          onUpdatePressed: () async {
                                            final result = await showImageSourceDialog(
                                              context,
                                              title: localizations.myBookingDetailUploadPaymentProof,
                                              cameraButtonText: localizations.myBookingDetailTakePhoto,
                                              galleryButtonText: localizations.myBookingDetailPickFromGallery,
                                              cancelButtonText: localizations.cancelButtonLabel,
                                            );

                                            if (result == ImageSourceOption.gallery) {
                                              _pickImageFromGallery(bookingIdrec, bookingData);
                                            } else if (result == ImageSourceOption.camera) {
                                              _takePhotoWithCamera(bookingIdrec, bookingData);
                                            }
                                          },
                                          onToggleVisibilityPressed: () { },
                                        ),
                                      ],
                                    );
                                  },
                                ),
                                // Check In Section
                                Builder(
                                  builder: (context) {
                                    // Ensure attachment is decoded before checking
                                    WidgetsBinding.instance.addPostFrameCallback((_) {
                                      if (mounted) {
                                        _updateDecodedAttachmentImageBytes(bookingData.attachment);
                                      }
                                    });

                                    final isPaid = _isTransactionPaid(bookingData.transactionStatus);

                                    // Check if today <= checkout date
                                    bool isValidCheckInDate = true;
                                    if (bookingData.checkOut != null && bookingData.checkOut!.isNotEmpty) {
                                      try {
                                        final checkoutDate = DateTime.parse(bookingData.checkOut!);
                                        final today = DateTime.now();
                                        final todayDate = DateTime(today.year, today.month, today.day);
                                        final checkoutDateOnly = DateTime(checkoutDate.year, checkoutDate.month, checkoutDate.day);

                                        // Check-in button should show if today <= checkout date
                                        isValidCheckInDate = todayDate.isBefore(checkoutDateOnly) || todayDate.isAtSameMomentAs(checkoutDateOnly);
                                      } catch (e) {
                                        AppLogger.e('Error parsing checkout date for check-in validation: $e', 'MYBOOKING-DETAIL');
                                        isValidCheckInDate = true; // Default to showing button if date parsing fails
                                      }
                                    }

                                    AppLogger.d('=== CHECK-IN DEBUG ===', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Transaction Status: "${bookingData.transactionStatus}"', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Paid: $isPaid', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Valid Check-In Date: $isValidCheckInDate', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Checkout Date: ${bookingData.checkOut}', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Should show check-in: ${isPaid && isValidCheckInDate}', 'MYBOOKING-DETAIL');

                                    if (isPaid && bookingData.checked_in_at == null && isValidCheckInDate) {
                                      return Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Divider(height: 30),
                                          sectionTitle(context, localizations.checkInSectionTitle),
                                          Center(
                                            child: Padding(
                                              padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                                              child: CustomIconButton(
                                                onPressed: () async {
                                                  AppLogger.d('Check In button pressed for booking: ${bookingData.orderId}', 'MYBOOKING-DETAIL');

                                                  final result = await showCheckInConfirmationDialog(
                                                    context,
                                                    propertyName: bookingData.propertyName ?? localizations.myBookingDetailPropertyNameDefault,
                                                    roomName: bookingData.roomName ?? localizations.myBookingDetailRoomNameDefault,
                                                    bookingType: bookingData.bookingType ?? 'daily',
                                                    checkIn: bookingData.checkIn ?? '',
                                                    checkOut: bookingData.checkOut ?? '',
                                                    roomPrice: bookingData.roomPrice,
                                                    serviceFee: bookingData.serviceFees,
                                                    grandTotal: bookingData.grandtotalPrice,
                                                    duration: bookingData.bookingType == 'daily'
                                                        ? (bookingData.bookingDays ?? 0)
                                                        : (bookingData.bookingMonths ?? 0),
                                                    paymentProofBase64: bookingData.attachment,
                                                  );

                                                  if (result != null && result.confirmed && result.idCardBase64 != null) {
                                                    AppLogger.d('Check-in confirmed with ID Card uploaded', 'MYBOOKING-DETAIL');

                                                    try {
                                                      // Call check-in API
                                                      final checkInService = ref.read(checkInProvider);
                                                      await checkInService(
                                                        orderId: bookingData.orderId ?? '',
                                                        idCardBase64: result.idCardBase64!,
                                                      );

                                                      // Refresh booking data after successful check-in
                                                      ref.invalidate(myBookingByIdProvider(bookingIdrec));
                                                      ref.invalidate(userBookingsProvider);

                                                      if (context.mounted) {
                                                        showNotificationDialog(
                                                          context,
                                                          'Check-in berhasil! Selamat menikmati penginapan Anda.',
                                                          defaultIcon: Icons.check_circle_outline,
                                                          iconColor: AppColors.primaryColor,
                                                        );
                                                      }
                                                    } catch (e) {
                                                      AppLogger.e('Check-in failed', e, null, 'MYBOOKING-DETAIL');
                                                      if (context.mounted) {
                                                        showNotificationDialog(
                                                          context,
                                                          'Check-in gagal: $e',
                                                          defaultIcon: Icons.error_outline,
                                                          iconColor: Colors.red,
                                                        );
                                                      }
                                                    }
                                                  }
                                                },
                                                icon: Icons.login,
                                                text: localizations.checkInSectionTitle,
                                                buttonColor: AppColors.primaryColor,
                                                textColor: Colors.white,
                                                iconColor: Colors.white,
                                                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                                                borderRadius: 8,
                                                height: 56,
                                                width: 200,
                                                fontSize: 16,
                                              ),
                                            ),
                                          ),
                                        ],
                                      );
                                    }
                                    return const SizedBox.shrink();
                                  },
                                ),

                                // Renew Booking Section - Only show for paid bookings that have checked in and before checkout
                                Builder(
                                  builder: (context) {
                                    final isPaid = _isTransactionPaid(bookingData.transactionStatus);
                                    final hasCheckedIn = bookingData.checked_in_at != null && bookingData.checked_in_at!.isNotEmpty;

                                    // Check if today is between check-in and checkout
                                    bool isWithinRenewPeriod = false;
                                    if (bookingData.checkIn != null && bookingData.checkIn!.isNotEmpty &&
                                        bookingData.checkOut != null && bookingData.checkOut!.isNotEmpty) {
                                      try {
                                        final checkInDate = DateTime.parse(bookingData.checkIn!);
                                        final checkoutDate = DateTime.parse(bookingData.checkOut!);
                                        final today = DateTime.now();
                                        final todayDate = DateTime(today.year, today.month, today.day);
                                        final checkInDateOnly = DateTime(checkInDate.year, checkInDate.month, checkInDate.day);
                                        final checkoutDateOnly = DateTime(checkoutDate.year, checkoutDate.month, checkoutDate.day);

                                        // Show button if today is between check-in and checkout (inclusive)
                                        isWithinRenewPeriod = (todayDate.isAfter(checkInDateOnly) || todayDate.isAtSameMomentAs(checkInDateOnly)) &&
                                                             (todayDate.isBefore(checkoutDateOnly) || todayDate.isAtSameMomentAs(checkoutDateOnly));
                                      } catch (e) {
                                        AppLogger.e('Error parsing dates: $e', 'MYBOOKING-DETAIL');
                                      }
                                    }

                                    // Check if already renewed (renewal_status = 1)
                                    final isAlreadyRenewed = bookingData.renewalStatus == 1;

                                    final shouldShowRenew = isPaid && hasCheckedIn && isWithinRenewPeriod && !isAlreadyRenewed;

                                    AppLogger.d('=== RENEW BOOKING VALIDATION ===', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Transaction Status: "${bookingData.transactionStatus}"', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Paid: $isPaid', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Has Checked In: $hasCheckedIn (checked_in_at: ${bookingData.checked_in_at})', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Check-in Date: ${bookingData.checkIn}', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Checkout Date: ${bookingData.checkOut}', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Within Renew Period (check-in to checkout): $isWithinRenewPeriod', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Is Already Renewed: $isAlreadyRenewed (renewal_status: ${bookingData.renewalStatus})', 'MYBOOKING-DETAIL');
                                    AppLogger.d('Should Show Renew: $shouldShowRenew', 'MYBOOKING-DETAIL');

                                    if (shouldShowRenew) {
                                      return Column(
                                        crossAxisAlignment: CrossAxisAlignment.start,
                                        children: [
                                          const Divider(height: 30),
                                          sectionTitle(context, localizations.renewBookingButton),
                                          Center(
                                            child: Padding(
                                              padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
                                              child: CustomIconButton(
                                                onPressed: () async {
                                                  AppLogger.d('Renew Booking button pressed', 'MYBOOKING-DETAIL');

                                                  // Show simple dialog to select dates
                                                  final result = await showDialog<Map<String, String>>(
                                                    context: context,
                                                    barrierDismissible: false,
                                                    builder: (dialogContext) {
                                                      return RenewBookingDialog(
                                                        bookingData: bookingData,
                                                      );
                                                    },
                                                  );

                                                  // If dates selected, navigate to payment page directly
                                                  if (result != null && context.mounted) {
                                                    AppLogger.d('Dates selected for renewal', 'MYBOOKING-DETAIL');
                                                    AppLogger.d('Check-in: ${result['check_in']}, Check-out: ${result['check_out']}', 'MYBOOKING-DETAIL');

                                                    // Navigate to payment page - loading state will be handled there
                                                    context.push('/payment', extra: {
                                                      'is_renewal': true,
                                                      'original_order_id': bookingData.orderId,
                                                      'original_check_in': bookingData.checkIn,
                                                      'original_check_out': bookingData.checkOut,
                                                      'new_check_in': result['check_in'],
                                                      'new_check_out': result['check_out'],
                                                      'room_id': bookingData.roomId,
                                                      'property_id': bookingData.propertyId,
                                                      'property_name': bookingData.propertyName,
                                                      'property_type': bookingData.propertyType,
                                                      'room_name': bookingData.roomName,
                                                      'booking_type': bookingData.bookingType,
                                                      'rentType': result['rentType'] ?? 'daily',
                                                      'daily_price': bookingData.dailyPrice ?? 0.0,
                                                      'monthly_price': bookingData.monthlyPrice ?? 0.0,
                                                    });
                                                  }
                                                },
                                                icon: Icons.refresh,
                                                text: localizations.renewBookingButton,
                                                buttonColor: AppColors.primaryColor,
                                                textColor: Colors.white,
                                                iconColor: Colors.white,
                                                padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
                                                borderRadius: 8,
                                                height: 56,
                                                width: 250,
                                                fontSize: 16,
                                              ),
                                            ),
                                          ),
                                        ],
                                      );
                                    }
                                    return const SizedBox.shrink();
                                  },
                                ),
                                // attachmentUploadStatus.when(
                                //   data: (isUploaded) => const SizedBox.shrink(),
                                //   loading: () => Center(
                                //     child: Padding(
                                //       padding: const EdgeInsets.symmetric(vertical: 10.0),
                                //       child: Column(
                                //         children: [
                                //           const CircularProgressIndicator(),
                                //           const SizedBox(height: 8),
                                //           Text(localizations.myBookingDetailUploading), 
                                //         ],
                                //       ),
                                //     ),
                                //   ),
                                //   error: (err, stack) => Center(
                                //     child: Padding(
                                //       padding: const EdgeInsets.symmetric(vertical: 10.0),
                                //       child: Text('${localizations.myBookingDetailUploadFailed}: $err', style: const TextStyle(color: Colors.red)), 
                                //     ),
                                //   ),
                                // ),
                                // Bottom upload button section - only show if status NOT paid AND transaction_type is "Transfer Manual"
                                Builder(
                                  builder: (context) {
                                    final isPaid = _isTransactionPaid(bookingData.transactionStatus);
                                    final isBRIManual = bookingData.transactionType == 'BRI Manual';
                                    // Only show for Pending status (not Waiting, Cancelled, Rejected)
                                    final status = bookingData.transactionStatus?.toLowerCase().trim() ?? '';
                                    final isPending = status == 'pending';
                                    final shouldShowUploadButton = isPending && isBRIManual;
                                    final hasNoAttachment = _decodedAttachmentImageBytes == null || _decodedAttachmentImageBytes!.isEmpty;
                                    final isNotLoading = attachmentUploadStatus is! AsyncLoading;

                                    // Only show this section if validation passes AND no attachment exists
                                    if (hasNoAttachment && isNotLoading && shouldShowUploadButton) {
                                      return Column(
                                        children: [
                                          if (selectedImage != null)
                                            Center(
                                              child: Padding(
                                                padding: const EdgeInsets.all(16.0),
                                                child: Image.file(
                                                  selectedImage!,
                                                  height: 150,
                                                  fit: BoxFit.cover,
                                                ),
                                              ),
                                            ),
                                          Center(
                                            child: Padding(
                                              padding: EdgeInsets.fromLTRB(16, selectedImage == null ? 0 : 10, 16, 10),
                                              child: CustomIconButton(
                                                onPressed: () async {
                                                  final result = await showImageSourceDialog(
                                                    context,
                                                    title: localizations.myBookingDetailUploadPaymentProof,
                                                    cameraButtonText: localizations.myBookingDetailTakePhoto,
                                                    galleryButtonText: localizations.myBookingDetailPickFromGallery,
                                                    cancelButtonText: localizations.cancelButtonLabel,
                                                  );

                                                  if (result == ImageSourceOption.gallery) {
                                                    _pickImageFromGallery(bookingIdrec, bookingData);
                                                  } else if (result == ImageSourceOption.camera) {
                                                    _takePhotoWithCamera(bookingIdrec, bookingData);
                                                  }
                                                },
                                                icon: Icons.camera_alt,
                                                text: localizations.myBookingDetailUploadPaymentProof,
                                                buttonColor: AppColors.primaryColor,
                                                textColor: Colors.white,
                                                iconColor: Colors.white,
                                                padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 12),
                                                borderRadius: 8,
                                                height: 56,
                                                width: double.infinity,
                                                fontSize: 15,
                                              ),
                                            ),
                                          ),
                                          if (selectedImage != null)
                                            Center(
                                              child: Padding(
                                                padding: const EdgeInsets.fromLTRB(16, 0, 16, 40),
                                                child: CustomIconButton(
                                                  onPressed: () => _uploadImage(bookingIdrec, bookingData),
                                                  icon: Icons.cloud_upload,
                                                  text: localizations.myBookingDetailUploadThisImage,
                                                  buttonColor: AppColors.primaryColor,
                                                  textColor: Colors.white,
                                                  iconColor: Colors.white,
                                                  padding: const EdgeInsets.symmetric(horizontal: 15, vertical: 12),
                                                  borderRadius: 8,
                                                  height: 56,
                                                  width: double.infinity,
                                                  fontSize: 15,
                                                ),
                                              ),
                                            ),
                                        ],
                                      );
                                    }
                                    return const SizedBox.shrink();
                                  },
                                ),
                              ],
                            ),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              );
            },
            loading: () => Skeletonizer(
              enabled: true,
              child: SafeArea(
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        height: 400,
                        color: Colors.grey[200],
                      ),
                      Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              'Loading Property Name',
                              style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                            ),
                            const SizedBox(height: 16),
                            Text('Room: Loading Room Name'),
                            const SizedBox(height: 8),
                            Text('Check-in: 2024-01-01'),
                            Text('Check-out: 2024-01-02'),
                            const SizedBox(height: 8),
                            Text('Status: Loading'),
                            const SizedBox(height: 16),
                            Text('Total: Rp 0'),
                            const SizedBox(height: 24),
                            Container(
                              height: 200,
                              decoration: BoxDecoration(
                                color: Colors.grey[300],
                                borderRadius: BorderRadius.circular(8),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            error: (err, stack) => Center(child: Text("${localizations.myBookingDetailError}: $err")), 
          ),
          Positioned(
            top: 0,
            left: 0,
            right: 0,
            child: CustomAppBar(title: localizations.myBookingDetailAppBarTitle), 
          ),
        ],
      ),
    );
  }
}