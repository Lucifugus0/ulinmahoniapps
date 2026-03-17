import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/mybookingdetails_repository.dart';
import '../model/roomimages_model.dart';
import '../../mybooking/model/mybooking_model.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/network/api_result.dart';

final myBookingDetailsRepositoryProvider =
    Provider<MyBookingDetailsRepository>((ref) {
  return MyBookingDetailsRepository();
});

class RoomImageNotifier extends FamilyAsyncNotifier<RoomImageModel?, int> {
  @override
  Future<RoomImageModel?> build(int roomId) async {
    final repository = ref.watch(myBookingDetailsRepositoryProvider);
    final result = await repository.fetchRoomImageModelByRoomId(roomId);

    switch (result) {
      case Success(:final data):
        return data;
      case Failure(:final message):
        AppLogger.w(
            'Error fetching room image for room $roomId: $message',
            'MYBOOKING-DETAILS');
        return null;
    }
  }

  Future<void> refreshRoomImage() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() => build(arg));
  }
}

final roomImageProvider =
AsyncNotifierProvider.family<RoomImageNotifier, RoomImageModel?, int>(
  RoomImageNotifier.new,
);


class MyBookingByIdNotifier extends FamilyAsyncNotifier<MyBookingModel?, int> {
  @override
  Future<MyBookingModel?> build(int idrec) async {
    final repository = ref.watch(myBookingDetailsRepositoryProvider);
    final result = await repository.fetchBookingById(idrec);

    switch (result) {
      case Success(:final data):
        return data;
      case Failure(:final message):
        AppLogger.e('Error fetching booking for idrec $idrec: $message',
            null, null, 'MYBOOKING-DETAILS');
        return null;
    }
  }

  Future<void> refreshBooking() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() => build(arg));
  }
}

final myBookingByIdProvider =
AsyncNotifierProvider.family<MyBookingByIdNotifier, MyBookingModel?, int>(
  MyBookingByIdNotifier.new,
);