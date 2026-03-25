import 'package:flutter/foundation.dart';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../roomdetails/model/rooms_model.dart';
import '../../../../roomdetails/provider/rooms_provider.dart';
import '../../../model/detailproperty_model.dart';
import '../../../../../../core/utils/formatcurrency.dart';
import '../../../../../../core/widgets/card/roomcard.dart';
import '../../../../../../core/widgets/dialog/error_widgets.dart';
import '../../../../../../core/widgets/comingsoon_widgets.dart';
import 'package:ulinmahoniapps/router/route_constants.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import '../../../../../../core/utils/app_logger.dart';

// StateProvider untuk selected room name filter
final selectedRoomFilterProvider = StateProvider.family<String?, int>((ref, propertyId) => null);

// StateProvider untuk selected room status filter (rental_status)
// null = all, '0' = available (Tersedia), '1' = occupied (Terisi)
final selectedRoomStatusFilterProvider = StateProvider.family<String?, int>((ref, propertyId) => null);

class RoomTypeSection extends ConsumerWidget {
  final DetailPropertyModel propertyData;
  const RoomTypeSection({Key? key, required this.propertyData}) : super(key: key);

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;
    final int propertyId = propertyData.id ?? 0;
    final roomsAsyncValue = ref.watch(roomListProvider(propertyId));
    final textTheme = Theme.of(context).textTheme;
    final selectedFilter = ref.watch(selectedRoomFilterProvider(propertyId));
    final selectedStatusFilter = ref.watch(selectedRoomStatusFilterProvider(propertyId));
    // Dark mode: slightly lighter than page bg so the section is still distinct
    final isDark = Theme.of(context).brightness == Brightness.dark;

    AppLogger.d('RoomTypeSection building for propertyId: $propertyId, state: ${roomsAsyncValue.isLoading ? "loading" : roomsAsyncValue.hasError ? "error" : "data"}', 'ROOMS');

    return Container(
      width: double.infinity,
      color: isDark ? const Color(0xFF1F2937) : AppColors.secondaryBackgroundColor,
      padding: const EdgeInsets.only(top: 16, bottom: 16),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  crossAxisAlignment: CrossAxisAlignment.center,
                  children: [
                    Expanded(
                      child: Text(
                        localizations.roomTypeAvailableRooms,
                        style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                      ),
                    ),
                  ],
                ),
                const SizedBox(height: 12),
                // Filter Row with both dropdowns
                roomsAsyncValue.maybeWhen(
                  data: (rooms) {
                    final availableRooms = rooms.where((room) => room.status != 0 && room.status != 2).toList();
                    // Get distinct room names
                    final distinctRoomNames = availableRooms
                        .map((room) => room.name)
                        .where((name) => name != null && name.isNotEmpty)
                        .cast<String>()
                        .toSet()
                        .toList()
                      ..sort();

                    if (distinctRoomNames.isEmpty) {
                      return const SizedBox.shrink();
                    }

                    return Row(
                      children: [
                        // Status Filter Dropdown
                        Expanded(
                          child: _RoomStatusFilterDropdown(
                            selectedStatusFilter: selectedStatusFilter,
                            onStatusFilterChanged: (value) {
                              ref.read(selectedRoomStatusFilterProvider(propertyId).notifier).state = value;
                            },
                            localizations: localizations,
                            isEnabled: true,
                          ),
                        ),
                        const SizedBox(width: 8),
                        // Room Name Filter Dropdown
                        Expanded(
                          child: _RoomFilterDropdown(
                            roomNames: distinctRoomNames,
                            selectedFilter: selectedFilter,
                            onFilterChanged: (value) {
                              ref.read(selectedRoomFilterProvider(propertyId).notifier).state = value;
                            },
                            localizations: localizations,
                            isEnabled: true,
                          ),
                        ),
                      ],
                    );
                  },
                  orElse: () {
                    AppLogger.d('Showing disabled dropdowns (loading or error)', 'ROOMS');
                    return Row(
                      children: [
                        Expanded(
                          child: _RoomStatusFilterDropdown(
                            selectedStatusFilter: null,
                            onStatusFilterChanged: (_) {},
                            localizations: localizations,
                            isEnabled: false,
                          ),
                        ),
                        const SizedBox(width: 8),
                        Expanded(
                          child: _RoomFilterDropdown(
                            roomNames: const [],
                            selectedFilter: null,
                            onFilterChanged: (_) {},
                            localizations: localizations,
                            isEnabled: false,
                          ),
                        ),
                      ],
                    );
                  },
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          roomsAsyncValue.when(
            data: (rooms) {
              var availableRooms = rooms.where((room) => room.status != 0 && room.status != 2).toList();

              // Apply status filter (rental_status)
              if (selectedStatusFilter != null && selectedStatusFilter.isNotEmpty) {
                final statusValue = int.tryParse(selectedStatusFilter);
                if (statusValue != null) {
                  availableRooms = availableRooms.where((room) => room.rentalStatus == statusValue).toList();
                }
              }

              // Apply room name filter
              if (selectedFilter != null && selectedFilter.isNotEmpty) {
                availableRooms = availableRooms.where((room) => room.name == selectedFilter).toList();
              }

              if (availableRooms.isEmpty) {
                // Beri padding juga untuk text kosong
                return Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  child: Text(localizations.roomTypeNoRoomsAvailable),
                );
              }
              return SizedBox(
                height: MediaQuery.of(context).size.height * 0.25,
                child: ListView.separated(
                  scrollDirection: Axis.horizontal,
                  padding: const EdgeInsets.symmetric(horizontal: 16.0),
                  itemCount: availableRooms.length,
                  separatorBuilder: (context, index) => const SizedBox(width: 16),
                  itemBuilder: (context, index) {
                    final room = availableRooms[index];
                    // If rental_status = 1, override room status to show as unavailable (status 3)
                    final int displayStatus = (room.rentalStatus == 1) ? 3 : (room.status ?? 0);

                    return ConstrainedBox(
                      constraints: BoxConstraints(
                        maxWidth: MediaQuery.of(context).size.width * 0.7,
                      ),
                      child: GestureDetector(
                        onTap: () {
                          var routeName = RouteNames.detailProperty;
                          AppLogger.d('$routeName : Navigating to room details for room ID: ${room.id} and room Name : ${room.name}', 'ROOMS');
                          context.push('/roomdetails/${room.id}', extra: {
                            'property': propertyData,
                            'room': room,
                          });
                        },
                        child: RoomCard(
                          isRoomDetail: true,
                          image: room.thumbnail,
                          title: room.name ?? '-',
                          roomStatus: displayStatus,
                          detail: room.descriptions ?? '-',
                          no: room.no.toString(),
                          price: formatCurrency(room.priceOriginalMonthly ?? 0).toString(),
                          imageHeight: 150,
                        ),
                      ),
                    );
                  },
                ),
              );
            },
            loading: () => SizedBox(
              height: MediaQuery.of(context).size.height * 0.25,
              child: ListView.separated(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16.0),
                itemCount: 3,
                separatorBuilder: (context, index) => const SizedBox(width: 16),
                itemBuilder: (context, index) {
                  return ConstrainedBox(
                    constraints: BoxConstraints(
                      maxWidth: MediaQuery.of(context).size.width * 0.7,
                    ),
                    child: Skeletonizer(
                      enabled: true,
                      child: RoomCard(
                        isRoomDetail: true,
                        image: AppImage.defaultRoomImage,
                        title: 'Loading Room Name Here',
                        roomStatus: 1,
                        detail: 'Loading Description',
                        no: '101',
                        price: 'Rp 1.000.000',
                        imageHeight: 150,
                      ),
                    ),
                  );
                },
              ),
            ),
            error: (err, stack) {
              AppLogger.e('Error loading rooms for property $propertyId', err, stack, 'ROOMS');
              return ErrorDisplayWidget(
                error: err,
                stackTrace: stack,
              );
            },
          ),
          const SizedBox(height: 8),
        ],
      ),
    );
  }
}

// Status Filter Dropdown Widget
class _RoomStatusFilterDropdown extends StatelessWidget {
  final String? selectedStatusFilter;
  final ValueChanged<String?> onStatusFilterChanged;
  final AppLocalizations localizations;
  final bool isEnabled;

  const _RoomStatusFilterDropdown({
    required this.selectedStatusFilter,
    required this.onStatusFilterChanged,
    required this.localizations,
    required this.isEnabled,
  });

  @override
  Widget build(BuildContext context) {
    // Dark mode detection for dropdown container and text
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1F2937) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: AppColors.primaryColor.withValues(alpha: 0.3),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String?>(
          value: selectedStatusFilter,
          icon: Icon(
            Icons.filter_alt_rounded,
            size: 20,
            color: isEnabled ? AppColors.primaryColor : Colors.grey[400],
          ),
          isDense: true,
          isExpanded: true,
          style: TextStyle(
            fontSize: 13,
            color: isEnabled ? (isDark ? Colors.white : Colors.black87) : Colors.grey[400],
            fontWeight: FontWeight.w500,
          ),
          borderRadius: BorderRadius.circular(12),
          dropdownColor: isDark ? const Color(0xFF374151) : Colors.white,
          items: [
            DropdownMenuItem<String?>(
              value: null,
              child: Row(
                children: [
                  Icon(
                    Icons.all_inclusive,
                    size: 18,
                    color: isDark ? Colors.grey[400] : Colors.grey[600],
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      localizations.roomFilterAllStatus,
                      style: TextStyle(
                        color: isDark ? Colors.white : Colors.grey[800],
                        fontSize: 13,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            DropdownMenuItem<String?>(
              value: '0',
              child: Row(
                children: [
                  Icon(
                    Icons.check_circle_outline,
                    size: 18,
                    color: Colors.green[600],
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      localizations.roomFilterAvailable,
                      style: TextStyle(fontSize: 13, color: isDark ? Colors.white : null),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            DropdownMenuItem<String?>(
              value: '1',
              child: Row(
                children: [
                  Icon(
                    Icons.event_busy,
                    size: 18,
                    color: Colors.red[600],
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      localizations.roomFilterOccupied,
                      style: TextStyle(fontSize: 13, color: isDark ? Colors.white : null),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
          ],
          onChanged: isEnabled ? onStatusFilterChanged : null,
          hint: Row(
            children: [
              Icon(
                Icons.filter_alt_outlined,
                size: 18,
                color: isEnabled ? Colors.grey[600] : Colors.grey[400],
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  'Status',
                  style: TextStyle(
                    fontSize: 13,
                    color: isEnabled ? Colors.grey[600] : Colors.grey[400],
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

// Room Name Filter Dropdown Widget
class _RoomFilterDropdown extends StatelessWidget {
  final List<String> roomNames;
  final String? selectedFilter;
  final ValueChanged<String?> onFilterChanged;
  final AppLocalizations localizations;
  final bool isEnabled;

  const _RoomFilterDropdown({
    required this.roomNames,
    required this.selectedFilter,
    required this.onFilterChanged,
    required this.localizations,
    required this.isEnabled,
  });

  @override
  Widget build(BuildContext context) {
    // Dark mode detection for dropdown container and text
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
      decoration: BoxDecoration(
        color: isDark ? const Color(0xFF1F2937) : Colors.white,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: AppColors.primaryColor.withValues(alpha: 0.3),
          width: 1,
        ),
        boxShadow: [
          BoxShadow(
            color: Colors.black.withValues(alpha: 0.05),
            blurRadius: 4,
            offset: const Offset(0, 2),
          ),
        ],
      ),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String?>(
          value: selectedFilter,
          icon: Icon(
            Icons.filter_list_rounded,
            size: 20,
            color: isEnabled ? AppColors.primaryColor : Colors.grey[400],
          ),
          isDense: true,
          isExpanded: true,
          style: TextStyle(
            fontSize: 13,
            color: isEnabled ? (isDark ? Colors.white : Colors.black87) : Colors.grey[400],
            fontWeight: FontWeight.w500,
          ),
          borderRadius: BorderRadius.circular(12),
          dropdownColor: isDark ? const Color(0xFF374151) : Colors.white,
          items: [
            DropdownMenuItem<String?>(
              value: null,
              child: Row(
                children: [
                  Icon(
                    Icons.clear_all,
                    size: 18,
                    color: isDark ? Colors.grey[400] : Colors.grey[600],
                  ),
                  const SizedBox(width: 8),
                  Expanded(
                    child: Text(
                      localizations.roomSortAllRooms,
                      style: TextStyle(
                        color: isDark ? Colors.white : Colors.grey[800],
                        fontSize: 13,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                  ),
                ],
              ),
            ),
            ...roomNames.map((roomName) {
              return DropdownMenuItem<String?>(
                value: roomName,
                child: Row(
                  children: [
                    Icon(
                      Icons.meeting_room_outlined,
                      size: 18,
                      color: AppColors.primaryColor,
                    ),
                    const SizedBox(width: 8),
                    Expanded(
                      child: Text(
                        roomName,
                        style: TextStyle(fontSize: 13, color: isDark ? Colors.white : null),
                        overflow: TextOverflow.ellipsis,
                      ),
                    ),
                  ],
                ),
              );
            }).toList(),
          ],
          onChanged: isEnabled ? onFilterChanged : null,
          hint: Row(
            children: [
              Icon(
                Icons.tune_rounded,
                size: 18,
                color: isEnabled ? Colors.grey[600] : Colors.grey[400],
              ),
              const SizedBox(width: 6),
              Expanded(
                child: Text(
                  localizations.roomSortFilterBy,
                  style: TextStyle(
                    fontSize: 13,
                    color: isEnabled ? Colors.grey[600] : Colors.grey[400],
                  ),
                  overflow: TextOverflow.ellipsis,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
