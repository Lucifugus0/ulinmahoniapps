import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../model/doku_va_model.dart';
import '../data/repositories/doku_va_repository.dart';

/// State for DOKU VA payment — holds the async result and selected bank
class DokuVAState {
  final AsyncValue<DokuVAResponse> generateResult;
  final String? selectedBank;

  DokuVAState({
    AsyncValue<DokuVAResponse>? generateResult,
    this.selectedBank,
  }) : generateResult = generateResult ?? const AsyncValue.loading();

  DokuVAState copyWith({
    AsyncValue<DokuVAResponse>? generateResult,
    String? selectedBank,
  }) {
    return DokuVAState(
      generateResult: generateResult ?? this.generateResult,
      selectedBank: selectedBank ?? this.selectedBank,
    );
  }
}

/// Riverpod 3.x Notifier for managing DOKU VA state — migrated from StateNotifier
class DokuVANotifier extends Notifier<DokuVAState> {
  /// Build method returns the initial state (replaces constructor super call)
  @override
  DokuVAState build() => DokuVAState();

  /// Select bank for VA generation
  void selectBank(String bank) {
    AppLogger.d('Selected bank: $bank', 'DOKU-VA-PROVIDER');
    state = state.copyWith(selectedBank: bank);
  }

  /// Clear selected bank
  void clearSelectedBank() {
    state = state.copyWith(selectedBank: null);
  }

  /// Generate DOKU Virtual Account via repository and update state accordingly
  Future<void> generateVA({
    required String orderId,
    required String userName,
    required String userEmail,
    required String userPhone,
    required double amount,
    required String bank,
  }) async {
    AppLogger.i(
      'Generating DOKU VA: order=$orderId, bank=$bank, amount=$amount',
      'DOKU-VA-PROVIDER',
    );

    // Set loading state
    state = state.copyWith(
      generateResult: const AsyncValue.loading(),
    );

    final request = DokuVARequest(
      orderId: orderId,
      userName: userName,
      userEmail: userEmail,
      userPhone: userPhone,
      amount: amount,
      bank: bank,
    );

    final repository = dokuVARepositoryProvider;
    final result = await repository.generateVA(request);

    switch (result) {
      case Success(:final data):
        AppLogger.s('DOKU VA generated successfully', 'DOKU-VA-PROVIDER');
        state = state.copyWith(
          generateResult: AsyncValue.data(data),
        );
        break;

      case Failure(:final message):
        AppLogger.e(
          'Failed to generate DOKU VA: $message',
          null,
          null,
          'DOKU-VA-PROVIDER',
        );
        state = state.copyWith(
          generateResult: AsyncValue.error(message, StackTrace.current),
        );
        break;
    }
  }

  /// Reset state back to initial values
  void resetState() {
    AppLogger.d('Resetting DOKU VA state', 'DOKU-VA-PROVIDER');
    state = DokuVAState();
  }

  /// Convenience getters for checking bank selection status
  bool get hasSelectedBank => state.selectedBank != null && state.selectedBank!.isNotEmpty;
  String? get selectedBank => state.selectedBank;
}

/// Provider for DokuVANotifier — migrated from StateNotifierProvider to NotifierProvider
final dokuVANotifierProvider =
    NotifierProvider<DokuVANotifier, DokuVAState>(DokuVANotifier.new);
