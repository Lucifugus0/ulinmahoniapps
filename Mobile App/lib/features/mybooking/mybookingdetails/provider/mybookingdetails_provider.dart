import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../data/repositories/mybookingdetails_repository.dart';
import '../model/roomimages_model.dart';
import '../../mybooking/model/mybooking_model.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../core/network/api_result.dart';

/// Provider for MyBookingDetailsRepository singleton.
final myBookingDetailsRepositoryProvider =
    Provider<MyBookingDetailsRepository>((ref) {
  return MyBookingDetailsRepository();
});

/// AsyncNotifier for fetching room images by room ID.
/// Uses family pattern — the room ID is passed via constructor.
class RoomImageNotifier extends AsyncNotifier<RoomImageModel?> {
  final int roomId;

  RoomImageNotifier(this.roomId);

  /// build() fetches the room image model for the given room ID.
  @override
  Future<RoomImageModel?> build() async {
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

  /// Refresh the room image data.
  Future<void> refreshRoomImage() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() => build());
  }
}

/// Provider for room images — family provider parameterized by room ID.
/// Migrated from FamilyAsyncNotifier to AsyncNotifierProvider.family for Riverpod 3.x.
final roomImageProvider =
    AsyncNotifierProvider.family<RoomImageNotifier, RoomImageModel?, int>(
  (roomId) => RoomImageNotifier(roomId),
);

/// AsyncNotifier for fetching a single booking by its idrec.
/// Uses family pattern — the booking idrec is passed via constructor.
class MyBookingByIdNotifier extends AsyncNotifier<MyBookingModel?> {
  final int idrec;

  MyBookingByIdNotifier(this.idrec);

  /// build() fetches the booking model for the given idrec.
  @override
  Future<MyBookingModel?> build() async {
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

  /// Refresh the booking data.
  Future<void> refreshBooking() async {
    state = const AsyncLoading();
    state = await AsyncValue.guard(() => build());
  }
}

/// Provider for booking by ID — family provider parameterized by booking idrec.
/// Migrated from FamilyAsyncNotifier to AsyncNotifierProvider.family for Riverpod 3.x.
final myBookingByIdProvider =
    AsyncNotifierProvider.family<MyBookingByIdNotifier, MyBookingModel?, int>(
  (idrec) => MyBookingByIdNotifier(idrec),
);
