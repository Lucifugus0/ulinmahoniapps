import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../model/doku_qris_model.dart';
import '../data/repositories/doku_qris_repository.dart';

/// State for DOKU QRIS
class DokuQRISState {
  final AsyncValue<DokuQRISResponse> generateResult;

  DokuQRISState({
    AsyncValue<DokuQRISResponse>? generateResult,
  }) : generateResult = generateResult ?? const AsyncValue.loading();

  DokuQRISState copyWith({
    AsyncValue<DokuQRISResponse>? generateResult,
  }) {
    return DokuQRISState(
      generateResult: generateResult ?? this.generateResult,
    );
  }
}

/// StateNotifier for managing DOKU QRIS state
class DokuQRISNotifier extends StateNotifier<DokuQRISState> {
  final DokuQRISRepository _repository;

  DokuQRISNotifier(this._repository) : super(DokuQRISState());

  /// Generate DOKU QRIS
  Future<void> generateQRIS({
    required String orderId,
    required String userName,
    required String userEmail,
    required String userPhone,
    required double amount,
  }) async {
    AppLogger.i(
      'Generating DOKU QRIS: order=$orderId, amount=$amount',
      'DOKU-QRIS-PROVIDER',
    );

    // Set loading state
    state = state.copyWith(
      generateResult: const AsyncValue.loading(),
    );

    final request = DokuQRISRequest(
      orderId: orderId,
      userName: userName,
      userEmail: userEmail,
      userPhone: userPhone,
      amount: amount,
    );

    final result = await _repository.generateQRIS(request);

    switch (result) {
      case Success(:final data):
        AppLogger.s('DOKU QRIS generated successfully', 'DOKU-QRIS-PROVIDER');
        state = state.copyWith(
          generateResult: AsyncValue.data(data),
        );
        break;

      case Failure(:final message):
        AppLogger.e(
          'Failed to generate DOKU QRIS: $message',
          null,
          null,
          'DOKU-QRIS-PROVIDER',
        );
        state = state.copyWith(
          generateResult: AsyncValue.error(message, StackTrace.current),
        );
        break;
    }
  }

  /// Reset state
  void resetState() {
    AppLogger.d('Resetting DOKU QRIS state', 'DOKU-QRIS-PROVIDER');
    state = DokuQRISState();
  }
}

/// Provider for DokuQRISNotifier
final dokuQRISNotifierProvider =
    StateNotifierProvider<DokuQRISNotifier, DokuQRISState>((ref) {
  final repository = dokuQRISRepositoryProvider;
  return DokuQRISNotifier(repository);
});
