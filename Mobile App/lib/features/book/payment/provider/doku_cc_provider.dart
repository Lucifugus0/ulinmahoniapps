import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../model/doku_cc_model.dart';
import '../data/repositories/doku_cc_repository.dart';

/// State for DOKU CC
class DokuCCState {
  final AsyncValue<DokuCCResponse> generateResult;

  DokuCCState({
    AsyncValue<DokuCCResponse>? generateResult,
  }) : generateResult = generateResult ?? const AsyncValue.loading();

  DokuCCState copyWith({
    AsyncValue<DokuCCResponse>? generateResult,
  }) {
    return DokuCCState(
      generateResult: generateResult ?? this.generateResult,
    );
  }
}

/// StateNotifier for managing DOKU CC state
class DokuCCNotifier extends StateNotifier<DokuCCState> {
  final DokuCCRepository _repository;

  DokuCCNotifier(this._repository) : super(DokuCCState());

  /// Generate DOKU Credit Card Payment
  Future<void> generateCC({
    required String orderId,
    required String userName,
    required String userEmail,
    required String userPhone,
    required double amount,
  }) async {
    AppLogger.i(
      'Generating DOKU CC: order=$orderId, amount=$amount',
      'DOKU-CC-PROVIDER',
    );

    // Set loading state
    state = state.copyWith(
      generateResult: const AsyncValue.loading(),
    );

    final request = DokuCCRequest(
      orderId: orderId,
      userName: userName,
      userEmail: userEmail,
      userPhone: userPhone,
      amount: amount,
    );

    final result = await _repository.generateCC(request);

    switch (result) {
      case Success(:final data):
        AppLogger.s('DOKU CC generated successfully', 'DOKU-CC-PROVIDER');
        state = state.copyWith(
          generateResult: AsyncValue.data(data),
        );
        break;

      case Failure(:final message):
        AppLogger.e(
          'Failed to generate DOKU CC: $message',
          null,
          null,
          'DOKU-CC-PROVIDER',
        );
        state = state.copyWith(
          generateResult: AsyncValue.error(message, StackTrace.current),
        );
        break;
    }
  }

  /// Reset state
  void resetState() {
    AppLogger.d('Resetting DOKU CC state', 'DOKU-CC-PROVIDER');
    state = DokuCCState();
  }
}

/// Provider for DokuCCNotifier
final dokuCCNotifierProvider =
    StateNotifierProvider<DokuCCNotifier, DokuCCState>((ref) {
  final repository = dokuCCRepositoryProvider;
  return DokuCCNotifier(repository);
});
