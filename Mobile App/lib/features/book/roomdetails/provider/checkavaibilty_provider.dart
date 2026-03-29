import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/checkavailability_repository.dart';
import '../model/checkavaibility_model.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';

/// Provider for CheckAvailabilityRepository instance
final checkAvailabilityRepositoryProvider = Provider<CheckAvailabilityRepository>((ref) {
  return CheckAvailabilityRepository();
});

/// State holding the room availability check result as an AsyncValue
class AvailabilityCheckState {
  final AsyncValue<bool?> isRoomAvailable;

  AvailabilityCheckState({
    this.isRoomAvailable = const AsyncValue.data(null),
  });

  AvailabilityCheckState copyWith({
    AsyncValue<bool?>? isRoomAvailable,
  }) {
    return AvailabilityCheckState(
      isRoomAvailable: isRoomAvailable ?? this.isRoomAvailable,
    );
  }
}

/// Riverpod 3.x Notifier for room availability checks — migrated from StateNotifier
class AvailabilityCheckNotifier extends Notifier<AvailabilityCheckState> {
  /// Build method returns the initial state (replaces constructor super call)
  @override
  AvailabilityCheckState build() => AvailabilityCheckState();

  /// Check room availability via repository for given property, room, and date range
  Future<void> checkRoomAvailability({
    required int propertyId,
    required int roomId,
    required String checkInDate,
    required String checkOutDate,
    bool isRenewal = false,
  }) async {
    state = state.copyWith(isRoomAvailable: const AsyncValue.loading());

    final repository = ref.read(checkAvailabilityRepositoryProvider);

    final result = await repository.checkRoomAvailability(
      propertyId: propertyId,
      roomId: roomId,
      checkInDate: checkInDate,
      checkOutDate: checkOutDate,
      isRenewal: isRenewal,
    );

    switch (result) {
      case Success(:final data):
        AppLogger.s('Room availability check successful. Available: ${data.data.isAvailable}', 'AVAILABILITY-CHECK');
        state = state.copyWith(isRoomAvailable: AsyncValue.data(data.data.isAvailable));

      case Failure(:final errorType, :final message):
        AppLogger.w('Room availability check failed - $errorType: $message', 'AVAILABILITY-CHECK');
        state = state.copyWith(isRoomAvailable: AsyncValue.error(Exception(message), StackTrace.current));
    }
  }

  /// Reset availability state back to initial (no check performed)
  void resetAvailabilityState() {
    state = AvailabilityCheckState();
  }
}

/// Provider for AvailabilityCheckNotifier — migrated from StateNotifierProvider to NotifierProvider
final availabilityCheckProvider = NotifierProvider<AvailabilityCheckNotifier, AvailabilityCheckState>(AvailabilityCheckNotifier.new);
