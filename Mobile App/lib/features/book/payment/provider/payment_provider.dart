import 'dart:convert';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/features/book/detailproperty/model/detailproperty_model.dart';
import 'package:ulinmahoniapps/features/book/roomdetails/model/rooms_model.dart';
import 'package:ulinmahoniapps/features/book/payment/model/payment_model.dart';
import 'package:ulinmahoniapps/features/book/payment/model/update_payment_method_model.dart';
import 'package:ulinmahoniapps/features/auth/login/provider/auth_provider.dart';
import 'package:ulinmahoniapps/features/book/payment/data/repositories/payment_repository.dart';
import 'package:ulinmahoniapps/core/network/api_result.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:intl/intl.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';

/// Parse dynamic value to double — strips currency symbols and whitespace
double parseToDouble(dynamic value) {
  if (value == null) return 0.0;
  String stringValue = value.toString().replaceAll('Rp', '').replaceAll(' ', '');
  return double.tryParse(stringValue) ?? 0.0;
}

/// Parse dynamic value to int — strips currency symbols and whitespace
int parseToInt(dynamic value) {
  if (value == null) return 0;
  String stringValue = value.toString().replaceAll('Rp', '').replaceAll(' ', '');
  return double.tryParse(stringValue)?.toInt() ?? 0;
}

/// State holding payment calculation data and booking post result
class PaymentState {
  final AsyncValue<Map<String, dynamic>> paymentCalculationData;
  final AsyncValue<Map<String, dynamic>?> postBookingResult;

  PaymentState({
    required this.paymentCalculationData,
    this.postBookingResult = const AsyncValue.data(null),
  });

  factory PaymentState.initial() {
    return PaymentState(
      paymentCalculationData: const AsyncValue.data({}),
      postBookingResult: const AsyncValue.data(null),
    );
  }

  PaymentState copyWith({
    AsyncValue<Map<String, dynamic>>? paymentCalculationData,
    AsyncValue<Map<String, dynamic>?>? postBookingResult,
  }) {
    return PaymentState(
      paymentCalculationData: paymentCalculationData ?? this.paymentCalculationData,
      postBookingResult: postBookingResult ?? this.postBookingResult,
    );
  }
}

/// Provider for PaymentRepository instance
final paymentRepositoryProvider = Provider<PaymentRepository>((ref) {
  return PaymentRepository();
});

/// Provider for PaymentNotifier — migrated from StateNotifierProvider to NotifierProvider
final paymentNotifierProvider = NotifierProvider<PaymentNotifier, PaymentState>(PaymentNotifier.new);

/// Riverpod 3.x Notifier for managing payment state — migrated from StateNotifier
class PaymentNotifier extends Notifier<PaymentState> {
  /// Build method returns the initial state with loading payment data
  @override
  PaymentState build() => PaymentState(paymentCalculationData: const AsyncValue.loading());

  /// Load and calculate payment details for a new booking based on room, property, rent type, and duration
  Future<void> loadPaymentDetails({
    required RoomModel room,
    required DetailPropertyModel propertyData,
    required String rentType,
    required int duration,
    required DateTime checkInDate,
    required DateTime checkOutDate,
    required AppLocalizations localizations,
    // Multi-tier total from price-preview API (weekday/weekend/holiday rates)
    double? multiTierTotalPrice,
  }) async {
    state = state.copyWith(paymentCalculationData: const AsyncValue.loading());
    try {
      AppLogger.d("Fetching Payment Data in Notifier (Combined)", "PAYMENT");

      final originalDailyPrice = parseToDouble(room.priceOriginalDaily);
      final originalMonthlyPrice = parseToDouble(room.priceOriginalMonthly);
      final adminFees = parseToDouble(room.adminfee);

      double originalTotal = 0;

      int monthlyDurationCalculated = 0;
      int dailyDurationCalculated = 0;
      double? finalDailyPrice = 0.0;
      double? finalMonthlyPrice = 0.0;
      int? finalBookingDays = 0;
      int? finalBookingMonths = 0;
      double taxAmount = 30000;       // Nilai Tax yang dihitung

      if (rentType == 'daily' || rentType == 'Daily') {
        // Multi-Tier Pricing: use per-date sum from price-preview API if available
        // The server calculates the correct total based on weekday/weekend/holiday/season rules
        // Fallback to flat rate if API fails or room has no per-date prices
        originalTotal = multiTierTotalPrice ?? (originalDailyPrice * duration);
        dailyDurationCalculated = duration;
        finalBookingDays = duration;
        finalDailyPrice = originalDailyPrice;
        finalBookingMonths = null;
        finalMonthlyPrice = null;
      } else if (rentType == 'monthly' || rentType == 'Monthly') {
        originalTotal = originalMonthlyPrice * duration;
        monthlyDurationCalculated = duration;
        finalBookingMonths = duration;
        finalMonthlyPrice = originalMonthlyPrice;
        finalBookingDays = null;
        finalDailyPrice = null;
      } else if (rentType == 'annual' || rentType == 'Annual') {
        // Multi-Tier Pricing: annual booking — flat rate × years
        final originalAnnualPrice = parseToDouble(room.priceOriginalAnnual);
        originalTotal = originalAnnualPrice * duration;
        finalBookingDays = null;
        finalDailyPrice = null;
        finalBookingMonths = null;
        finalMonthlyPrice = null;
      }

      final grandTotal = originalTotal + taxAmount;

      final roomDetails = {
        'name': room.name ?? '-',
        'id': room.id ?? '',
        'checkIn': checkInDate.toString().substring(0, 10),
        'checkOut': checkOutDate.toString().substring(0, 10),
        'type': room.type ?? '-',
        'propertyName': propertyData.name ?? '-',
        'propertyId': propertyData.id ?? '',
        'propertyType': propertyData.tags ?? '-',
        'location': propertyData.location ?? '-',
        'daily_price': finalDailyPrice,
        'monthly_price': finalMonthlyPrice,
        'rentType': rentType,
        'booking_months': finalBookingMonths,
        'booking_days': finalBookingDays,
        'service_fees': taxAmount,
      };


      final currencyFormatter = NumberFormat.currency(
        locale: localizations.localeName,
        symbol: 'Rp',
        decimalDigits: 0,
      );

      final List<Map<String, dynamic>> itemDetails = [
        {
          'name': '$duration ${rentType == 'Daily' || rentType == 'daily' ? localizations.dailyDurationUnit : localizations.monthlyDurationUnit} ${localizations.normalPriceLabel}',
          'price': currencyFormatter.format(originalTotal),
          'rawPrice': originalTotal,
          'type': 'original',
        },
      ];

      if (taxAmount > 0) {
        itemDetails.add({
          'name': localizations.taxlabel ?? 'Tax (20%)',
          'price': currencyFormatter.format(taxAmount),
          'rawPrice': taxAmount,
          'type': 'tax',
        });
      }

      final Map<String, dynamic> combinedData = {
        'roomData': roomDetails,
        'itemDetails': itemDetails,
        'afterOriginalTotalFees': taxAmount, // Nilai Tax
        'grandTotal': grandTotal, // Nilai Total (120%)
      };

      state = state.copyWith(paymentCalculationData: AsyncValue.data(combinedData));
      AppLogger.d("Payment data calculated: $combinedData", "PAYMENT");
    } catch (e, st) {
      AppLogger.e("Error loading payment details", e, st, "PAYMENT");
      state = state.copyWith(paymentCalculationData: AsyncValue.error(e, st));
    }
  }

  /// Load and calculate payment details for a booking renewal
  Future<void> loadRenewalPaymentDetails({
    required int roomId,
    required int propertyId,
    required String roomName,
    required String propertyName,
    required double dailyPrice,
    required double monthlyPrice,
    required String rentType,
    required int duration,
    required DateTime checkInDate,
    required DateTime checkOutDate,
    required AppLocalizations localizations,
    // Multi-tier total from price-preview API (weekday/weekend/holiday rates)
    double? multiTierTotalPrice,
  }) async {
    state = state.copyWith(paymentCalculationData: const AsyncValue.loading());
    try {
      AppLogger.d("Loading Renewal Payment Data", "PAYMENT-RENEWAL");

      double originalTotal = 0;
      int? finalBookingDays;
      int? finalBookingMonths;
      double taxAmount = 30000;

      if (rentType == 'daily' || rentType == 'Daily') {
        // Use multi-tier total if available, fallback to flat rate
        originalTotal = multiTierTotalPrice ?? (dailyPrice * duration);
        finalBookingDays = duration;
        finalBookingMonths = null;
      } else if (rentType == 'monthly' || rentType == 'Monthly') {
        originalTotal = monthlyPrice * duration;
        finalBookingMonths = duration;
        finalBookingDays = null;
      }

      final grandTotal = originalTotal + taxAmount;

      final roomDetails = {
        'name': roomName,
        'id': roomId,
        'checkIn': checkInDate.toString().substring(0, 10),
        'checkOut': checkOutDate.toString().substring(0, 10),
        'type': 'renewal',
        'propertyName': propertyName,
        'propertyId': propertyId,
        'propertyType': '-',
        'location': '-',
        'daily_price': dailyPrice,
        'monthly_price': monthlyPrice,
        'rentType': rentType,
        'booking_months': finalBookingMonths,
        'booking_days': finalBookingDays,
        'service_fees': taxAmount,
      };

      final currencyFormatter = NumberFormat.currency(
        locale: localizations.localeName,
        symbol: 'Rp',
        decimalDigits: 0,
      );

      final List<Map<String, dynamic>> itemDetails = [
        {
          'name': '$duration ${rentType == 'Daily' || rentType == 'daily' ? localizations.dailyDurationUnit : localizations.monthlyDurationUnit} ${localizations.normalPriceLabel}',
          'price': currencyFormatter.format(originalTotal),
          'rawPrice': originalTotal,
          'type': 'original',
        },
      ];

      if (taxAmount > 0) {
        itemDetails.add({
          'name': localizations.taxlabel ?? 'Tax',
          'price': currencyFormatter.format(taxAmount),
          'rawPrice': taxAmount,
          'type': 'tax',
        });
      }

      final Map<String, dynamic> combinedData = {
        'roomData': roomDetails,
        'itemDetails': itemDetails,
        'afterOriginalTotalFees': taxAmount,
        'grandTotal': grandTotal,
      };

      state = state.copyWith(paymentCalculationData: AsyncValue.data(combinedData));
      AppLogger.d("Renewal payment data calculated: $combinedData", "PAYMENT-RENEWAL");
    } catch (e, st) {
      AppLogger.e("Error loading renewal payment details", e, st, "PAYMENT-RENEWAL");
      state = state.copyWith(paymentCalculationData: AsyncValue.error(e, st));
    }
  }


  /// Calculate total price from item list plus tax fee
  double calculateTotalPrice(List<Map<String, dynamic>> items, double taxFee) {
    double totalPrice = 0;
    for (var item in items) {
      totalPrice += parseToDouble(item['rawPrice']);
    }
    return totalPrice;
  }

  /// Update payment method for a booking — delegates to repository
  Future<ApiResult<UpdatePaymentMethodResponse>> updatePaymentMethod(
    String bookingId,
    UpdatePaymentMethodRequest request,
  ) async {
    final repository = ref.read(paymentRepositoryProvider);
    return await repository.updatePaymentMethod(bookingId, request);
  }

  /// Post a new booking to the server with all payment and room details
  Future<void> postBooking({
    required String transactionType,
    String? voucherCode,
    double? depositFee,
    double? parkingFee,
    String? parkingType,
    int? parkingDuration,
  }) async {
    state = state.copyWith(postBookingResult: const AsyncValue.loading());

    final user = ref.read(authProvider).user.value;
    if (user == null) {
      state = state.copyWith(postBookingResult: AsyncValue.error('User not logged in', StackTrace.current));
      return;
    }

    final currentPaymentData = state.paymentCalculationData.value;
    if (currentPaymentData == null) {
      state = state.copyWith(postBookingResult: AsyncValue.error('Payment calculation data not loaded', StackTrace.current));
      return;
    }
    AppLogger.i("Transaction type: $transactionType", "PAYMENT");
    if (voucherCode != null && voucherCode.isNotEmpty) {
      AppLogger.i("Voucher code: $voucherCode", "PAYMENT");
    }

    final roomDetails = currentPaymentData['roomData'] as Map<String, dynamic>;

    AppLogger.d('User Data - userId: ${user.id}, userName: ${user.username}, userEmail: ${user.email}', "PAYMENT");
    AppLogger.d('Room Details: $roomDetails', "PAYMENT");

    final bookingRequest = BookingRequest(
      userId: user.id,
      userName: user.username,
      userPhoneNumber: user.phoneNumber,
      propertyId: roomDetails['propertyId']?.toString() ?? '',
      propertyName: roomDetails['propertyName']?.toString() ?? '',
      checkIn: roomDetails['checkIn']?.toString() ?? '',
      checkOut: roomDetails['checkOut']?.toString() ?? '',
      roomName: roomDetails['name']?.toString() ?? '',
      roomId: roomDetails['id']?.toString() ?? '',
      userEmail: user.email,
      dailyPrice: roomDetails['daily_price'],
      monthlyPrice: roomDetails['monthly_price'],
      serviceFee: 30000.0,
      propertyType: roomDetails['propertyType']?.toString() ?? '',
      bookingType: roomDetails['rentType']?.toString() ?? '',
      bookingDays: roomDetails['booking_days'],
      bookingMonths: roomDetails['booking_months'],
      transactionType: transactionType.toString(),
      voucherCode: voucherCode,
      depositFee: depositFee,
      parkingFee: parkingFee,
      parkingType: parkingType,
      parkingDuration: parkingDuration,
      isRenewal: 0, // Regular booking (not renewal)
    );

    AppLogger.separator("Data Booking yang Akan Dikirim");
    AppLogger.d(jsonEncode(bookingRequest.toJson()), "PAYMENT");
    AppLogger.separator();

    // Use Repository with ApiResult pattern
    final repository = ref.read(paymentRepositoryProvider);
    final result = await repository.createBooking(bookingRequest);

    // Handle result with pattern matching
    switch (result) {
      case Success(:final data):
        AppLogger.s('Booking successful - idrec: ${data.idrec}', 'PAYMENT');
        state = state.copyWith(
          postBookingResult: AsyncValue.data({
            'status': 'success',
            'message': data.message ?? 'Booking berhasil dibuat',
            'data': data.data,
            'idrec': data.idrec,
          }),
        );

      case Failure(:final errorType, :final message, :final statusCode):
        AppLogger.w('Booking failed - $errorType: $message (Status: $statusCode)', 'PAYMENT');
        state = state.copyWith(
          postBookingResult: AsyncValue.data({
            'status': 'error',
            'message': message,
            'errorType': errorType.name,
            'statusCode': statusCode,
          }),
        );
    }
  }
}
