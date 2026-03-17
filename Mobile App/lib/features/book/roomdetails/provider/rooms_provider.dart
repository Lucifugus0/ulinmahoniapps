import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../model/rooms_model.dart';
import '../data/repositories/rooms_repository.dart';
import '../../../../core/network/api_result.dart';
import '../../../../core/utils/app_logger.dart';

final roomsRepositoryProvider = Provider<RoomsRepository>((ref) {
  return RoomsRepository();
});


final roomListProvider = FutureProvider.family<List<RoomModel>, int>((ref, propertyId) async {
  AppLogger.d('🔵 PROVIDER: Fetching rooms for property ID: $propertyId', 'ROOMS-PROVIDER');
  final repository = ref.read(roomsRepositoryProvider);

  final result = await repository.getRoomsByPropertyId(propertyId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});

final roomByIdProvider = FutureProvider.family<RoomModel, int>((ref, roomId) async {
  AppLogger.d('🔵 PROVIDER: Fetching room by ID: $roomId', 'ROOMS-PROVIDER');
  final repository = ref.read(roomsRepositoryProvider);

  final result = await repository.getRoomById(roomId);

  return switch (result) {
    Success(:final data) => data,
    Failure(:final message) => throw Exception(message),
  };
});
