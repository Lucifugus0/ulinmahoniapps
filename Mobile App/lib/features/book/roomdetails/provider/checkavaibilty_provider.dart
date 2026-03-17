import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/checkavailability_repository.dart';
import '../model/checkavaibility_model.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';

final checkAvailabilityRepositoryProvider = Provider<CheckAvailabilityRepository>((ref) {
  return CheckAvailabilityRepository();
});


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


class AvailabilityCheckNotifier extends StateNotifier<AvailabilityCheckState> {
  AvailabilityCheckNotifier(this.ref) : super(AvailabilityCheckState());

  final Ref ref;

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

  
  void resetAvailabilityState() {
    state = AvailabilityCheckState(); 
  }
}


final availabilityCheckProvider = StateNotifierProvider<AvailabilityCheckNotifier, AvailabilityCheckState>((ref) {
  return AvailabilityCheckNotifier(ref);
});