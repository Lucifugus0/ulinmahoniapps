import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/core/layout/mainlayout.dart';
import 'package:ulinmahoniapps/features/book/detailproperty/model/detailproperty_model.dart';
import 'package:ulinmahoniapps/features/book/roomdetails/model/rooms_model.dart';
import 'package:ulinmahoniapps/features/book/roomdetails/provider/rooms_provider.dart';
import 'package:ulinmahoniapps/features/auth/login/provider/auth_provider.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/utils/formatcurrency.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../widgets/payment_method.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import '../../../../../core/widgets/syarat&ketentuan_widgets.dart';
import '../widgets/confirmation_dialog.dart';
import 'package:ulinmahoniapps/features/book/payment/provider/payment_provider.dart' ;
import 'package:ulinmahoniapps/features/book/payment/provider/voucher_provider.dart';
import 'package:ulinmahoniapps/features/book/payment/model/voucher_model.dart';
import 'package:ulinmahoniapps/features/book/payment/model/update_payment_method_model.dart';
import 'package:ulinmahoniapps/core/network/api_result.dart';
import 'package:url_launcher/url_launcher.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/utils/app_logger.dart';
import '../widgets/bank_selection_widget.dart';
import '../../provider/doku_va_provider.dart';
import '../../provider/doku_qris_provider.dart';
import '../../provider/doku_cc_provider.dart';
import '../widgets/va_result_dialog.dart';
import '../widgets/qris_result_dialog.dart';
import '../../../../mybooking/mybookingdetails/provider/renew_booking_provider.dart';
import '../../../roomdetails/presentation/widgets/daily_price_breakdown.dart';
import '../../../../mybooking/mybooking/provider/mybooking_provider.dart';
import '../../../../../core/utils/payment_cache_utils.dart';
import '../../../../../core/network/dio_client.dart';
import '../../../../../core/constants/api_constants.dart';

class PaymentPage extends ConsumerStatefulWidget {
  // Normal booking fields
  final RoomModel? room;
  final DetailPropertyModel? propertyData;
  final String? rentType;
  final int? duration;
  final DateTime? checkInDate;
  final DateTime? checkOutDate;

  // Renewal booking fields
  final bool isRenewal;
  final String? originalOrderId;
  final String? originalCheckIn;
  final String? originalCheckOut;
  final String? newCheckIn;
  final String? newCheckOut;
  final int? roomId;
  final int? propertyId;
  final String? propertyName;
  final String? roomName;
  final String? bookingType;
  final double? dailyPrice;
  final double? monthlyPrice;

  // Daily multi-tier pricing breakdown from price-preview API
  final List<dynamic>? multiTierBreakdown;
  final double? multiTierTotalPrice;

  const PaymentPage({
    super.key,
    this.room,
    this.propertyData,
    this.rentType,
    this.duration,
    this.checkInDate,
    this.checkOutDate,
    this.isRenewal = false,
    this.originalOrderId,
    this.originalCheckIn,
    this.originalCheckOut,
    this.newCheckIn,
    this.newCheckOut,
    this.roomId,
    this.propertyId,
    this.propertyName,
    this.roomName,
    this.bookingType,
    this.dailyPrice,
    this.monthlyPrice,
    this.multiTierBreakdown,
    this.multiTierTotalPrice,
  });

  @override
  ConsumerState<PaymentPage> createState() => _PaymentPageState();
}

class _PaymentPageState extends ConsumerState<PaymentPage> {
  List<Map<String, dynamic>> get paymentMethods {
    final localizations = AppLocalizations.of(context)!;
    return [
      {
        'iconAsset': 'assets/images/payment/doku_va.png',
        'title': localizations.paymentGenerateTransferVA,
        'subtitle': localizations.paymentVirtualAccountSelectBank,
        'value': 'Transfer VA'
      },
      {
        'iconAsset': 'assets/images/payment/qris.png',
        'title': localizations.paymentQRIS,
        'subtitle': localizations.paymentQRISSubtitle,
        'value': 'QRIS'
      },
      {
        'iconAsset': 'assets/images/payment/mastercard.png',
        'title': localizations.paymentCreditCard,
        'subtitle': localizations.paymentCreditCardSubtitle,
        'value': 'CREDITCARD'
      },
      // DISABLED: BRI Manual payment method
      // {
      //   'iconUrl': 'https://logotyp.us/file/bri.svg',
      //   'title': localizations.paymentManualTransferBRI,
      //   'subtitle': '050501001671567 an PT.Kelola Aset Properti',
      //   'value': 'BRI Manual'
      // },
    ];
  }

  String? _selectedTransactionValue;
  int? _selectedPaymentMethodIndex;
  bool _agreedToTerms = false;

  // Confirmed total — set when user confirms payment, used for all DOKU gateway calls
  double _confirmedTotal = 0;

  // Renewal multi-tier pricing — fetched from price-preview API for renewal daily bookings
  List<dynamic>? _renewalMultiTierBreakdown;
  double? _renewalMultiTierTotalPrice;

  // Deposit & Parking state
  double _depositFee = 0; // Will be loaded from room data
  String? _selectedParkingType; // 'car' or 'motorcycle' or null
  int _parkingDuration = 1;
  double _parkingFee = 0; // Total price for display (base * duration)
  double _carParkingPrice = 0; // Will be loaded from room.parkingFees
  double _motorcycleParkingPrice = 0; // Will be loaded from room.parkingFees
  int _carParkingCapacity = 0; // Will be loaded from room.parkingFees
  int _motorcycleParkingCapacity = 0; // Will be loaded from room.parkingFees
  int _carParkingQuotaUsed = 0; // Will be loaded from room.parkingFees
  int _motorcycleParkingQuotaUsed = 0; // Will be loaded from room.parkingFees
  final TextEditingController _vehiclePlateController = TextEditingController();

  // Voucher state
  final TextEditingController _voucherController = TextEditingController();

  @override
  void initState() {
    super.initState();

    // Load deposit and parking fees from room data
    if (widget.room != null) {
      _depositFee = widget.room!.depositFee ?? 0.0;

      // Load parking prices from room.parkingFees
      for (var parkingFee in widget.room!.parkingFees) {
        if (parkingFee.parkingType?.toLowerCase() == 'car') {
          _carParkingPrice = parkingFee.fee ?? 0.0;
          _carParkingCapacity = parkingFee.capacity ?? 0;
          _carParkingQuotaUsed = parkingFee.quotaUsed ?? 0;
        } else if (parkingFee.parkingType?.toLowerCase() == 'motorcycle') {
          _motorcycleParkingPrice = parkingFee.fee ?? 0.0;
          _motorcycleParkingCapacity = parkingFee.capacity ?? 0;
          _motorcycleParkingQuotaUsed = parkingFee.quotaUsed ?? 0;
        }
      }

      AppLogger.d('Loaded deposit fee: $_depositFee', 'PAYMENT-PAGE');
      AppLogger.d('Loaded car parking - price: $_carParkingPrice, capacity: $_carParkingCapacity, used: $_carParkingQuotaUsed', 'PAYMENT-PAGE');
      AppLogger.d('Loaded motorcycle parking - price: $_motorcycleParkingPrice, capacity: $_motorcycleParkingCapacity, used: $_motorcycleParkingQuotaUsed', 'PAYMENT-PAGE');
    } else if (widget.isRenewal && widget.roomId != null) {
      // For renewal: fetch room data to get latest parking fees
      AppLogger.i('Renewal mode: fetching room data for roomId ${widget.roomId}', 'PAYMENT-PAGE');
      _fetchRoomDataForRenewal();
    }

    WidgetsBinding.instance.addPostFrameCallback((_) {
      final localizations = AppLocalizations.of(context)!;

      if (widget.isRenewal) {
        // For renewal booking: calculate duration and load payment details
        AppLogger.i('Renewal booking mode - calculating duration from dates', 'PAYMENT-PAGE');

        if (widget.newCheckIn != null && widget.newCheckOut != null &&
            widget.dailyPrice != null && widget.propertyName != null && widget.roomName != null) {

          // Parse dates and calculate duration
          final checkInDate = DateTime.parse(widget.newCheckIn!);
          final checkOutDate = DateTime.parse(widget.newCheckOut!);

          final rentType = widget.rentType ?? 'daily';

          // Calculate duration based on rent type
          int duration;
          if (rentType.toLowerCase() == 'monthly') {
            // Calculate duration in months for monthly rentals
            final months = (checkOutDate.year - checkInDate.year) * 12 + (checkOutDate.month - checkInDate.month);
            duration = months > 0 ? months : 1;
          } else {
            // Calculate duration in days for daily rentals
            duration = checkOutDate.difference(checkInDate).inDays;
          }

          AppLogger.d('Renewal - Duration: $duration ${rentType.toLowerCase() == "monthly" ? "months" : "days"}, Price: ${widget.dailyPrice}', 'PAYMENT-PAGE');

          // Load renewal payment details with flat rate first (immediate)
          ref.read(paymentNotifierProvider.notifier).loadRenewalPaymentDetails(
            roomId: widget.roomId ?? 0,
            propertyId: widget.propertyId ?? 0,
            roomName: widget.roomName ?? 'Room',
            propertyName: widget.propertyName ?? 'Property',
            dailyPrice: widget.dailyPrice!,
            monthlyPrice: widget.monthlyPrice ?? 0.0,
            rentType: rentType,
            duration: duration,
            checkInDate: checkInDate,
            checkOutDate: checkOutDate,
            localizations: localizations,
          );

          // Fetch multi-tier pricing immediately — updates provider when API responds
          // Decoupled from _fetchRoomDataForRenewal so room data errors don't block this
          _fetchRenewalPricing();
        } else {
          AppLogger.e('Missing renewal data', null, null, 'PAYMENT-PAGE');
        }
        return;
      }

      // Normal booking flow
      if (widget.room != null && widget.propertyData != null &&
          widget.rentType != null && widget.duration != null &&
          widget.checkInDate != null && widget.checkOutDate != null) {
        ref.read(paymentNotifierProvider.notifier).loadPaymentDetails(
          room: widget.room!,
          propertyData: widget.propertyData!,
          rentType: widget.rentType!,
          duration: widget.duration!,
          checkInDate: widget.checkInDate!,
          checkOutDate: widget.checkOutDate!,
          localizations: localizations,
          // Pass multi-tier total so provider uses correct weekday/weekend/holiday pricing
          multiTierTotalPrice: widget.multiTierTotalPrice,
        );
      }
    });
  }

  /// Fetch price-preview API for renewal daily bookings — gets per-date breakdown
  Future<void> _fetchRenewalPricing() async {
    if (widget.roomId == null || widget.newCheckIn == null || widget.newCheckOut == null) return;
    final rentType = widget.rentType ?? 'daily';
    if (rentType.toLowerCase() != 'daily') return;

    try {
      final dioClient = DioClient();
      final url = ApiConfig.roomPricePreview(widget.roomId.toString())
          .replaceFirst(ApiConfig.baseUrl, '');

      AppLogger.i('Renewal - Fetching price-preview: roomId=${widget.roomId}, ${widget.newCheckIn} to ${widget.newCheckOut}', 'PAYMENT-PAGE');

      final response = await dioClient.get(url, queryParameters: {
        'check_in': widget.newCheckIn,
        'check_out': widget.newCheckOut,
      });

      if (response.statusCode == 200 && response.data is Map<String, dynamic>) {
        final data = response.data['data'] as Map<String, dynamic>?;
        if (data != null) {
          final totalPrice = (data['total_price'] as num?)?.toDouble() ?? 0;
          final breakdown = data['breakdown'] as List? ?? [];

          if (totalPrice > 0 && mounted) {
            setState(() {
              _renewalMultiTierTotalPrice = totalPrice;
              _renewalMultiTierBreakdown = breakdown;
            });

            // Re-load renewal payment details with correct multi-tier total
            final localizations = AppLocalizations.of(context)!;
            final checkInDate = DateTime.parse(widget.newCheckIn!);
            final checkOutDate = DateTime.parse(widget.newCheckOut!);
            final duration = checkOutDate.difference(checkInDate).inDays;

            ref.read(paymentNotifierProvider.notifier).loadRenewalPaymentDetails(
              roomId: widget.roomId ?? 0,
              propertyId: widget.propertyId ?? 0,
              roomName: widget.roomName ?? 'Room',
              propertyName: widget.propertyName ?? 'Property',
              dailyPrice: widget.dailyPrice!,
              monthlyPrice: widget.monthlyPrice ?? 0.0,
              rentType: rentType,
              duration: duration,
              checkInDate: checkInDate,
              checkOutDate: checkOutDate,
              localizations: localizations,
              multiTierTotalPrice: totalPrice,
            );

            AppLogger.i('Renewal - Multi-tier total: $totalPrice, days: ${breakdown.length}', 'PAYMENT-PAGE');
          }
        }
      }
    } catch (e, st) {
      AppLogger.e('Renewal - Failed to fetch price-preview', e, st, 'PAYMENT-PAGE');
    }
  }

  /// Fetch room data for renewal to get latest parking fees
  Future<void> _fetchRoomDataForRenewal() async {
    try {
      final roomData = await ref.read(roomByIdProvider(widget.roomId!).future);

      if (mounted) {
        setState(() {
          // Deposit tidak berlaku untuk renewal, jadi tetap 0
          _depositFee = 0.0;

          // Load parking prices from fetched room data
          for (var parkingFee in roomData.parkingFees) {
            if (parkingFee.parkingType?.toLowerCase() == 'car') {
              _carParkingPrice = parkingFee.fee ?? 0.0;
              _carParkingCapacity = parkingFee.capacity ?? 0;
              _carParkingQuotaUsed = parkingFee.quotaUsed ?? 0;
            } else if (parkingFee.parkingType?.toLowerCase() == 'motorcycle') {
              _motorcycleParkingPrice = parkingFee.fee ?? 0.0;
              _motorcycleParkingCapacity = parkingFee.capacity ?? 0;
              _motorcycleParkingQuotaUsed = parkingFee.quotaUsed ?? 0;
            }
          }

          AppLogger.d('Renewal - Loaded parking fees from API:', 'PAYMENT-PAGE');
          AppLogger.d('  Car parking - price: $_carParkingPrice, capacity: $_carParkingCapacity, used: $_carParkingQuotaUsed', 'PAYMENT-PAGE');
          AppLogger.d('  Motorcycle parking - price: $_motorcycleParkingPrice, capacity: $_motorcycleParkingCapacity, used: $_motorcycleParkingQuotaUsed', 'PAYMENT-PAGE');
        });
      }
    } catch (e, st) {
      AppLogger.e('Failed to fetch room data for renewal', e, st, 'PAYMENT-PAGE');
      // Tetap lanjutkan dengan nilai default (0) jika gagal
    }
  }

  Future<void> _applyVoucher({
    required Map<String, dynamic> roomData,
    required List<Map<String, dynamic>> itemDetails,
    required double basePrice,
  }) async {
    final localizations = AppLocalizations.of(context)!;
    final authState = ref.read(authProvider);
    final code = _voucherController.text.trim();

    if (code.isEmpty) {
      showNotificationDialog(
        context,
        localizations.paymentVoucherInvalid,
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // Get user ID from authState
    final user = authState.user.value;
    if (user == null) {
      showNotificationDialog(
        context,
        'User not authenticated',
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // Calculate subtotal (price × duration) - NOT afterOriginalTotalFees
    // Example: 1 Day × 500k = 500k (subtotal), discount 50k, then add service fee 30k = 480k (grand total)
    final rentType = widget.rentType ?? roomData['rentType']?.toString() ?? 'daily';
    final pricePerUnit = rentType.toLowerCase() == 'daily'
        ? (roomData['daily_price'] as num?)?.toDouble() ?? 0.0
        : (roomData['monthly_price'] as num?)?.toDouble() ?? 0.0;

    final duration = widget.duration ??
        (roomData['booking_days'] as int? ?? roomData['booking_months'] as int? ?? 0);
    final subtotal = pricePerUnit * duration;

    // Validate minimum transaction amount (500k)
    if (subtotal < 500000) {
      showNotificationDialog(
        context,
        'Minimum transaction amount for voucher is Rp 500.000',
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // Validate voucher
    await ref.read(voucherNotifierProvider.notifier).validateVoucher(
      voucherCode: code,
      userId: user.id,
      transactionAmount: subtotal, // Use subtotal only (before service fees)
      propertyId: widget.propertyData?.id ?? widget.propertyId ?? 0,
      roomId: widget.room?.id ?? widget.roomId ?? 0,
    );
  }

  void _removeVoucher() {
    ref.read(voucherNotifierProvider.notifier).removeVoucher();
    _voucherController.clear();
  }

  @override
  void dispose() {
    // Voucher state is already reset before navigation in success callback
    // No need to reset here as it causes "ref after dispose" error
    _voucherController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final paymentState = ref.watch(paymentNotifierProvider);
    final voucherNotifier = ref.watch(voucherNotifierProvider.notifier);
    final voucherValidationState = ref.watch(
      voucherNotifierProvider.select((state) => state.validationResult),
    );
    final authState = ref.watch(authProvider);
    final textTheme = Theme.of(context).textTheme;
    // Dark mode detection for payment page colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final isVoucherLoading = voucherValidationState.isLoading;

    // Listen to voucher validation result
    ref.listen<AsyncValue<VoucherValidationResponse?>>(
      voucherNotifierProvider.select((state) => state.validationResult),
      (previous, next) {
        next.whenOrNull(
          data: (validationResponse) {
            if (validationResponse != null) {
              if (validationResponse.isValid) {
                showNotificationDialog(
                  context,
                  localizations.paymentVoucherApplied,
                  defaultIcon: Icons.check_circle_outline,
                  iconColor: AppColors.primaryAdaptive(context),
                );
              }
            }
          },
          error: (error, stackTrace) {
            // Display API error message directly
            final errorMessage = error is String ? error : error.toString();
            AppLogger.w('Voucher validation failed: $errorMessage', 'PAYMENT-PAGE');
            showNotificationDialog(
              context,
              errorMessage,
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          },
        );
      },
    );

    // Listen to voucher application result (when voucher is applied to booking)
    ref.listen<AsyncValue<VoucherApplicationResponse?>>(
      voucherNotifierProvider.select((state) => state.applicationResult),
      (previous, next) {
        next.whenOrNull(
          data: (applicationResponse) {
            if (applicationResponse != null) {
              if (applicationResponse.isSuccess) {
                AppLogger.s(
                  'Voucher applied to booking successfully: ${applicationResponse.message}',
                  'PAYMENT-PAGE',
                );
                // Success notification is already shown by booking success
              }
            }
          },
          error: (error, stackTrace) {
            // Display API error message directly
            final errorMessage = error is String ? error : error.toString();
            AppLogger.e(
              'Failed to apply voucher to booking',
              error,
              stackTrace,
              'PAYMENT-PAGE',
            );
            showNotificationDialog(
              context,
              'Voucher application failed: $errorMessage',
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          },
        );
      },
    );

    ref.listen<AsyncValue<Map<String, dynamic>?>>(
      paymentNotifierProvider.select((state) => state.postBookingResult),
          (previous, next) {
        next.whenOrNull(
          data: (responseData) async {
            if (responseData != null) {
              final message = responseData['message']?.toString() ?? '';
              final paymentUrl = responseData['data']?['payment_url']?.toString();
              final idrec = responseData['data']?['idrec']?.toString();
              final orderId = responseData['data']?['order_id']?.toString();

              // Log booking response data for debugging
              AppLogger.separator('POST BOOKING RESPONSE');
              AppLogger.d('idrec: $idrec', 'PAYMENT-PAGE');
              AppLogger.d('order_id: $orderId', 'PAYMENT-PAGE');
              AppLogger.d('Full data: ${responseData['data']}', 'PAYMENT-PAGE');
              AppLogger.separator();

              // Voucher already applied during booking creation
              // Backend response contains discount_amount and grandtotal_price
              // No need to re-apply voucher here to avoid "already used" error
              if (voucherNotifier.hasAppliedVoucher && idrec != null) {
                AppLogger.d(
                  'Voucher ${voucherNotifier.appliedVoucherCode} already applied in booking $idrec, using grandtotal from backend response',
                  'PAYMENT-PAGE',
                );
              }

              // Reset voucher state and clear input after successful booking (before navigation)
              AppLogger.d('Resetting voucher state after successful booking', 'PAYMENT-PAGE');
              ref.read(voucherNotifierProvider.notifier).resetState();

              if (!mounted) return;
              _voucherController.clear();

              if (message.isNotEmpty) {
                showNotificationDialog(
                  context,
                  '${localizations.paymentBookingSuccess}: $message',
                  defaultIcon: Icons.check_circle_outline,
                  iconColor: AppColors.primaryAdaptive(context),
                );
              }

              if (_selectedTransactionValue == 'DOKU') {

                if (paymentUrl != null && paymentUrl.isNotEmpty) {
                  final Uri url = Uri.parse(paymentUrl);
                  if (await canLaunchUrl(url)) {

                    await launchUrl(url, mode: LaunchMode.externalApplication);


                    Future.delayed(const Duration(seconds: 1), () {
                      if (mounted) context.go('/mybooking');
                    });
                  } else {

                    showNotificationDialog(
                      context,
                      'Tidak dapat membuka tautan pembayaran DOKU.',
                      defaultIcon: Icons.error_outline,
                      iconColor: Colors.red,
                    );
                  }
                } else {

                  showNotificationDialog(
                    context,
                    'Gagal mendapatkan tautan pembayaran DOKU dari server.',
                    defaultIcon: Icons.error_outline,
                    iconColor: Colors.red,
                  );
                }
              } else if (_selectedTransactionValue == 'Transfer VA') {
                // Generate DOKU VA
                final dokuVAState = ref.read(dokuVANotifierProvider);
                final selectedBank = dokuVAState.selectedBank;

                if (selectedBank != null && idrec != null) {
                  final user = authState.user.value;
                  if (user != null) {
                    // Use _confirmedTotal (set when user confirmed payment) — matches what was shown on screen
                    // Backend's grandtotal_price uses flat daily_price × days, not multi-tier rates
                    final paymentState = ref.read(paymentNotifierProvider);
                    final bookingData = paymentState.postBookingResult.value;

                    // Get order_id from booking response (use this for DOKU, not idrec)
                    final orderId = bookingData?['data']?['order_id']?.toString() ?? idrec;

                    final finalAmount = _confirmedTotal;

                    AppLogger.i(
                      'Generating DOKU VA for booking $idrec, order_id: $orderId, bank: $selectedBank, amount: $finalAmount (source: confirmedTotal)',
                      'PAYMENT-PAGE',
                    );

                    // Generate VA - use order_id, not idrec
                    await ref.read(dokuVANotifierProvider.notifier).generateVA(
                      orderId: orderId,
                      userName: user.firstName,
                      userEmail: user.email,
                      userPhone: user.phoneNumber,
                      amount: finalAmount,
                      bank: selectedBank,
                    );

                    // Check VA generation result
                    final vaState = ref.read(dokuVANotifierProvider);
                    await vaState.generateResult.when(
                      data: (vaResponse) async {
                        if (vaResponse.isSuccess && vaResponse.data != null) {
                          AppLogger.s(
                            'VA generated successfully: ${vaResponse.data!.virtualAccountNo}',
                            'PAYMENT-PAGE',
                          );

                          // Update payment method in backend
                          // Get rent type from booking data
                          final bookingType = bookingData?['data']?['booking_type']?.toString().toLowerCase();
                          final isMonthly = bookingType == 'monthly';
                          // Get voucher data
                          final voucherState = ref.read(voucherNotifierProvider);
                          final updateRequest = UpdatePaymentMethodRequest(
                            paymentMethod: 'Transfer VA',
                            virtualAccountNo: vaResponse.data!.virtualAccountNo,
                            paymentBank: selectedBank.toUpperCase(),
                            depositFee: isMonthly ? _depositFee : null,
                            parkingFee: isMonthly && _parkingFee > 0 ? _parkingFee : null,
                            parkingType: isMonthly && _selectedParkingType != null
                                ? _selectedParkingType
                                : null,
                            parkingDuration: isMonthly && _parkingDuration > 0 ? _parkingDuration : null,
                            vehiclePlate: isMonthly && _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                ? _vehiclePlateController.text.trim()
                                : null,
                            ownerName: user.firstName,
                            ownerPhone: user.phoneNumber,
                            voucherCode: voucherState.appliedVoucherCode,
                            discountAmount: voucherState.discountAmount,
                          );

                          final updateResult = await ref
                              .read(paymentNotifierProvider.notifier)
                              .updatePaymentMethod(idrec.toString(), updateRequest);

                          switch (updateResult) {
                            case Success(:final data):
                              AppLogger.s(
                                'Payment method updated: ${data.message}',
                                'PAYMENT-PAGE',
                              );
                            case Failure(:final message):
                              AppLogger.w(
                                'Failed to update payment method: $message',
                                'PAYMENT-PAGE',
                              );
                              // Note: Don't block user flow, just log the error
                          }

                          // Show VA Result Dialog
                          if (mounted) {
                            await showDialog(
                              context: context,
                              barrierDismissible: false,
                              builder: (ctx) => VAResultDialog(
                                vaData: vaResponse.data!,
                                onClose: () {
                                  Navigator.of(ctx).pop();
                                  // Refresh mybooking list before navigation
                                  ref.invalidate(userBookingsProvider);
                                  context.go('/mybooking');
                                },
                              ),
                            );
                          }
                        } else {
                          AppLogger.w(
                            'VA generation failed: ${vaResponse.message}',
                            'PAYMENT-PAGE',
                          );
                          if (mounted) {
                            // Show error dialog with custom style
                            await showNotificationDialog(
                              context,
                              '${localizations.vaGenerationFailedMessage}\n\n${vaResponse.message}',
                              title: localizations.vaGenerationFailedTitle,
                              defaultIcon: Icons.error_outline,
                              iconColor: Colors.red,
                              barrierDismissible: false,
                              onOkPressed: () {
                                context.go('/mybooking');
                              },
                            );
                          }
                        }
                      },
                      error: (error, stackTrace) async {
                        AppLogger.e(
                          'VA generation error',
                          error,
                          stackTrace,
                          'PAYMENT-PAGE',
                        );
                        if (mounted) {
                          // Show error dialog with custom style
                          await showNotificationDialog(
                            context,
                            '${localizations.vaGenerationErrorMessage}\n\n${error.toString()}',
                            title: localizations.vaGenerationErrorTitle,
                            defaultIcon: Icons.error_outline,
                            iconColor: Colors.red,
                            barrierDismissible: false,
                            onOkPressed: () {
                              // Refresh mybooking list before navigation
                              ref.invalidate(userBookingsProvider);
                              context.go('/mybooking');
                            },
                          );
                        }
                      },
                      loading: () async {
                        // Wait for the result
                        await Future.delayed(const Duration(milliseconds: 500));
                      },
                    );
                  }
                } else {
                  if (mounted) context.go('/mybooking');
                }
              } else if (_selectedTransactionValue == 'QRIS') {
                // Generate DOKU QRIS
                if (idrec != null) {
                  final user = authState.user.value;
                  if (user != null) {
                    // Use _confirmedTotal (set when user confirmed payment) — matches what was shown on screen
                    final paymentState = ref.read(paymentNotifierProvider);
                    final bookingData = paymentState.postBookingResult.value;

                    // Get order_id from booking response (use this for DOKU, not idrec)
                    final orderId = bookingData?['data']?['order_id']?.toString() ?? idrec;

                    final finalAmount = _confirmedTotal;

                    AppLogger.i(
                      'Generating DOKU QRIS for booking $idrec, order_id: $orderId, amount: $finalAmount (source: confirmedTotal)',
                      'PAYMENT-PAGE',
                    );

                    // Generate QRIS - use order_id, not idrec
                    await ref.read(dokuQRISNotifierProvider.notifier).generateQRIS(
                      orderId: orderId,
                      userName: user.firstName,
                      userEmail: user.email,
                      userPhone: user.phoneNumber,
                      amount: finalAmount,
                    );

                    // Check QRIS generation result
                    final qrisState = ref.read(dokuQRISNotifierProvider);
                    await qrisState.generateResult.when(
                      data: (qrisResponse) async {
                        if (qrisResponse.isSuccess && qrisResponse.data != null) {
                          AppLogger.s(
                            'QRIS generated successfully: ${qrisResponse.data!.referenceNo}',
                            'PAYMENT-PAGE',
                          );

                          // Update payment method in backend
                          // Get rent type from booking data
                          final bookingType = bookingData?['data']?['booking_type']?.toString().toLowerCase();
                          final isMonthly = bookingType == 'monthly';
                          // Get voucher data
                          final voucherState = ref.read(voucherNotifierProvider);
                          final updateRequest = UpdatePaymentMethodRequest(
                            paymentMethod: 'QRIS',
                            qrisReferenceNo: qrisResponse.data!.referenceNo,
                            depositFee: isMonthly ? _depositFee : null,
                            parkingFee: isMonthly && _parkingFee > 0 ? _parkingFee : null,
                            parkingType: isMonthly && _selectedParkingType != null
                                ? _selectedParkingType
                                : null,
                            parkingDuration: isMonthly && _parkingDuration > 0 ? _parkingDuration : null,
                            vehiclePlate: isMonthly && _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                ? _vehiclePlateController.text.trim()
                                : null,
                            ownerName: user.firstName,
                            ownerPhone: user.phoneNumber,
                            voucherCode: voucherState.appliedVoucherCode,
                            discountAmount: voucherState.discountAmount,
                          );

                          final updateResult = await ref
                              .read(paymentNotifierProvider.notifier)
                              .updatePaymentMethod(idrec.toString(), updateRequest);

                          switch (updateResult) {
                            case Success(:final data):
                              AppLogger.s(
                                'Payment method updated: ${data.message}',
                                'PAYMENT-PAGE',
                              );
                            case Failure(:final message):
                              AppLogger.w(
                                'Failed to update payment method: $message',
                                'PAYMENT-PAGE',
                              );
                              // Note: Don't block user flow, just log the error
                          }

                          // Save QR content to cache (30 minutes expiry)
                          final transactionDate = bookingData?['data']?['transaction_date'];
                          if (transactionDate != null) {
                            final createdAt = DateTime.parse(transactionDate);
                            final expiredAt = createdAt.add(const Duration(minutes: 30));

                            await PaymentCacheUtils.saveQRContent(
                              bookingId: idrec.toString(),
                              qrContent: qrisResponse.data!.qrContent,
                              orderId: orderId,
                              userId: user.id.toString(),
                              expiredAt: expiredAt,
                            );
                          }

                          // Show QRIS Result Dialog
                          if (mounted) {
                            await showDialog(
                              context: context,
                              barrierDismissible: false,
                              builder: (ctx) => QRISResultDialog(
                                qrisData: qrisResponse.data!,
                                amount: finalAmount,
                                onClose: () {
                                  Navigator.of(ctx).pop();
                                  // Refresh mybooking list before navigation
                                  ref.invalidate(userBookingsProvider);
                                  context.go('/mybooking');
                                },
                              ),
                            );
                          }
                        } else {
                          AppLogger.w(
                            'QRIS generation failed: ${qrisResponse.message}',
                            'PAYMENT-PAGE',
                          );
                          if (mounted) {
                            await showNotificationDialog(
                              context,
                              '${localizations.qrisGenerationFailedMessage}\n\n${qrisResponse.message}',
                              title: localizations.qrisGenerationFailedTitle,
                              defaultIcon: Icons.error_outline,
                              iconColor: Colors.red,
                              barrierDismissible: false,
                              onOkPressed: () {
                                context.go('/mybooking');
                              },
                            );
                          }
                        }
                      },
                      error: (error, stackTrace) async {
                        AppLogger.e(
                          'QRIS generation error',
                          error,
                          stackTrace,
                          'PAYMENT-PAGE',
                        );
                        if (mounted) {
                          await showNotificationDialog(
                            context,
                            '${localizations.qrisGenerationErrorMessage}\n\n${error.toString()}',
                            title: localizations.qrisGenerationErrorTitle,
                            defaultIcon: Icons.error_outline,
                            iconColor: Colors.red,
                            barrierDismissible: false,
                            onOkPressed: () {
                              // Refresh mybooking list before navigation
                              ref.invalidate(userBookingsProvider);
                              context.go('/mybooking');
                            },
                          );
                        }
                      },
                      loading: () async {
                        // Wait for the result
                        await Future.delayed(const Duration(milliseconds: 500));
                      },
                    );
                  }
                } else {
                  if (mounted) context.go('/mybooking');
                }
              } else if (_selectedTransactionValue == 'CREDITCARD') {
                // Generate DOKU CC Payment
                if (idrec != null) {
                  final user = authState.user.value;
                  if (user != null) {
                    // Get grandtotal and order_id from backend response (source of truth)
                    // Use _confirmedTotal (set when user confirmed payment) — matches what was shown on screen
                    final paymentState = ref.read(paymentNotifierProvider);
                    final bookingData = paymentState.postBookingResult.value;

                    // Get order_id from booking response (use this for DOKU, not idrec)
                    final orderId = bookingData?['data']?['order_id']?.toString() ?? idrec;

                    final finalAmount = _confirmedTotal;

                    AppLogger.i(
                      'Generating DOKU CC for booking $idrec, order_id: $orderId, amount: $finalAmount (source: confirmedTotal)',
                      'PAYMENT-PAGE',
                    );

                    // Generate CC - use order_id, not idrec
                    await ref.read(dokuCCNotifierProvider.notifier).generateCC(
                      orderId: orderId,
                      userName: user.firstName,
                      userEmail: user.email,
                      userPhone: user.phoneNumber,
                      amount: finalAmount,
                    );

                    AppLogger.d('CC generation completed, checking result state', 'PAYMENT-PAGE');

                    // Check CC generation result
                    final ccState = ref.read(dokuCCNotifierProvider);

                    AppLogger.d('CC state: ${ccState.generateResult}', 'PAYMENT-PAGE');

                    await ccState.generateResult.when(
                      data: (ccResponse) async {
                        AppLogger.d('CC when.data called - isSuccess: ${ccResponse.isSuccess}, hasData: ${ccResponse.data != null}', 'PAYMENT-PAGE');
                        if (ccResponse.isSuccess && ccResponse.data != null) {
                          AppLogger.s(
                            'CC payment URL generated successfully: ${ccResponse.data!.paymentUrl}',
                            'PAYMENT-PAGE',
                          );

                          // Update payment method in backend
                          // Get rent type from booking data
                          final bookingType = bookingData?['data']?['booking_type']?.toString().toLowerCase();
                          final isMonthly = bookingType == 'monthly';
                          // Get voucher data
                          final voucherState = ref.read(voucherNotifierProvider);
                          final updateRequest = UpdatePaymentMethodRequest(
                            paymentMethod: 'Credit Card',
                            creditCardInvoice: ccResponse.data!.invoiceNumber,
                            depositFee: isMonthly ? _depositFee : null,
                            parkingFee: isMonthly && _parkingFee > 0 ? _parkingFee : null,
                            parkingType: isMonthly && _selectedParkingType != null
                                ? _selectedParkingType
                                : null,
                            parkingDuration: isMonthly && _parkingDuration > 0 ? _parkingDuration : null,
                            vehiclePlate: isMonthly && _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                ? _vehiclePlateController.text.trim()
                                : null,
                            ownerName: user.firstName,
                            ownerPhone: user.phoneNumber,
                            voucherCode: voucherState.appliedVoucherCode,
                            discountAmount: voucherState.discountAmount,
                          );

                          final updateResult = await ref
                              .read(paymentNotifierProvider.notifier)
                              .updatePaymentMethod(idrec.toString(), updateRequest);

                          switch (updateResult) {
                            case Success(:final data):
                              AppLogger.s(
                                'Payment method updated: ${data.message}',
                                'PAYMENT-PAGE',
                              );
                            case Failure(:final message):
                              AppLogger.w(
                                'Failed to update payment method: $message',
                                'PAYMENT-PAGE',
                              );
                              // Note: Don't block user flow, just log the error
                          }

                          // Save CC payment link to cache (30 minutes expiry)
                          final transactionDate = bookingData?['data']?['transaction_date'];
                          if (transactionDate != null) {
                            final createdAt = DateTime.parse(transactionDate);
                            final expiredAt = createdAt.add(const Duration(minutes: 30));

                            await PaymentCacheUtils.saveCCLink(
                              bookingId: idrec.toString(),
                              paymentUrl: ccResponse.data!.paymentUrl,
                              invoiceNumber: ccResponse.data!.invoiceNumber,
                              userId: user.id.toString(),
                              expiredAt: expiredAt,
                            );
                          }

                          // Launch payment URL
                          final paymentUrl = ccResponse.data!.paymentUrl;
                          AppLogger.d('CC payment URL: $paymentUrl', 'PAYMENT-PAGE');

                          if (paymentUrl.isNotEmpty) {
                            final Uri uri = Uri.parse(paymentUrl);
                            AppLogger.d('Parsed URI: $uri', 'PAYMENT-PAGE');

                            final canLaunch = await canLaunchUrl(uri);
                            AppLogger.d('Can launch URL: $canLaunch', 'PAYMENT-PAGE');

                            if (canLaunch) {
                              AppLogger.i('Launching CC payment URL in external browser', 'PAYMENT-PAGE');
                              await launchUrl(uri, mode: LaunchMode.externalApplication);

                              Future.delayed(const Duration(seconds: 1), () {
                                if (mounted) context.go('/mybooking');
                              });
                            } else {
                              AppLogger.w('Cannot launch URL: $uri', 'PAYMENT-PAGE');
                              if (mounted) {
                                showNotificationDialog(
                                  context,
                                  'Gagal membuka halaman pembayaran.',
                                  defaultIcon: Icons.error_outline,
                                  iconColor: Colors.red,
                                );
                              }
                            }
                          } else {
                            AppLogger.w('CC payment URL is empty', 'PAYMENT-PAGE');
                            if (mounted) {
                              showNotificationDialog(
                                context,
                                'Payment URL tidak tersedia.',
                                defaultIcon: Icons.error_outline,
                                iconColor: Colors.red,
                              );
                            }
                          }
                        } else {
                          AppLogger.w(
                            'CC generation failed: ${ccResponse.message}',
                            'PAYMENT-PAGE',
                          );
                          if (mounted) {
                            await showDialog(
                              context: context,
                              barrierDismissible: false,
                              builder: (ctx) => _PaymentErrorDialog(
                                title: localizations.ccGenerationFailedTitle,
                                message: localizations.ccGenerationFailedMessage,
                                errorDetail: ccResponse.message,
                                note: localizations.ccGenerationFailedNote,
                                onClose: () {
                                  Navigator.of(ctx).pop();
                                  context.go('/mybooking');
                                },
                              ),
                            );
                          }
                        }
                      },
                      error: (error, stackTrace) async {
                        AppLogger.e(
                          'CC when.error called - error: $error',
                          error,
                          stackTrace,
                          'PAYMENT-PAGE',
                        );
                        if (mounted) {
                          await showDialog(
                            context: context,
                            barrierDismissible: false,
                            builder: (ctx) => _PaymentErrorDialog(
                              title: localizations.ccGenerationErrorTitle,
                              message: localizations.ccGenerationErrorMessage,
                              errorDetail: error.toString(),
                              note: localizations.ccGenerationErrorNote,
                              onClose: () {
                                Navigator.of(ctx).pop();
                                // Refresh mybooking list before navigation
                                ref.invalidate(userBookingsProvider);
                                context.go('/mybooking');
                              },
                            ),
                          );
                        }
                      },
                      loading: () async {
                        AppLogger.w('CC when.loading called - state is still loading', 'PAYMENT-PAGE');
                        // Wait for the result
                        await Future.delayed(const Duration(milliseconds: 500));
                      },
                    );
                  }
                } else {
                  if (mounted) context.go('/mybooking');
                }
              }
              // DISABLED: BRI Manual payment handling
              // else if (_selectedTransactionValue == 'Transfer Manual') {
              //   // BRI Manual - Direct update payment method (no VA generation needed)
              //   if (idrec != null) {
              //     AppLogger.i(
              //       'Processing BRI Manual payment for order $idrec',
              //       'PAYMENT-PAGE',
              //     );

              //     // Update payment method with fixed BRI VA
              //     final updateRequest = UpdatePaymentMethodRequest(
              //       paymentMethod: 'BRI Manual',
              //       virtualAccountNo: '050501001671567',
              //       paymentBank: 'BRI Manual',
              //     );

              //     final updateResult = await ref
              //         .read(paymentNotifierProvider.notifier)
              //         .updatePaymentMethod(idrec.toString(), updateRequest);

              //     switch (updateResult) {
              //       case Success(:final data):
              //         AppLogger.s(
              //           'BRI Manual payment method updated: ${data.message}',
              //           'PAYMENT-PAGE',
              //         );

              //         // Navigate to My Booking after successful update
              //         if (mounted) {
              //           context.go('/mybooking');
              //         }

              //       case Failure(:final message):
              //         AppLogger.w(
              //           'Failed to update BRI Manual payment method: $message',
              //           'PAYMENT-PAGE',
              //         );

              //         // Show error but still navigate (booking is created)
              //         if (mounted) {
              //           showNotificationDialog(
              //             context,
              //             'Booking berhasil dibuat. $message',
              //             defaultIcon: Icons.warning_outlined,
              //             iconColor: Colors.orange,
              //           );

              //           Future.delayed(const Duration(seconds: 2), () {
              //             if (mounted) context.go('/mybooking');
              //           });
              //         }
              //     }
              //   } else {
              //     if (mounted) context.go('/mybooking');
              //   }
              // }
            }
          },
          error: (err, st) {
            showNotificationDialog(
              context,
              '${localizations.paymentBookingFailed}: ${err.toString()}',
              defaultIcon: Icons.error_outline,
              iconColor: Colors.red,
            );
          },
        );
      },
    );



    return paymentState.paymentCalculationData.when(
      loading: () => MainLayout(
        currentIndex: 0,
        showBottomNav: false,
        showNavBar: false,
        child: Scaffold(
          backgroundColor: AppColors.primaryAdaptive(context).withOpacity(0.9), 
          body: const Center(child: CircularProgressIndicator(color: Colors.white)),
        ),
      ),
      error: (err, stack) => MainLayout(
        currentIndex: 0,
        showBottomNav: false,
        showNavBar: false,
        child: Scaffold(
          backgroundColor: AppColors.primaryAdaptive(context).withOpacity(0.9), 
          body: Center(
            child: Text('${localizations.paymentError}: ${err.toString()}', style: const TextStyle(color: Colors.white)), 
          ),
        ),
      ),
      data: (data) {
        final roomData = data['roomData'] as Map<String, dynamic>;
        final itemDetails = data['itemDetails'] as List<Map<String, dynamic>>;
        final afterOriginalTotalFees = (data['afterOriginalTotalFees'] as num?)?.toDouble();
        final user = authState.user.value;

        // Helper to get duration and rentType for both normal and renewal booking
        int getDuration(Map<String, dynamic> roomData) {
          if (widget.duration != null) return widget.duration!;
          // For renewal, get from calculated data
          return (roomData['booking_days'] as int? ?? roomData['booking_months'] as int? ?? 0);
        }

        String getRentType(Map<String, dynamic> roomData) {
          if (widget.rentType != null) return widget.rentType!;
          // For renewal, get from roomData
          return roomData['rentType']?.toString() ?? 'daily';
        }

        // Compute displayed Total Harga once — used for both display and DOKU gateway
        // For renewal daily: use multi-tier total from price-preview API directly
        // For new booking: calculateTotalPrice uses rawPrice from provider (already multi-tier via multiTierTotalPrice)
        final double _roomSubtotal = (widget.isRenewal &&
                (widget.rentType ?? '').toLowerCase() == 'daily' &&
                _renewalMultiTierTotalPrice != null)
            ? _renewalMultiTierTotalPrice! + 30000
            : ref.read(paymentNotifierProvider.notifier).calculateTotalPrice(itemDetails, afterOriginalTotalFees ?? 0);
        final double displayedTotal = _roomSubtotal
            - voucherNotifier.currentDiscount
            + ((!widget.isRenewal && _depositFee > 0) ? _depositFee : 0)
            + (getRentType(roomData).toLowerCase() == 'monthly' ? _parkingFee : 0);
        final String normalizedRentType = widget.rentType?.toLowerCase() ?? 'daily';
        final String durationUnit = (normalizedRentType == 'daily')
            ? localizations.filterSuffixDays
            : localizations.filterSuffixMonths;

        /// Translate booking type based on current locale
        /// 'monthly' -> 'bulanan' (ID) or 'Monthly' (EN)
        /// 'daily' -> 'harian' (ID) or 'Daily' (EN)
        String getLocalizedBookingType(String bookingType) {
          final locale = Localizations.localeOf(context).languageCode;
          final type = bookingType.toLowerCase();

          if (locale == 'id') {
            return type == 'monthly' ? 'bulanan' : 'harian';
          } else {
            return type == 'monthly' ? 'Monthly' : 'Daily';
          }
        }

        return MainLayout(
          currentIndex: 0,
          showBottomNav: false,
          showNavBar: false,
          child: Scaffold(
            backgroundColor: isDark ? const Color(0xFF111827) : Colors.white,
            appBar: AppBar(
              backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
              elevation: 2,
              shadowColor: Colors.black.withOpacity(0.1),
              leading: IconButton(
                icon: Icon(Icons.arrow_back, color: isDark ? Colors.white : Colors.black),
                onPressed: () => Navigator.of(context).pop(),
              ),
              title: Text(
                localizations.paymentPageTitle,
                style: textTheme.titleLarge?.copyWith(
                  fontWeight: FontWeight.bold,
                  color: isDark ? Colors.white : Colors.black,
                ),
              ),
              titleSpacing: 0,
            ),
            body: SafeArea(
              child: SingleChildScrollView(
                child: Padding(
                            padding: const EdgeInsets.all(16.0),
                            child: Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                      // Property Name - Bold and prominent
                                      Row(
                                        children: [
                                          Expanded(
                                            child: Text(
                                              roomData['propertyName']?.toString() ?? 'Property Name Not Available',
                                              style: textTheme.headlineMedium?.copyWith(
                                                fontWeight: FontWeight.bold,
                                                color: isDark ? Colors.white : Colors.black,
                                              ),
                                              maxLines: 2,
                                              overflow: TextOverflow.ellipsis,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 12),
                                      // Room Name
                                      Text(
                                        roomData['name']?.toString() ?? 'Room Name Not Available',
                                        style: textTheme.bodyLarge?.copyWith(
                                          color: Colors.grey[700],
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(localizations.paymentRentType, style: textTheme.bodyLarge),
                                          Text(getLocalizedBookingType(getRentType(roomData)), style: textTheme.bodyLarge),
                                        ],
                                      ),
                                      const SizedBox(height: 8),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(localizations.paymentDuration, style: textTheme.bodyLarge),
                                          Text(
                                            '${getDuration(roomData)} $durationUnit',
                                            style: textTheme.bodyLarge,
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 8),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(localizations.paymentCheckInDate, style: textTheme.bodyLarge), 
                                          Text(roomData['checkIn']?.toString() ?? '-', style: textTheme.bodyLarge),
                                        ],
                                      ),
                                      const SizedBox(height: 8),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(localizations.paymentCheckOutDate, style: textTheme.bodyLarge), 
                                          Text(roomData['checkOut']?.toString() ?? '-', style: textTheme.bodyLarge),
                                        ],
                                      ),
                                      const SizedBox(height: 16),

                                      // Deposit & Parking Fees Section (Only for Monthly Bookings)
                                      // Only show if at least one fee is available (> 0)
                                      if (getRentType(roomData).toLowerCase() == 'monthly' &&
                                          ((_depositFee > 0 && !widget.isRenewal) || _carParkingPrice > 0 || _motorcycleParkingPrice > 0)) ...[
                                        Text(
                                          localizations.paymentAdditionalFees,
                                          style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                                        ),
                                        const SizedBox(height: 12),

                                        // Deposit (Mandatory - Only for NEW bookings, NOT for renewal)
                                        // Only show if deposit fee > 0
                                        if (!widget.isRenewal && _depositFee > 0) ...[
                                          Container(
                                          padding: const EdgeInsets.all(12),
                                          decoration: BoxDecoration(
                                            border: Border.all(color: AppColors.primaryAdaptive(context).withOpacity(0.3)),
                                            borderRadius: BorderRadius.circular(8),
                                            color: AppColors.primaryAdaptive(context).withOpacity(0.05),
                                          ),
                                          child: Row(
                                            children: [
                                              Icon(Icons.money, color: AppColors.primaryAdaptive(context), size: 24),
                                              const SizedBox(width: 12),
                                              Expanded(
                                                child: Column(
                                                  crossAxisAlignment: CrossAxisAlignment.start,
                                                  children: [
                                                    Text(
                                                      localizations.paymentDepositRequired,
                                                      style: textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.bold),
                                                    ),
                                                    const SizedBox(height: 4),
                                                    Text(
                                                      localizations.paymentDepositNote,
                                                      style: textTheme.bodySmall?.copyWith(
                                                        color: Colors.grey[600],
                                                        fontStyle: FontStyle.italic,
                                                      ),
                                                    ),
                                                  ],
                                                ),
                                              ),
                                              Text(
                                                formatCurrency(_depositFee),
                                                style: textTheme.bodyLarge?.copyWith(
                                                  fontWeight: FontWeight.bold,
                                                  color: AppColors.primaryAdaptive(context),
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(height: 16),
                                        ],

                                      // Parking Fees (Optional)
                                      // Only show "Pilih Parkir (Opsional)" label if at least one parking option available
                                      if (_carParkingPrice > 0 || _motorcycleParkingPrice > 0) ...[
                                        Text(
                                          localizations.paymentSelectParking,
                                          style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                        ),
                                        const SizedBox(height: 8),
                                      ],


                                      // Parkir Mobil - Only show if price > 0
                                      if (_carParkingPrice > 0)
                                      InkWell(
                                        onTap: () {
                                          // Check if parking is full
                                          final availableSlots = _carParkingCapacity - _carParkingQuotaUsed;
                                          if (availableSlots <= 0) {
                                            // Don't allow selection if full
                                            return;
                                          }
                                          setState(() {
                                            // Toggle: if already selected, unselect it
                                            if (_selectedParkingType == 'car') {
                                              _selectedParkingType = null;
                                              _parkingFee = 0;
                                              _parkingDuration = 1;
                                              _vehiclePlateController.clear();
                                            } else {
                                              // Select car parking
                                              _selectedParkingType = 'car';
                                              _parkingDuration = 1;
                                              _parkingFee = _carParkingPrice * _parkingDuration;
                                            }
                                          });
                                        },
                                        child: Opacity(
                                          opacity: (_carParkingCapacity - _carParkingQuotaUsed) <= 0 ? 0.5 : 1.0,
                                          child: Container(
                                            padding: const EdgeInsets.all(12),
                                            decoration: BoxDecoration(
                                              border: Border.all(
                                                color: _selectedParkingType == 'car'
                                                    ? AppColors.primaryAdaptive(context)
                                                    : Colors.grey.withValues(alpha: 0.3),
                                                width: _selectedParkingType == 'car' ? 2 : 1,
                                              ),
                                              borderRadius: BorderRadius.circular(8),
                                              color: _selectedParkingType == 'car'
                                                  ? AppColors.primaryAdaptive(context).withValues(alpha: 0.05)
                                                  : (isDark ? const Color(0xFF374151) : Colors.white),
                                            ),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                children: [
                                                  Icon(
                                                    _selectedParkingType == 'car'
                                                        ? Icons.radio_button_checked
                                                        : Icons.radio_button_unchecked,
                                                    color: _selectedParkingType == 'car'
                                                        ? AppColors.primaryAdaptive(context)
                                                        : Colors.grey,
                                                  ),
                                                  const SizedBox(width: 12),
                                                  Icon(Icons.directions_car, color: AppColors.primaryAdaptive(context), size: 20),
                                                  const SizedBox(width: 8),
                                                  Expanded(
                                                    child: Text(
                                                      localizations.paymentCarParking,
                                                      style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                                    ),
                                                  ),
                                                  Text(
                                                    '${formatCurrency(_carParkingPrice)}/${getRentType(roomData).toLowerCase() == 'daily' ? localizations.paymentPerDay : localizations.paymentPerMonth}',
                                                    style: textTheme.bodyMedium?.copyWith(
                                                      fontWeight: FontWeight.w600,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              if (_carParkingCapacity > 0) ...[
                                                const SizedBox(height: 8),
                                                Padding(
                                                  padding: const EdgeInsets.only(left: 40),
                                                  child: () {
                                                    final availableSlots = _carParkingCapacity - _carParkingQuotaUsed;
                                                    final isFull = availableSlots <= 0;
                                                    return Text(
                                                      isFull
                                                        ? localizations.paymentParkingFull(0, _carParkingCapacity)
                                                        : localizations.paymentParkingAvailable(availableSlots, _carParkingCapacity),
                                                      style: textTheme.bodySmall?.copyWith(
                                                        color: isFull ? Colors.red : AppColors.primaryAdaptive(context),
                                                        fontWeight: FontWeight.w600,
                                                      ),
                                                    );
                                                  }(),
                                                ),
                                              ],
                                            ],
                                          ),
                                        ),
                                        ),
                                      ),
                                      const SizedBox(height: 8),

                                      // Parkir Motor - Only show if price > 0
                                      if (_motorcycleParkingPrice > 0)
                                      InkWell(
                                        onTap: () {
                                          // Check if parking is full
                                          final availableSlots = _motorcycleParkingCapacity - _motorcycleParkingQuotaUsed;
                                          if (availableSlots <= 0) {
                                            // Don't allow selection if full
                                            return;
                                          }
                                          setState(() {
                                            // Toggle: if already selected, unselect it
                                            if (_selectedParkingType == 'motorcycle') {
                                              _selectedParkingType = null;
                                              _parkingFee = 0;
                                              _parkingDuration = 1;
                                              _vehiclePlateController.clear();
                                            } else {
                                              // Select motorcycle parking
                                              _selectedParkingType = 'motorcycle';
                                              _parkingDuration = 1;
                                              _parkingFee = _motorcycleParkingPrice * _parkingDuration;
                                            }
                                          });
                                        },
                                        child: Opacity(
                                          opacity: (_motorcycleParkingCapacity - _motorcycleParkingQuotaUsed) <= 0 ? 0.5 : 1.0,
                                          child: Container(
                                            padding: const EdgeInsets.all(12),
                                            decoration: BoxDecoration(
                                              border: Border.all(
                                                color: _selectedParkingType == 'motorcycle'
                                                    ? AppColors.primaryAdaptive(context)
                                                    : Colors.grey[300]!,
                                                width: _selectedParkingType == 'motorcycle' ? 2 : 1,
                                              ),
                                              borderRadius: BorderRadius.circular(8),
                                              color: _selectedParkingType == 'motorcycle'
                                                  ? AppColors.primaryAdaptive(context).withAlpha(13)
                                                  : (isDark ? const Color(0xFF374151) : Colors.white),
                                            ),
                                          child: Column(
                                            crossAxisAlignment: CrossAxisAlignment.start,
                                            children: [
                                              Row(
                                                children: [
                                                  Icon(
                                                    _selectedParkingType == 'motorcycle'
                                                        ? Icons.radio_button_checked
                                                        : Icons.radio_button_unchecked,
                                                    color: _selectedParkingType == 'motorcycle'
                                                        ? AppColors.primaryAdaptive(context)
                                                        : Colors.grey,
                                                  ),
                                                  const SizedBox(width: 12),
                                                  Icon(Icons.two_wheeler, color: AppColors.primaryAdaptive(context), size: 20),
                                                  const SizedBox(width: 8),
                                                  Expanded(
                                                    child: Text(
                                                      localizations.paymentMotorcycleParking,
                                                      style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                                    ),
                                                  ),
                                                  Text(
                                                    '${formatCurrency(_motorcycleParkingPrice)}/${getRentType(roomData).toLowerCase() == 'daily' ? localizations.paymentPerDay : localizations.paymentPerMonth}',
                                                    style: textTheme.bodyMedium?.copyWith(
                                                      fontWeight: FontWeight.w600,
                                                    ),
                                                  ),
                                                ],
                                              ),
                                              if (_motorcycleParkingCapacity > 0) ...[
                                                const SizedBox(height: 8),
                                                Padding(
                                                  padding: const EdgeInsets.only(left: 40),
                                                  child: () {
                                                    final availableSlots = _motorcycleParkingCapacity - _motorcycleParkingQuotaUsed;
                                                    final isFull = availableSlots <= 0;
                                                    return Text(
                                                      isFull
                                                        ? localizations.paymentParkingFull(0, _motorcycleParkingCapacity)
                                                        : localizations.paymentParkingAvailable(availableSlots, _motorcycleParkingCapacity),
                                                      style: textTheme.bodySmall?.copyWith(
                                                        color: isFull ? Colors.red : AppColors.primaryAdaptive(context),
                                                        fontWeight: FontWeight.w600,
                                                      ),
                                                    );
                                                  }(),
                                                ),
                                              ],
                                            ],
                                          ),
                                        ),
                                        ),
                                      ),

                                      // Jumlah Bulan Parkir section - Show if parking is selected
                                      if (_selectedParkingType != null) ...[
                                        const SizedBox(height: 16),
                                        Text(
                                          localizations.paymentParkingDurationTitle,
                                          style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                        ),
                                        const SizedBox(height: 8),
                                        Container(
                                          padding: const EdgeInsets.all(12),
                                          decoration: BoxDecoration(
                                            color: isDark ? const Color(0xFF374151) : Colors.grey[50],
                                            borderRadius: BorderRadius.circular(8),
                                            border: Border.all(color: isDark ? Colors.grey.shade600 : Colors.grey[300]!),
                                          ),
                                          child: Column(
                                            children: [
                                              Row(
                                                children: [
                                                  IconButton(
                                                    icon: const Icon(Icons.remove_circle_outline),
                                                    onPressed: _parkingDuration > 1
                                                        ? () {
                                                            setState(() {
                                                              _parkingDuration--;
                                                              _parkingFee = (_selectedParkingType == 'car' ? _carParkingPrice : _motorcycleParkingPrice) * _parkingDuration;
                                                            });
                                                          }
                                                        : null,
                                                    color: AppColors.primaryAdaptive(context),
                                                  ),
                                                  Expanded(
                                                    child: Center(
                                                      child: Text(
                                                        '$_parkingDuration ${getRentType(roomData).toLowerCase() == 'daily' ? localizations.paymentPerDay : localizations.paymentPerMonth}',
                                                        style: textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.bold),
                                                      ),
                                                    ),
                                                  ),
                                                  IconButton(
                                                    icon: const Icon(Icons.add_circle_outline),
                                                    onPressed: _parkingDuration < getDuration(roomData)
                                                        ? () {
                                                            setState(() {
                                                              _parkingDuration++;
                                                              _parkingFee = (_selectedParkingType == 'car' ? _carParkingPrice : _motorcycleParkingPrice) * _parkingDuration;
                                                            });
                                                          }
                                                        : null,
                                                    color: AppColors.primaryAdaptive(context),
                                                  ),
                                                ],
                                              ),
                                              Text(
                                                '(${localizations.paymentMaxDuration(getDuration(roomData), getRentType(roomData).toLowerCase() == 'daily' ? localizations.paymentPerDay : localizations.paymentPerMonth)})',
                                                style: textTheme.bodySmall?.copyWith(
                                                  color: Colors.grey[600],
                                                  fontStyle: FontStyle.italic,
                                                ),
                                              ),
                                              const SizedBox(height: 12),
                                              const Divider(),
                                              const SizedBox(height: 8),
                                              Row(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                children: [
                                                  Text(
                                                    localizations.paymentTotalParkingFee,
                                                    style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                                  ),
                                                  Text(
                                                    formatCurrency(_parkingFee),
                                                    style: textTheme.bodyLarge?.copyWith(
                                                      fontWeight: FontWeight.bold,
                                                      color: AppColors.primaryAdaptive(context),
                                                    ),
                                                  ),
                                                ],
                                              ),
                                            ],
                                          ),
                                        ),

                                        // Detail Kendaraan section
                                        const SizedBox(height: 16),
                                        RichText(
                                          text: TextSpan(
                                            style: textTheme.bodyMedium?.copyWith(fontWeight: FontWeight.w600),
                                            children: [
                                              TextSpan(text: localizations.paymentVehicleDetailTitle),
                                              const TextSpan(
                                                text: ' *',
                                                style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
                                              ),
                                            ],
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        TextField(
                                          controller: _vehiclePlateController,
                                          decoration: InputDecoration(
                                            labelText: localizations.paymentVehiclePlateLabel,
                                            hintText: localizations.paymentVehiclePlateHint,
                                            helperText: localizations.paymentVehiclePlateHelper,
                                            helperStyle: const TextStyle(color: Colors.red, fontSize: 12),
                                            border: OutlineInputBorder(
                                              borderRadius: BorderRadius.circular(8),
                                            ),
                                            enabledBorder: OutlineInputBorder(
                                              borderRadius: BorderRadius.circular(8),
                                              borderSide: BorderSide(color: isDark ? Colors.grey.shade600 : Colors.grey[300]!),
                                            ),
                                            focusedBorder: OutlineInputBorder(
                                              borderRadius: BorderRadius.circular(8),
                                              // Remove const — primaryAdaptive(context) is not const
                                              borderSide: BorderSide(color: AppColors.primaryAdaptive(context), width: 2),
                                            ),
                                            errorBorder: OutlineInputBorder(
                                              borderRadius: BorderRadius.circular(8),
                                              borderSide: const BorderSide(color: Colors.red, width: 1),
                                            ),
                                            prefixIcon: Icon(
                                              _selectedParkingType == 'car' ? Icons.directions_car : Icons.two_wheeler,
                                              color: AppColors.primaryAdaptive(context),
                                            ),
                                          ),
                                          textCapitalization: TextCapitalization.characters,
                                          onChanged: (value) {
                                            // Trigger rebuild to update button state
                                            setState(() {});
                                          },
                                        ),
                                      ],

                                      const SizedBox(height: 16),
                                      ],

                                      // Voucher Section
                                      Text(
                                        localizations.paymentVoucherTitle,
                                        style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                                      ),
                                      const SizedBox(height: 8),
                                      if (voucherNotifier.hasAppliedVoucher)
                                        Container(
                                          padding: const EdgeInsets.all(16),
                                          decoration: BoxDecoration(
                                            border: Border.all(color: AppColors.primaryAdaptive(context).withOpacity(0.3)),
                                            borderRadius: BorderRadius.circular(8),
                                            color: AppColors.primaryAdaptive(context).withOpacity(0.05),
                                          ),
                                          child: Row(
                                            children: [
                                              Icon(
                                                Icons.check_circle,
                                                color: AppColors.primaryAdaptive(context),
                                              ),
                                              const SizedBox(width: 12),
                                              Expanded(
                                                child: Text(
                                                  '${localizations.paymentVoucherApplied} - ${voucherNotifier.appliedVoucherCode}',
                                                  style: textTheme.bodyMedium?.copyWith(
                                                    color: AppColors.primaryAdaptive(context),
                                                    fontWeight: FontWeight.bold,
                                                  ),
                                                ),
                                              ),
                                              IconButton(
                                                icon: Icon(Icons.close, color: Colors.grey.shade600),
                                                onPressed: _removeVoucher,
                                                padding: EdgeInsets.zero,
                                                constraints: const BoxConstraints(),
                                              ),
                                            ],
                                          ),
                                        )
                                      else
                                        Row(
                                          children: [
                                            Expanded(
                                              child: TextField(
                                                controller: _voucherController,
                                                decoration: InputDecoration(
                                                  hintText: localizations.paymentVoucherPlaceholder,
                                                  border: OutlineInputBorder(
                                                    borderRadius: BorderRadius.circular(8),
                                                  ),
                                                  enabledBorder: OutlineInputBorder(
                                                    borderRadius: BorderRadius.circular(8),
                                                    borderSide: BorderSide(color: Colors.grey.shade300),
                                                  ),
                                                  focusedBorder: OutlineInputBorder(
                                                    borderRadius: BorderRadius.circular(8),
                                                    borderSide: BorderSide(color: AppColors.primaryAdaptive(context), width: 2),
                                                  ),
                                                  prefixIcon: Icon(Icons.local_offer, color: AppColors.primaryAdaptive(context)),
                                                  contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
                                                ),
                                                textCapitalization: TextCapitalization.characters,
                                              ),
                                            ),
                                            const SizedBox(width: 8),
                                            ElevatedButton(
                                              onPressed: isVoucherLoading
                                                  ? null
                                                  : () => _applyVoucher(
                                                      roomData: roomData,
                                                      itemDetails: itemDetails,
                                                      basePrice: afterOriginalTotalFees ?? 0,
                                                    ),
                                              style: ElevatedButton.styleFrom(
                                                backgroundColor: AppColors.primaryAdaptive(context),
                                                foregroundColor: Colors.white,
                                                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
                                                shape: RoundedRectangleBorder(
                                                  borderRadius: BorderRadius.circular(8),
                                                ),
                                              ),
                                              child: isVoucherLoading
                                                  ? const SizedBox(
                                                      width: 20,
                                                      height: 20,
                                                      child: CircularProgressIndicator(
                                                        strokeWidth: 2,
                                                        valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                                      ),
                                                    )
                                                  : Text(
                                                      localizations.paymentVoucherApplyButton,
                                                      style: textTheme.labelLarge?.copyWith(
                                                        color: Colors.white,
                                                        fontWeight: FontWeight.bold,
                                                      ),
                                                    ),
                                            ),
                                          ],
                                        ),
                                      const SizedBox(height: 16),

                                      Text(
                                        localizations.paymentPriceDetails,
                                        style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                                      ),
                                      const SizedBox(height: 8),

                                      // Daily pricing: show per-day breakdown if available
                                      // For renewal, use state vars fetched from price-preview; for new bookings use widget props
                                      if ((getRentType(roomData) == 'daily' || getRentType(roomData) == 'Daily')
                                          && (widget.multiTierBreakdown ?? _renewalMultiTierBreakdown) != null
                                          && (widget.multiTierBreakdown ?? _renewalMultiTierBreakdown)!.isNotEmpty
                                          && (widget.multiTierTotalPrice ?? _renewalMultiTierTotalPrice) != null) ...[
                                        DailyPriceBreakdown(
                                          breakdown: (widget.multiTierBreakdown ?? _renewalMultiTierBreakdown)!,
                                          totalPrice: (widget.multiTierTotalPrice ?? _renewalMultiTierTotalPrice)!,
                                        ),
                                      ] else ...[
                                        // Monthly or flat rate fallback: show single price + duration
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                getRentType(roomData) == 'daily' || getRentType(roomData) == 'Daily'
                                                    ? localizations.paymentDailyPrice
                                                    : localizations.paymentMonthlyPrice,
                                                style: textTheme.bodyLarge,
                                              ),
                                              Text(
                                                getRentType(roomData) == 'daily' || getRentType(roomData) == 'Daily'
                                                    ? formatCurrency(roomData['daily_price'] ?? 0.0)
                                                    : formatCurrency(roomData['monthly_price'] ?? 0.0),
                                                style: textTheme.bodyLarge,
                                              ),
                                            ],
                                          ),
                                        ),
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(localizations.confirmationDialogDuration, style: textTheme.bodyLarge),
                                              Text('${getDuration(roomData)} $durationUnit', style: textTheme.bodyLarge),
                                            ],
                                          ),
                                        ),
                                        const Divider(height: 20, thickness: 0.5, color: Colors.grey),
                                        Builder(
                                          builder: (context) {
                                            final basePrice = widget.rentType == 'daily' || widget.rentType == 'Daily'
                                                ? roomData['daily_price'] ?? 0.0
                                                : roomData['monthly_price'] ?? 0.0;
                                            final duration = getDuration(roomData);
                                            final subtotal = basePrice * duration;
                                            return Padding(
                                              padding: const EdgeInsets.symmetric(vertical: 4.0),
                                              child: Row(
                                                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                                children: [
                                                  Text('Subtotal', style: textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.w600)),
                                                  Text(formatCurrency(subtotal), style: textTheme.bodyLarge?.copyWith(fontWeight: FontWeight.w600)),
                                                ],
                                              ),
                                            );
                                          },
                                        ),
                                      ],

                                      // 4. Voucher Discount (if applied)
                                      if (voucherNotifier.hasAppliedVoucher && voucherNotifier.currentDiscount > 0)
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                '${localizations.paymentVoucherTitle} (${voucherNotifier.appliedVoucherCode})',
                                                style: textTheme.bodyLarge?.copyWith(
                                                  color: AppColors.primaryAdaptive(context),
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                              Text(
                                                '- ${formatCurrency(voucherNotifier.currentDiscount)}',
                                                style: textTheme.bodyLarge?.copyWith(
                                                  color: AppColors.primaryAdaptive(context),
                                                  fontWeight: FontWeight.bold,
                                                ),
                                              ),
                                            ],
                                          ),
                                        ),

                                      const Divider(height: 20, thickness: 0.5, color: Colors.grey),

                                      // Display ONLY Service Fee (tax) from itemDetails
                                      ...itemDetails.where((item) {
                                        String itemType = item['type']?.toString() ?? '';
                                        return itemType == 'tax'; // Only show tax/service fee
                                      }).map((item) {
                                        String priceString = item['price']?.toString() ?? '0';
                                        String cleanPriceString = priceString.replaceAll(RegExp(r'[^\d]'), '');
                                        double priceValue = double.tryParse(cleanPriceString) ?? 0.0;

                                        return Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                item['name']?.toString() ?? localizations.paymentFee,
                                                style: textTheme.bodyLarge,
                                              ),
                                              Text(
                                                formatCurrency(priceValue),
                                                style: textTheme.bodyLarge,
                                              ),
                                            ],
                                          ),
                                        );
                                      }),

                                      // Deposit Fee — show for any rent type if deposit > 0 and NOT renewal
                                      if (_depositFee > 0 && !widget.isRenewal)
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                'Deposit',
                                                style: textTheme.bodyLarge,
                                              ),
                                              Text(
                                                formatCurrency(_depositFee),
                                                style: textTheme.bodyLarge,
                                              ),
                                            ],
                                          ),
                                        ),

                                      // Parking Fee (only for monthly bookings and if selected)
                                      if (getRentType(roomData).toLowerCase() == 'monthly' && _parkingFee > 0)
                                        Padding(
                                          padding: const EdgeInsets.symmetric(vertical: 4.0),
                                          child: Row(
                                            mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                            children: [
                                              Text(
                                                _selectedParkingType == 'car'
                                                    ? localizations.paymentCarParking
                                                    : localizations.paymentMotorcycleParking,
                                                style: textTheme.bodyLarge,
                                              ),
                                              Text(
                                                formatCurrency(_parkingFee),
                                                style: textTheme.bodyLarge,
                                              ),
                                            ],
                                          ),
                                        ),

                                      const SizedBox(height: 8),
                                      const Divider(height: 15, thickness: 1),
                                      Row(
                                        mainAxisAlignment: MainAxisAlignment.spaceBetween,
                                        children: [
                                          Text(
                                            localizations.paymentTotalPrice,
                                            style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                                          ),
                                          Text(
                                            formatCurrency(displayedTotal),
                                            style: textTheme.titleLarge?.copyWith(
                                              fontWeight: FontWeight.bold,
                                              // Bright orange in dark mode for readability, red in light mode
                                              color: isDark ? const Color(0xFFFF9500) : AppColors.secondaryColor,
                                            ),
                                          ),
                                        ],
                                      ),
                                      const SizedBox(height: 24),

                                      // Metode Pembayaran Section (moved below Total Harga)
                                      RichText(
                                        text: TextSpan(
                                          style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                                          children: [
                                            TextSpan(text: localizations.paymentMethodTitle),
                                            const TextSpan(
                                              text: ' *',
                                              style: TextStyle(color: Colors.red, fontWeight: FontWeight.bold),
                                            ),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(height: 8),
                                      Column(
                                        children: [
                                          ...List.generate(paymentMethods.length, (index) {
                                            final method = paymentMethods[index];
                                            final isTransferVA = method['value'] == 'Transfer VA';

                                            return Column(
                                              children: [
                                                PaymentMethodItem(
                                                  iconAsset: method['iconAsset'],
                                                  title: method['title'],
                                                  subtitle: method['subtitle'] ?? '',
                                                  isSelected: _selectedPaymentMethodIndex == index,
                                                  onTap: () {
                                                    setState(() {
                                                      _selectedPaymentMethodIndex = index;
                                                      _selectedTransactionValue = method['value'];
                                                    });
                                                  },
                                                ),

                                                // Show bank selection when Transfer VA is selected
                                                if (isTransferVA && _selectedPaymentMethodIndex == index) ...[
                                                  const SizedBox(height: 8),
                                                  Padding(
                                                    padding: const EdgeInsets.symmetric(horizontal: 8.0),
                                                    child: Consumer(
                                                      builder: (context, ref, child) {
                                                        final dokuVAState = ref.watch(dokuVANotifierProvider);
                                                        return BankSelectionWidget(
                                                          selectedBank: dokuVAState.selectedBank,
                                                          onBankSelected: (bank) {
                                                            ref.read(dokuVANotifierProvider.notifier).selectBank(bank);
                                                          },
                                                        );
                                                      },
                                                    ),
                                                  ),
                                                  const SizedBox(height: 8),
                                                ],
                                              ],
                                            );
                                          }),
                                        ],
                                      ),

                                      const SizedBox(height: 24),
                                      // Terms and Conditions Checkbox
                                      Container(
                                        padding: const EdgeInsets.all(12),
                                        decoration: BoxDecoration(
                                          // Dark mode: gunakan surface gelap, light mode: amber[50]
                                          color: isDark ? AppColors.surfaceDarkElevated : Colors.amber[50],
                                          borderRadius: BorderRadius.circular(8),
                                          border: Border.all(
                                            color: isDark ? Colors.white24 : Colors.amber[200]!,
                                          ),
                                        ),
                                        child: Row(
                                          crossAxisAlignment: CrossAxisAlignment.start,
                                          children: [
                                            Checkbox(
                                              value: _agreedToTerms,
                                              onChanged: (bool? newValue) {
                                                setState(() {
                                                  _agreedToTerms = newValue ?? false;
                                                });
                                              },
                                              activeColor: AppColors.primaryAdaptive(context),
                                              // Dark mode: border checkbox putih supaya keliatan
                                              side: isDark
                                                  ? const BorderSide(color: Colors.white70, width: 2)
                                                  : null,
                                            ),
                                            Expanded(
                                              child: Padding(
                                                padding: const EdgeInsets.only(top: 12, right: 8),
                                                child: RichText(
                                                  text: TextSpan(
                                                    style: TextStyle(
                                                      fontSize: 13,
                                                      color: isDark ? Colors.white : Colors.black87,
                                                    ),
                                                    children: [
                                                      WidgetSpan(
                                                        child: GestureDetector(
                                                          onTap: () => setState(() => _agreedToTerms = !_agreedToTerms),
                                                          child: Text(
                                                            localizations.paymentTermsAgreePrefix,
                                                            style: TextStyle(
                                                              fontSize: 13,
                                                              color: isDark ? Colors.white : Colors.black87,
                                                            ),
                                                          ),
                                                        ),
                                                      ),
                                                      WidgetSpan(
                                                        child: GestureDetector(
                                                          onTap: () async {
                                                            await showDialog<bool>(
                                                              context: context,
                                                              builder: (BuildContext context) {
                                                                return const TermsAndConditionsDialog(isTerms: true, isPrivacy: false);
                                                              },
                                                            );
                                                          },
                                                          child: Text(
                                                            localizations.paymentTermsConditions,
                                                            style: TextStyle(
                                                              fontSize: 13,
                                                              color: AppColors.primaryAdaptive(context),
                                                              fontWeight: FontWeight.bold,
                                                              decoration: TextDecoration.underline,
                                                              decorationColor: AppColors.primaryAdaptive(context),
                                                            ),
                                                          ),
                                                        ),
                                                      ),
                                                      TextSpan(text: localizations.paymentTermsAnd),
                                                      WidgetSpan(
                                                        child: GestureDetector(
                                                          onTap: () async {
                                                            await showDialog<bool>(
                                                              context: context,
                                                              builder: (BuildContext context) {
                                                                return const TermsAndConditionsDialog(isTerms: false, isPrivacy: true);
                                                              },
                                                            );
                                                          },
                                                          child: Text(
                                                            localizations.paymentPrivacyPolicy,
                                                            style: TextStyle(
                                                              fontSize: 13,
                                                              color: AppColors.primaryAdaptive(context),
                                                              fontWeight: FontWeight.bold,
                                                              decoration: TextDecoration.underline,
                                                              decorationColor: AppColors.primaryAdaptive(context),
                                                            ),
                                                          ),
                                                        ),
                                                      ),
                                                      // ", dan " / ", and "
                                                      TextSpan(text: localizations.paymentTermsAndRental),
                                                      // "Perjanjian Sewa" link
                                                      WidgetSpan(
                                                        child: GestureDetector(
                                                          onTap: () async {
                                                            await showDialog<bool>(
                                                              context: context,
                                                              builder: (BuildContext context) {
                                                                return const TermsAndConditionsDialog(isTerms: true, isPrivacy: false);
                                                              },
                                                            );
                                                          },
                                                          child: Text(
                                                            localizations.paymentRentalAgreement,
                                                            style: TextStyle(
                                                              fontSize: 13,
                                                              color: AppColors.primaryAdaptive(context),
                                                              fontWeight: FontWeight.bold,
                                                              decoration: TextDecoration.underline,
                                                              decorationColor: AppColors.primaryAdaptive(context),
                                                            ),
                                                          ),
                                                        ),
                                                      ),
                                                      const TextSpan(text: '.'),
                                                    ],
                                                  ),
                                                ),
                                              ),
                                            ),
                                          ],
                                        ),
                                      ),
                                      const SizedBox(height: 16),
                                      Center(
                                        child: FractionallySizedBox(
                                          widthFactor: 0.925,
                                          child: Consumer(
                                            builder: (context, ref, child) {
                                              final dokuVAState = ref.watch(dokuVANotifierProvider);
                                              final selectedBank = dokuVAState.selectedBank;

                                              // Watch for loading states
                                              final renewalState = ref.watch(renewBookingProvider);
                                              final paymentState = ref.watch(paymentNotifierProvider);
                                              final isProcessing = renewalState.isLoading ||
                                                                   paymentState.postBookingResult.isLoading;

                                              // Check if Transfer VA is selected but no bank chosen
                                              final isTransferVASelected = _selectedTransactionValue == 'Transfer VA';
                                              final isBankSelected = isTransferVASelected ? selectedBank != null : true;

                                              // Check if parking is selected but vehicle plate not filled
                                              final isParkingSelected = _selectedParkingType != null && _parkingFee > 0;
                                              final isVehiclePlateFilled = isParkingSelected
                                                  ? (_vehiclePlateController.text.trim().isNotEmpty)
                                                  : true;

                                              final isButtonEnabled = _agreedToTerms &&
                                                                      _selectedPaymentMethodIndex != null &&
                                                                      isBankSelected &&
                                                                      isVehiclePlateFilled &&
                                                                      !isProcessing;

                                              return ElevatedButton(
                                                onPressed: isButtonEnabled
                                                    ? () {
                                                  // Use the same displayedTotal shown on screen — single source of truth
                                                  setState(() => _confirmedTotal = displayedTotal);

                                                  showDialog(
                                                    context: context,
                                                    builder: (ctx) => ConfirmationDialog(
                                                      roomData: roomData,
                                                      itemDetails: itemDetails,
                                                      rentType: getRentType(roomData),
                                                      duration: getDuration(roomData),
                                                      totalHarga: displayedTotal,
                                                      voucherCode: voucherNotifier.appliedVoucherCode,
                                                      voucherDiscount: voucherNotifier.currentDiscount > 0 ? voucherNotifier.currentDiscount : null,
                                                      originalTotal: voucherNotifier.currentDiscount > 0 ? _roomSubtotal : null,
                                                      depositFee: widget.isRenewal ? 0 : _depositFee,
                                                      parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                      parkingType: _selectedParkingType != null
                                                          ? _selectedParkingType
                                                          : null,
                                                      parkingDuration: _parkingFee > 0 ? _parkingDuration : null,
                                                      // Pass multi-tier subtotal: renewal uses state var, new booking uses widget prop
                                                      multiTierSubtotal: widget.multiTierTotalPrice ?? _renewalMultiTierTotalPrice,
                                                      onConfirm: () async {
                                                        final selectedTransactionType = paymentMethods[_selectedPaymentMethodIndex!]['value'];

                                                        final user = ref.read(authProvider).user.value;
                                                        if (user == null) {
                                                          showNotificationDialog(
                                                            context,
                                                            'User belum login',
                                                            defaultIcon: Icons.error_outline,
                                                            iconColor: Colors.red,
                                                          );
                                                          return;
                                                        }

                                                        // Check if this is a renewal booking
                                                        if (widget.isRenewal && widget.originalOrderId != null) {
                                                          AppLogger.d('Using renew booking API for order: ${widget.originalOrderId}', 'PAYMENT-PAGE');

                                                          // Get data from widget and roomData
                                                          final rentType = getRentType(roomData);
                                                          final duration = getDuration(roomData);
                                                          final isMonthly = rentType.toLowerCase() == 'monthly';

                                                          // Calculate prices and fees
                                                          final pricePerUnit = isMonthly
                                                              ? (widget.monthlyPrice ?? (roomData['monthly_price'] as num?)?.toDouble() ?? 0.0)
                                                              : (widget.dailyPrice ?? (roomData['daily_price'] as num?)?.toDouble() ?? 0.0);

                                                          final adminFees = (roomData['admin_fee'] as num?)?.toDouble();
                                                          final serviceFees = (roomData['service_fees'] as num?)?.toDouble() ?? 0.0;

                                                          // Get property type from roomData or default
                                                          final propertyType = roomData['property_type']?.toString() ?? 'Kos';

                                                          // Step 1: Renew booking (get new order ID)
                                                          // Note: Renewal does NOT include deposit (only parking is optional)
                                                          final renewalResponse = await ref.read(renewBookingProvider.notifier).renewBooking(
                                                            orderId: widget.originalOrderId!,
                                                            userId: user.id,
                                                            userName: user.firstName,
                                                            userPhoneNumber: user.phoneNumber,
                                                            userEmail: user.email,
                                                            propertyId: widget.propertyId ?? 0,
                                                            propertyName: widget.propertyName ?? '',
                                                            propertyType: propertyType,
                                                            roomId: widget.roomId ?? 0,
                                                            roomName: widget.roomName ?? '',
                                                            bookingType: isMonthly ? 'monthly' : 'daily',
                                                            checkIn: widget.newCheckIn!,
                                                            checkOut: widget.newCheckOut!,
                                                            monthlyPrice: isMonthly ? pricePerUnit : null,
                                                            bookingMonths: isMonthly ? duration : null,
                                                            adminFees: adminFees,
                                                            serviceFees: serviceFees,
                                                            transactionType: selectedTransactionType.toString(),
                                                            voucherCode: voucherNotifier.appliedVoucherCode,
                                                            parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                            parkingType: _selectedParkingType != null && _selectedParkingType!.isNotEmpty ? _selectedParkingType : null,
                                                            parkingDuration: _parkingDuration > 0 ? _parkingDuration : null,
                                                          );

                                                          if (renewalResponse == null) {
                                                            if (context.mounted) {
                                                              final renewalState = ref.read(renewBookingProvider);
                                                              final errorMessage = renewalState.hasError
                                                                ? renewalState.error.toString()
                                                                : 'Gagal memperpanjang booking. Silakan coba lagi.';

                                                              showNotificationDialog(
                                                                context,
                                                                errorMessage,
                                                                defaultIcon: Icons.error_outline,
                                                                iconColor: Colors.red,
                                                              );
                                                            }
                                                            return;
                                                          }

                                                          // Get the new booking ID and order ID directly from response
                                                          final newBookingId = renewalResponse.data?.bookingId;
                                                          final newOrderId = renewalResponse.data?.orderId;

                                                          // Debug logging
                                                          AppLogger.d('=== RENEWAL DEBUG ===', 'PAYMENT-PAGE');
                                                          AppLogger.d('Renewal Response: $renewalResponse', 'PAYMENT-PAGE');
                                                          AppLogger.d('Renewal Response data: ${renewalResponse.data}', 'PAYMENT-PAGE');
                                                          AppLogger.d('New Booking ID: "$newBookingId"', 'PAYMENT-PAGE');
                                                          AppLogger.d('New Order ID: "$newOrderId"', 'PAYMENT-PAGE');

                                                          if (newBookingId == null || newBookingId.isEmpty) {
                                                            if (context.mounted) {
                                                              showNotificationDialog(
                                                                context,
                                                                'Gagal mendapatkan booking ID baru. Silakan coba lagi.',
                                                                defaultIcon: Icons.error_outline,
                                                                iconColor: Colors.red,
                                                              );
                                                            }
                                                            return;
                                                          }

                                                          AppLogger.s('Renewal booking successful. New Booking ID: $newBookingId, Order ID: $newOrderId', 'PAYMENT-PAGE');

                                                          // Add small delay to ensure booking is committed to database
                                                          await Future.delayed(const Duration(milliseconds: 500));

                                                          // Step 2 & 3: Generate payment and update payment method based on selected type
                                                          // DISABLED: BRI Manual payment handling for renewal
                                                          // if (selectedTransactionType == 'BRI Manual') {
                                                          //   // For BRI Manual, only update payment method, no generation needed
                                                          //   // Use booking ID for API call
                                                          //   // Include parking info if selected (renewal doesn't have deposit)
                                                          //   final updateRequest = UpdatePaymentMethodRequest(
                                                          //     paymentMethod: 'BRI Manual',
                                                          //     paymentBank: 'BRI Manual',
                                                          //     parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                          //     parkingType: _selectedParkingType != null && _selectedParkingType!.isNotEmpty
                                                          //         ? (_selectedParkingType == 'car' ? 'Mobil' : 'Motor')
                                                          //         : null,
                                                          //     parkingDuration: _parkingDuration > 0 ? _parkingDuration : null,
                                                          //   );

                                                          //   final updateResult = await ref
                                                          //       .read(paymentNotifierProvider.notifier)
                                                          //       .updatePaymentMethod(newBookingId, updateRequest);

                                                          //   if (context.mounted) {
                                                          //     if (updateResult is Success) {
                                                          //       AppLogger.s('Payment method updated for BRI Manual', 'PAYMENT-PAGE');
                                                          //       // Refresh mybooking list before navigation
                                                          //       ref.invalidate(userBookingsProvider);
                                                          //       context.go('/mybooking');
                                                          //     } else {
                                                          //       showNotificationDialog(
                                                          //         context,
                                                          //         'Gagal mengupdate metode pembayaran.',
                                                          //         defaultIcon: Icons.error_outline,
                                                          //         iconColor: Colors.red,
                                                          //       );
                                                          //     }
                                                          //   }
                                                          // } else
                                                          if (selectedTransactionType == 'Transfer VA') {
                                                            // Generate VA
                                                            final dokuVAState = ref.read(dokuVANotifierProvider);
                                                            final selectedBank = dokuVAState.selectedBank;

                                                            if (selectedBank == null) {
                                                              if (context.mounted) {
                                                                showNotificationDialog(
                                                                  context,
                                                                  'Pilih bank terlebih dahulu',
                                                                  defaultIcon: Icons.error_outline,
                                                                  iconColor: Colors.red,
                                                                );
                                                              }
                                                              return;
                                                            }

                                                            // Use _confirmedTotal — set when user confirmed, matches displayed price
                                                            // Backend grandTotal uses flat rate, not multi-tier
                                                            final amount = _confirmedTotal;

                                                            // Use order_id if available, fallback to booking_id
                                                            final orderIdForDoku = newOrderId ?? newBookingId;
                                                            AppLogger.i('Generating VA for renewal - booking_id: $newBookingId, order_id: $orderIdForDoku, bank: $selectedBank', 'PAYMENT-PAGE');

                                                            await ref.read(dokuVANotifierProvider.notifier).generateVA(
                                                              orderId: orderIdForDoku,
                                                              userName: user.firstName,
                                                              userEmail: user.email,
                                                              userPhone: user.phoneNumber,
                                                              amount: amount,
                                                              bank: selectedBank,
                                                            );

                                                            // Check VA generation result
                                                            final vaState = ref.read(dokuVANotifierProvider);
                                                            await vaState.generateResult.when(
                                                              data: (vaResponse) async {
                                                                if (vaResponse.isSuccess && vaResponse.data != null) {
                                                                  // Update payment method (renewal doesn't include deposit or voucher)
                                                                  final updateRequest = UpdatePaymentMethodRequest(
                                                                    paymentMethod: 'Transfer VA',
                                                                    virtualAccountNo: vaResponse.data!.virtualAccountNo,
                                                                    paymentBank: selectedBank.toUpperCase(),
                                                                    parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                                    parkingType: _selectedParkingType != null && _selectedParkingType!.isNotEmpty
                                                                        ? _selectedParkingType
                                                                        : null,
                                                                    parkingDuration: _parkingDuration > 0 ? _parkingDuration : null,
                                                                    vehiclePlate: _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                                                        ? _vehiclePlateController.text.trim()
                                                                        : null,
                                                                    ownerName: user.firstName,
                                                                    ownerPhone: user.phoneNumber,
                                                                  );

                                                                  await ref
                                                                      .read(paymentNotifierProvider.notifier)
                                                                      .updatePaymentMethod(newBookingId, updateRequest);

                                                                  // Show VA Result Dialog
                                                                  if (context.mounted) {
                                                                    await showDialog(
                                                                      context: context,
                                                                      barrierDismissible: false,
                                                                      builder: (ctx) => VAResultDialog(
                                                                        vaData: vaResponse.data!,
                                                                        onClose: () {
                                                                          Navigator.of(ctx).pop();
                                                                          // Refresh mybooking list before navigation
                                                                          ref.invalidate(userBookingsProvider);
                                                                          context.go('/mybooking');
                                                                        },
                                                                      ),
                                                                    );
                                                                  }
                                                                } else {
                                                                  if (context.mounted) {
                                                                    showNotificationDialog(
                                                                      context,
                                                                      'VA generation failed: ${vaResponse.message}',
                                                                      defaultIcon: Icons.error_outline,
                                                                      iconColor: Colors.red,
                                                                    );
                                                                    // Refresh mybooking list before navigation
                                                                    ref.invalidate(userBookingsProvider);
                                                                    context.go('/mybooking');
                                                                  }
                                                                }
                                                              },
                                                              error: (error, stackTrace) async {
                                                                if (context.mounted) {
                                                                  showNotificationDialog(
                                                                    context,
                                                                    'VA generation error: $error',
                                                                    defaultIcon: Icons.error_outline,
                                                                    iconColor: Colors.red,
                                                                  );
                                                                  // Refresh mybooking list before navigation
                                                                  ref.invalidate(userBookingsProvider);
                                                                  context.go('/mybooking');
                                                                }
                                                              },
                                                              loading: () {},
                                                            );
                                                          } else if (selectedTransactionType == 'QRIS') {
                                                            // Generate QRIS
                                                            // Use _confirmedTotal — set when user confirmed, matches displayed price
                                                            // Backend grandTotal uses flat rate, not multi-tier
                                                            final amount = _confirmedTotal;

                                                            // Use order_id if available, fallback to booking_id
                                                            final orderIdForDoku = newOrderId ?? newBookingId;
                                                            AppLogger.i('Generating QRIS for renewal - booking_id: $newBookingId, order_id: $orderIdForDoku', 'PAYMENT-PAGE');

                                                            await ref.read(dokuQRISNotifierProvider.notifier).generateQRIS(
                                                              orderId: orderIdForDoku,
                                                              userName: user.firstName,
                                                              userEmail: user.email,
                                                              userPhone: user.phoneNumber,
                                                              amount: amount,
                                                            );

                                                            // Check QRIS generation result
                                                            final qrisState = ref.read(dokuQRISNotifierProvider);
                                                            await qrisState.generateResult.when(
                                                              data: (qrisResponse) async {
                                                                if (qrisResponse.isSuccess && qrisResponse.data != null) {
                                                                  // Update payment method (renewal doesn't include deposit or voucher)
                                                                  final updateRequest = UpdatePaymentMethodRequest(
                                                                    paymentMethod: 'QRIS',
                                                                    qrisReferenceNo: qrisResponse.data!.referenceNo,
                                                                    parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                                    parkingType: _selectedParkingType != null && _selectedParkingType!.isNotEmpty
                                                                        ? _selectedParkingType
                                                                        : null,
                                                                    parkingDuration: _parkingDuration > 0 ? _parkingDuration : null,
                                                                    vehiclePlate: _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                                                        ? _vehiclePlateController.text.trim()
                                                                        : null,
                                                                    ownerName: user.firstName,
                                                                    ownerPhone: user.phoneNumber,
                                                                  );

                                                                  await ref
                                                                      .read(paymentNotifierProvider.notifier)
                                                                      .updatePaymentMethod(newBookingId, updateRequest);

                                                                  // Save QR content to cache (30 minutes expiry)
                                                                  final createdAt = DateTime.now();
                                                                  final expiredAt = createdAt.add(const Duration(minutes: 30));

                                                                  await PaymentCacheUtils.saveQRContent(
                                                                    bookingId: newBookingId,
                                                                    qrContent: qrisResponse.data!.qrContent,
                                                                    orderId: orderIdForDoku,
                                                                    userId: user.id.toString(),
                                                                    expiredAt: expiredAt,
                                                                  );

                                                                  // Show QRIS Result Dialog
                                                                  if (context.mounted) {
                                                                    await showDialog(
                                                                      context: context,
                                                                      barrierDismissible: false,
                                                                      builder: (ctx) => QRISResultDialog(
                                                                        qrisData: qrisResponse.data!,
                                                                        amount: amount,
                                                                        onClose: () {
                                                                          Navigator.of(ctx).pop();
                                                                          // Refresh mybooking list before navigation
                                                                          ref.invalidate(userBookingsProvider);
                                                                          context.go('/mybooking');
                                                                        },
                                                                      ),
                                                                    );
                                                                  }
                                                                } else {
                                                                  if (context.mounted) {
                                                                    showNotificationDialog(
                                                                      context,
                                                                      'QRIS generation failed: ${qrisResponse.message}',
                                                                      defaultIcon: Icons.error_outline,
                                                                      iconColor: Colors.red,
                                                                    );
                                                                    // Refresh mybooking list before navigation
                                                                    ref.invalidate(userBookingsProvider);
                                                                    context.go('/mybooking');
                                                                  }
                                                                }
                                                              },
                                                              error: (error, stackTrace) async {
                                                                if (context.mounted) {
                                                                  showNotificationDialog(
                                                                    context,
                                                                    'QRIS generation error: $error',
                                                                    defaultIcon: Icons.error_outline,
                                                                    iconColor: Colors.red,
                                                                  );
                                                                  // Refresh mybooking list before navigation
                                                                  ref.invalidate(userBookingsProvider);
                                                                  context.go('/mybooking');
                                                                }
                                                              },
                                                              loading: () {},
                                                            );
                                                          } else if (selectedTransactionType == 'CREDITCARD') {
                                                            // Generate CC
                                                            // Use _confirmedTotal — set when user confirmed, matches displayed price
                                                            // Backend grandTotal uses flat rate, not multi-tier
                                                            final amount = _confirmedTotal;

                                                            // Use order_id if available, fallback to booking_id
                                                            final orderIdForDoku = newOrderId ?? newBookingId;
                                                            AppLogger.i('Generating CC for renewal - booking_id: $newBookingId, order_id: $orderIdForDoku', 'PAYMENT-PAGE');

                                                            await ref.read(dokuCCNotifierProvider.notifier).generateCC(
                                                              orderId: orderIdForDoku,
                                                              userName: user.firstName,
                                                              userEmail: user.email,
                                                              userPhone: user.phoneNumber,
                                                              amount: amount,
                                                            );

                                                            // Check CC generation result
                                                            final ccState = ref.read(dokuCCNotifierProvider);
                                                            await ccState.generateResult.when(
                                                              data: (ccResponse) async {
                                                                if (ccResponse.isSuccess && ccResponse.data != null) {
                                                                  // Update payment method (renewal doesn't include deposit or voucher)
                                                                  final updateRequest = UpdatePaymentMethodRequest(
                                                                    paymentMethod: 'Credit Card',
                                                                    creditCardInvoice: ccResponse.data!.invoiceNumber,
                                                                    parkingFee: _parkingFee > 0 ? _parkingFee : null,
                                                                    parkingType: _selectedParkingType != null && _selectedParkingType!.isNotEmpty
                                                                        ? _selectedParkingType
                                                                        : null,
                                                                    parkingDuration: _parkingDuration > 0 ? _parkingDuration : null,
                                                                    vehiclePlate: _selectedParkingType != null && _vehiclePlateController.text.isNotEmpty
                                                                        ? _vehiclePlateController.text.trim()
                                                                        : null,
                                                                    ownerName: user.firstName,
                                                                    ownerPhone: user.phoneNumber,
                                                                  );

                                                                  await ref
                                                                      .read(paymentNotifierProvider.notifier)
                                                                      .updatePaymentMethod(newBookingId, updateRequest);

                                                                  // Save CC payment link to cache (30 minutes expiry)
                                                                  final createdAt = DateTime.now();
                                                                  final expiredAt = createdAt.add(const Duration(minutes: 30));

                                                                  await PaymentCacheUtils.saveCCLink(
                                                                    bookingId: newBookingId,
                                                                    paymentUrl: ccResponse.data!.paymentUrl,
                                                                    invoiceNumber: ccResponse.data!.invoiceNumber,
                                                                    userId: user.id.toString(),
                                                                    expiredAt: expiredAt,
                                                                  );

                                                                  // Launch payment URL
                                                                  final paymentUrl = ccResponse.data!.paymentUrl;
                                                                  if (paymentUrl.isNotEmpty) {
                                                                    final uri = Uri.parse(paymentUrl);
                                                                    if (await canLaunchUrl(uri)) {
                                                                      await launchUrl(uri, mode: LaunchMode.externalApplication);
                                                                      if (context.mounted) {
                                                                        // Refresh mybooking list before navigation
                                                                        ref.invalidate(userBookingsProvider);
                                                                        context.go('/mybooking');
                                                                      }
                                                                    }
                                                                  }
                                                                } else {
                                                                  if (context.mounted) {
                                                                    showNotificationDialog(
                                                                      context,
                                                                      'CC payment failed: ${ccResponse.message}',
                                                                      defaultIcon: Icons.error_outline,
                                                                      iconColor: Colors.red,
                                                                    );
                                                                    // Refresh mybooking list before navigation
                                                                    ref.invalidate(userBookingsProvider);
                                                                    context.go('/mybooking');
                                                                  }
                                                                }
                                                              },
                                                              error: (error, stackTrace) async {
                                                                if (context.mounted) {
                                                                  showNotificationDialog(
                                                                    context,
                                                                    'CC generation error: $error',
                                                                    defaultIcon: Icons.error_outline,
                                                                    iconColor: Colors.red,
                                                                  );
                                                                  // Refresh mybooking list before navigation
                                                                  ref.invalidate(userBookingsProvider);
                                                                  context.go('/mybooking');
                                                                }
                                                              },
                                                              loading: () {},
                                                            );
                                                          }
                                                        } else {
                                                          // Create regular booking with voucher code if applied
                                                          final isMonthly = getRentType(roomData).toLowerCase() == 'monthly';
                                                          ref.read(paymentNotifierProvider.notifier).postBooking(
                                                            transactionType: selectedTransactionType,
                                                            voucherCode: voucherNotifier.appliedVoucherCode,
                                                            depositFee: isMonthly ? _depositFee : 0,
                                                            parkingFee: isMonthly && _parkingFee > 0 ? _parkingFee : null,
                                                            parkingType: isMonthly && _selectedParkingType != null ? _selectedParkingType! : '',
                                                            parkingDuration: isMonthly && _parkingDuration > 0 ? _parkingDuration : null,
                                                          );
                                                        }
                                                      },
                                                    ),
                                                  );
                                                } : null,
                                                style: ElevatedButton.styleFrom(
                                                  backgroundColor: (isButtonEnabled && user != null) ? AppColors.primaryAdaptive(context) : Colors.grey,
                                                  foregroundColor: Colors.white,
                                                  padding: const EdgeInsets.symmetric(vertical: 20),
                                                  textStyle: textTheme.titleMedium?.copyWith(color: Colors.white),
                                                  shape: RoundedRectangleBorder(
                                                    borderRadius: BorderRadius.circular(10),
                                                  ),
                                                ),
                                                child: isProcessing
                                                    ? const SizedBox(
                                                        height: 24,
                                                        width: 24,
                                                        child: CircularProgressIndicator(
                                                          strokeWidth: 2.5,
                                                          valueColor: AlwaysStoppedAnimation<Color>(Colors.white),
                                                        ),
                                                      )
                                                    : Row(
                                                        mainAxisAlignment: MainAxisAlignment.center,
                                                        mainAxisSize: MainAxisSize.min,
                                                        children: [
                                                          Icon(Icons.shopping_cart, size: textTheme.headlineSmall?.fontSize ?? 24, color: Colors.white),
                                                          const SizedBox(width: 8),
                                                          Text(
                                                            localizations.paymentBookNowButton,
                                                            style: textTheme.titleMedium?.copyWith(
                                                              fontWeight: FontWeight.w600,
                                                              color: Colors.white,
                                                              letterSpacing: 0.5,
                                                            ),
                                                          ),
                                                        ],
                                                      ),
                                              );
                                            },
                                          ),
                                        ),
                                      ),

                                      // Add spacing below button
                                      const SizedBox(height: 20),
                              ],
                            ),
                          ),
              ),
            ),
          ),
        );
      },
    );
  }
}

/// Custom Payment Error Dialog Widget
///
/// Shows error notification with:
/// - Logo Ulin Mahoni at top
/// - Title/Header in center
/// - Error detail message
/// - Note at bottom
class _PaymentErrorDialog extends StatelessWidget {
  final String title;
  final String message;
  final String errorDetail;
  final String note;
  final VoidCallback onClose;

  const _PaymentErrorDialog({
    required this.title,
    required this.message,
    required this.errorDetail,
    required this.note,
    required this.onClose,
  });

  @override
  Widget build(BuildContext context) {
    // Dark mode detection for error dialog text and background colors
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Dialog(
      backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(20),
      ),
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            // Logo Ulin Mahoni at top
            Image.asset(
              AppImage.logo,
              width: 80,
              height: 80,
              fit: BoxFit.contain,
            ),
            const SizedBox(height: 20),

            // Title/Header
            Text(
              title,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: isDark ? Colors.white : Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),

            // Main message
            Text(
              message,
              style: TextStyle(
                fontSize: 15,
                fontWeight: FontWeight.w500,
                color: isDark ? Colors.white70 : Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 16),

            // Error detail box
            Container(
              width: double.infinity,
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: Colors.red.shade50,
                borderRadius: BorderRadius.circular(12),
                border: Border.all(color: Colors.red.shade200, width: 1.5),
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    children: [
                      Icon(Icons.error_outline, color: Colors.red.shade700, size: 18),
                      const SizedBox(width: 6),
                      const Text(
                        'Error:',
                        style: TextStyle(
                          fontWeight: FontWeight.bold,
                          fontSize: 13,
                          // Error box always has a light red bg so black is fine here
                          color: Colors.black87,
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 6),
                  Text(
                    errorDetail,
                    style: TextStyle(
                      color: Colors.red.shade900,
                      fontSize: 13,
                      height: 1.4,
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Note at bottom
            Container(
              padding: const EdgeInsets.all(12),
              decoration: BoxDecoration(
                color: isDark ? const Color(0xFF374151) : Colors.grey.shade100,
                borderRadius: BorderRadius.circular(12),
              ),
              child: Row(
                children: [
                  Icon(Icons.info_outline, color: Colors.blue.shade700, size: 18),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      note,
                      style: TextStyle(
                        fontSize: 12,
                        color: isDark ? Colors.grey.shade300 : Colors.grey.shade700,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20),

            // Close button
            SizedBox(
              width: double.infinity,
              child: ElevatedButton(
                onPressed: onClose,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primaryAdaptive(context),
                  foregroundColor: Colors.white,
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                  elevation: 0,
                ),
                child: const Text(
                  'Ke My Booking',
                  style: TextStyle(
                    fontSize: 16,
                    fontWeight: FontWeight.w600,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}