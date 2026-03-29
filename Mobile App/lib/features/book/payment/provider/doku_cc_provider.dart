import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/network/api_result.dart';
import '../../../../../core/utils/app_logger.dart';
import '../model/doku_cc_model.dart';
import '../data/repositories/doku_cc_repository.dart';

/// State for DOKU CC payment generation — holds the async result of the CC generation request
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

/// Riverpod 3.x Notifier for managing DOKU CC state — migrated from StateNotifier
class DokuCCNotifier extends Notifier<DokuCCState> {
  /// Build method returns the initial state (replaces constructor super call)
  @override
  DokuCCState build() => DokuCCState();

  /// Generate DOKU Credit Card Payment via repository and update state accordingly
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

    final repository = dokuCCRepositoryProvider;
    final result = await repository.generateCC(request);

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

  /// Reset state back to initial values
  void resetState() {
    AppLogger.d('Resetting DOKU CC state', 'DOKU-CC-PROVIDER');
    state = DokuCCState();
  }
}

/// Provider for DokuCCNotifier — migrated from StateNotifierProvider to NotifierProvider
final dokuCCNotifierProvider =
    NotifierProvider<DokuCCNotifier, DokuCCState>(DokuCCNotifier.new);
