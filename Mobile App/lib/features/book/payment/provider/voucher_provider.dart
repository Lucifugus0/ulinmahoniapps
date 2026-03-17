import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/core/network/api_result.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';
import '../data/repositories/voucher_repository.dart';
import '../model/voucher_model.dart';

/// Provider for VoucherRepository
final voucherRepositoryProvider = Provider<VoucherRepository>((ref) {
  return VoucherRepository();
});

/// State class for voucher operations
class VoucherState {
  final AsyncValue<VoucherValidationResponse?> validationResult;
  final AsyncValue<VoucherApplicationResponse?> applicationResult;
  final String? appliedVoucherCode;
  final double? discountAmount;

  VoucherState({
    this.validationResult = const AsyncValue.data(null),
    this.applicationResult = const AsyncValue.data(null),
    this.appliedVoucherCode,
    this.discountAmount,
  });

  VoucherState copyWith({
    AsyncValue<VoucherValidationResponse?>? validationResult,
    AsyncValue<VoucherApplicationResponse?>? applicationResult,
    String? appliedVoucherCode,
    double? discountAmount,
    bool clearAppliedVoucher = false,
  }) {
    return VoucherState(
      validationResult: validationResult ?? this.validationResult,
      applicationResult: applicationResult ?? this.applicationResult,
      appliedVoucherCode: clearAppliedVoucher ? null : (appliedVoucherCode ?? this.appliedVoucherCode),
      discountAmount: clearAppliedVoucher ? null : (discountAmount ?? this.discountAmount),
    );
  }
}

/// StateNotifier for managing voucher state
class VoucherNotifier extends StateNotifier<VoucherState> {
  final VoucherRepository _repository;

  VoucherNotifier(this._repository) : super(VoucherState());

  /// Validate a voucher code
  Future<void> validateVoucher({
    required String voucherCode,
    required int userId,
    required double transactionAmount,
    required int propertyId,
    required int roomId,
  }) async {
    AppLogger.i('Validating voucher: $voucherCode', 'VOUCHER-PROVIDER');

    // Set loading state
    state = state.copyWith(
      validationResult: const AsyncValue.loading(),
    );

    final request = VoucherValidationRequest(
      voucherCode: voucherCode,
      userId: userId,
      transactionAmount: transactionAmount,
      propertyId: propertyId,
      roomId: roomId,
    );

    final result = await _repository.validateVoucher(request);

    switch (result) {
      case Success(:final data):
        AppLogger.s('Voucher validation successful', 'VOUCHER-PROVIDER');
        state = state.copyWith(
          validationResult: AsyncValue.data(data),
          appliedVoucherCode: voucherCode,
          discountAmount: data.calculation.discountAmount,
        );
        break;

      case Failure(:final message):
        AppLogger.w('Voucher validation failed: $message', 'VOUCHER-PROVIDER');
        state = state.copyWith(
          validationResult: AsyncValue.error(message, StackTrace.current),
          clearAppliedVoucher: true,
        );
        break;
    }
  }

  /// Apply a voucher to a booking
  Future<void> applyVoucher({
    required String voucherCode,
    required int userId,
    required double transactionAmount,
    required int propertyId,
    required int roomId,
  }) async {
    AppLogger.i('Applying voucher: $voucherCode (amount: $transactionAmount)', 'VOUCHER-PROVIDER');

    // Set loading state
    state = state.copyWith(
      applicationResult: const AsyncValue.loading(),
    );

    final request = VoucherApplicationRequest(
      voucherCode: voucherCode,
      userId: userId,
      transactionAmount: transactionAmount,
      propertyId: propertyId,
      roomId: roomId,
    );

    final result = await _repository.applyVoucher(request);

    switch (result) {
      case Success(:final data):
        AppLogger.s('Voucher application successful', 'VOUCHER-PROVIDER');
        state = state.copyWith(
          applicationResult: AsyncValue.data(data),
          appliedVoucherCode: voucherCode,
          discountAmount: data.discountApplied,
        );
        break;

      case Failure(:final message):
        AppLogger.w('Voucher application failed: $message', 'VOUCHER-PROVIDER');
        state = state.copyWith(
          applicationResult: AsyncValue.error(message, StackTrace.current),
        );
        break;
    }
  }

  /// Remove applied voucher
  void removeVoucher() {
    AppLogger.d('Removing applied voucher', 'VOUCHER-PROVIDER');
    state = state.copyWith(
      validationResult: const AsyncValue.data(null),
      applicationResult: const AsyncValue.data(null),
      clearAppliedVoucher: true,
    );
  }

  /// Reset voucher state
  void resetState() {
    AppLogger.d('Resetting voucher state', 'VOUCHER-PROVIDER');
    state = VoucherState();
  }

  /// Get current discount amount
  double get currentDiscount => state.discountAmount ?? 0.0;

  /// Check if voucher is applied
  bool get hasAppliedVoucher => state.appliedVoucherCode != null;

  /// Get applied voucher code
  String? get appliedVoucherCode => state.appliedVoucherCode;
}

/// Provider for VoucherNotifier
final voucherNotifierProvider =
    StateNotifierProvider<VoucherNotifier, VoucherState>((ref) {
  final repository = ref.watch(voucherRepositoryProvider);
  return VoucherNotifier(repository);
});
