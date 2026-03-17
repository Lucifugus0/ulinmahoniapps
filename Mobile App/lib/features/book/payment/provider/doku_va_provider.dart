import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../model/doku_va_model.dart';
import '../data/repositories/doku_va_repository.dart';

/// State for DOKU VA
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

/// StateNotifier for managing DOKU VA state
class DokuVANotifier extends StateNotifier<DokuVAState> {
  final DokuVARepository _repository;

  DokuVANotifier(this._repository) : super(DokuVAState());

  /// Select bank for VA generation
  void selectBank(String bank) {
    AppLogger.d('Selected bank: $bank', 'DOKU-VA-PROVIDER');
    state = state.copyWith(selectedBank: bank);
  }

  /// Clear selected bank
  void clearSelectedBank() {
    state = state.copyWith(selectedBank: null);
  }

  /// Generate DOKU Virtual Account
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

    final result = await _repository.generateVA(request);

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

  /// Reset state
  void resetState() {
    AppLogger.d('Resetting DOKU VA state', 'DOKU-VA-PROVIDER');
    state = DokuVAState();
  }

  /// Convenience getters
  bool get hasSelectedBank => state.selectedBank != null && state.selectedBank!.isNotEmpty;
  String? get selectedBank => state.selectedBank;
}

/// Provider for DokuVANotifier
final dokuVANotifierProvider =
    StateNotifierProvider<DokuVANotifier, DokuVAState>((ref) {
  final repository = dokuVARepositoryProvider;
  return DokuVANotifier(repository);
});
