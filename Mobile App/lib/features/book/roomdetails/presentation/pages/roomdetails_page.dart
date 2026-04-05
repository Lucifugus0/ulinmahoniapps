import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/widgets/html_description.dart';
import 'package:intl/intl.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../provider/roomdetails_provider.dart';
import '../../../../../core/layout/mainlayout.dart';
import '../../../../../core/widgets/appbar/custom_appbar.dart';
import '../widgets/roomfacility_section.dart';
import '../widgets/roomfacility_iconify_section.dart';
import '../../model/rooms_model.dart';
import '../../../detailproperty/model/detailproperty_model.dart';
import '../widgets/inputform_section.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../provider/checkavaibilty_provider.dart';
import '../../../../auth/login/provider/auth_provider.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/widgets/image_viewer_popup.dart';
import '../../provider/rooms_provider.dart';

class RoomDetailsPage extends ConsumerStatefulWidget {
  final RoomModel room;
  final DetailPropertyModel propertyData;

  const RoomDetailsPage({
    Key? key,
    required this.room,
    required this.propertyData,
  }) : super(key: key);
  @override
  ConsumerState<RoomDetailsPage> createState() => _RoomDetailsPageState();
}

class _RoomDetailsPageState extends ConsumerState<RoomDetailsPage> {
  late TextEditingController _checkInDateController;
  late TextEditingController _checkOutDateController;

  bool _isFormValid = false;
  String? _availabilityWarningText;

  late PageController _imagePageController;
  Timer? _imageAutoSlideTimer;
  final ValueNotifier<int> _currentImagePageNotifier = ValueNotifier<int>(0);

  @override
  void didUpdateWidget(covariant RoomDetailsPage oldWidget) {
    super.didUpdateWidget(oldWidget);
    _updateDateControllers();
  }

  @override
  void initState() {
    super.initState();
    _checkInDateController = TextEditingController();
    _checkOutDateController = TextEditingController();
    _imagePageController = PageController(initialPage: 0);
    _imagePageController.addListener(_onImagePageChanged);

    WidgetsBinding.instance.addPostFrameCallback((_) {
      AppLogger.d('🔄 RoomDetailsPage InitState: Memuat detail ruangan setelah frame callback...', 'ROOM-DETAILS');
      ref.read(roomDetailsProvider.notifier).loadRoomDetails(widget.room, widget.propertyData);
      _startImageAutoSlide();

      final validRentTypes = <String>[];

      // Cek ketersediaan Daily (cek kedua kemungkinan nama field: map atau int)
      if (widget.room.periode['daily'] == true || widget.room.periode_daily == 1) {
        validRentTypes.add('Daily'); // Gunakan format Title Case agar konsisten
      }

      // Cek ketersediaan Monthly
      if (widget.room.periode['monthly'] == true || widget.room.periode_monthly == 1) {
        validRentTypes.add('Monthly');
      }

      // 3. Ambil tipe sewa yang dibawa dari Search (Input Awal dari state)
      final currentRead = ref.read(roomDetailsProvider).value;
      final initialInputType = currentRead?['rentType'] as String?;

      AppLogger.d("🔍 DEBUG VALIDASI: Input Search='$initialInputType', Tersedia di DB=$validRentTypes", 'ROOM-DETAILS');

      // 4. LOGIKA VALIDASI & AUTO-SWITCH
      if (initialInputType != null && validRentTypes.isNotEmpty) {
        // Cek apakah input awal ada di daftar valid (Case Insensitive)
        bool isInputValid = validRentTypes.any((t) => t.toLowerCase() == initialInputType.toLowerCase());

        if (!isInputValid) {
          // KONFLIK DITEMUKAN!
          // Contoh: User cari 'Daily', tapi kamar cuma punya 'Monthly'.

          // Ambil opsi pertama yang valid sebagai pengganti (misal: 'Monthly')
          final fallbackType = validRentTypes.first;

          AppLogger.w("⚠️ KONFLIK DATA: Tipe '$initialInputType' tidak tersedia. Auto-switch ke '$fallbackType'", 'ROOM-DETAILS');

          // UPDATE STATE: Paksa ubah ke tipe yang valid
          ref.read(roomDetailsProvider.notifier).updateRentType(fallbackType);

          // Reset durasi ke 1 (karena durasi daily vs monthly beda satuan)
          ref.read(roomDetailsProvider.notifier).updateDuration(1);

          // Beritahu user lewat Snackbar
          Future.delayed(const Duration(milliseconds: 500), () {
            if (mounted) {
              _showSnackbar(
                  "Penyewaan $initialInputType tidak tersedia untuk kamar ini. Dialihkan ke $fallbackType.",
                  AppColors.secondaryColor
              );
            }
          });
        }
      }
      // Edge Case: Jika input kosong, atau hanya ada 1 opsi tersedia, paksa pilih opsi itu.
      else if (validRentTypes.isNotEmpty) {
        ref.read(roomDetailsProvider.notifier).updateRentType(validRentTypes.first);
      }

      // Auto-trigger availability check jika form sudah lengkap dari awal
      _triggerAvailabilityCheckIfReady();
    });
  }

  @override
  void dispose() {
    _checkInDateController.dispose();
    _checkOutDateController.dispose();
    _imagePageController.removeListener(_onImagePageChanged);
    _imagePageController.dispose();
    _imageAutoSlideTimer?.cancel();
    _currentImagePageNotifier.dispose();
    super.dispose();
  }

  void _showSnackbar(String message, Color color) {
    showNotificationDialog(
      context,
      message,
      defaultIcon: Icons.info_outline,
      iconColor: color,
    );
  }

  Future<void> _refreshRoomData() async {
    AppLogger.d('🔄 Refreshing room data...', 'ROOM-DETAILS');
    // Invalidate the provider to force a refetch
    ref.invalidate(roomByIdProvider(widget.room.id!));
    // Wait for the new data to be fetched
    await ref.read(roomByIdProvider(widget.room.id!).future);
  }

  void _updateDateControllers() {
    final state = ref.read(roomDetailsProvider).value;
    if (state != null) {
      final checkInDate = state['checkInDate'] as DateTime?;
      final checkOutDate = state['checkOutDate'] as DateTime?;

      _checkInDateController.text = checkInDate != null
          ? DateFormat('dd-MM-yyyy').format(checkInDate)
          : '';

      _checkOutDateController.text = checkOutDate != null
          ? DateFormat('dd-MM-yyyy').format(checkOutDate)
          : '';
      AppLogger.d('✅ RoomDetailsPage: Controllers updated. Check-in: "${_checkInDateController.text}", Check-out: "${_checkOutDateController.text}"', 'ROOM-DETAILS');
    } else {
      _checkInDateController.clear();
      _checkOutDateController.clear();
      AppLogger.w('⚠️ RoomDetailsPage: _updateDateControllers dipanggil tapi state roomDetailsProvider null.', 'ROOM-DETAILS');
    }
  }

  Future<void> _selectCheckInDate() async {
    final localizations = AppLocalizations.of(context)!;
    final now = DateTime.now();
    // Daily: max 14 days ahead. Monthly: max 90 days ahead.
    final rentType = ref.read(roomDetailsProvider).value?['rentType'] as String?;
    final isMonthly = rentType?.toLowerCase() == 'monthly';
    final maxDaysAhead = isMonthly ? 90 : 14;
    final maxCheckInDate = DateTime(now.year, now.month, now.day + maxDaysAhead);
    DateTime? picked = await showDatePicker(
      context: context,
      initialDate: ref.read(roomDetailsProvider).value?['checkInDate'] ?? now,
      firstDate: now,
      lastDate: maxCheckInDate,
      builder: (BuildContext context, Widget? child) {
        return Theme(
          data: Theme.of(context).copyWith(
            colorScheme: ColorScheme.light(
              primary: AppColors.primaryAdaptive(context),
              onPrimary: Colors.white,
              surface: Colors.white,
              onSurface: Colors.black,
            ),
            dialogBackgroundColor: Colors.white,
          ),
          child: child!,
        );
      },
    );
    if (picked != null) {
      final pickedWithTime = DateTime(picked.year, picked.month, picked.day, now.hour, now.minute, now.second);
      AppLogger.d('✅ RoomDetailsPage: Tanggal Check-in baru dipilih: ${pickedWithTime}', 'ROOM-DETAILS');
      ref.read(roomDetailsProvider.notifier).updateCheckInDate(pickedWithTime);
      _triggerAvailabilityCheckIfReady();
    }
  }

  void _onDurationChanged(int? value) {
    AppLogger.d('✅ RoomDetailsPage: Durasi diubah menjadi: $value', 'ROOM-DETAILS');
    ref.read(roomDetailsProvider.notifier).updateDuration(value);
    _triggerAvailabilityCheckIfReady();
  }

  void _onRentTypeChanged(String? value) {
    AppLogger.d('✅ RoomDetailsPage: Tipe Sewa diubah menjadi: $value', 'ROOM-DETAILS');
    ref.read(roomDetailsProvider.notifier).updateRentType(value);
    _triggerAvailabilityCheckIfReady();
  }

  void _triggerAvailabilityCheckIfReady() {
    final roomState = ref.read(roomDetailsProvider).value;
    if (roomState == null) return;

    final checkInDate = roomState['checkInDate'] as DateTime?;
    final rentType = roomState['rentType'] as String?;
    final duration = roomState['duration'] as int?;

    AppLogger.d('🔎 [DEBUG] Data sebelum pemicu:', 'ROOM-DETAILS');
    AppLogger.d('  - Check-in Date: $checkInDate', 'ROOM-DETAILS');
    AppLogger.d('  - Rent Type: $rentType', 'ROOM-DETAILS');
    AppLogger.d('  - Duration: $duration', 'ROOM-DETAILS');

    if (checkInDate != null && rentType != null && duration != null && duration > 0) {
      AppLogger.d('✅ [DEBUG] Semua data terisi. Memicu pengecekan ketersediaan...', 'ROOM-DETAILS');
      _triggerAvailabilityCheck();
    } else {
      AppLogger.w('❌ [DEBUG] Data belum lengkap. Pengecekan tidak dilakukan.', 'ROOM-DETAILS');
    }
  }

  void _triggerAvailabilityCheck() {
    final roomState = ref.read(roomDetailsProvider).value;
    if (roomState == null) {
      AppLogger.w('⚠️ _triggerAvailabilityCheck: roomDetailsProvider state is null. Cannot check availability.', 'ROOM-DETAILS');
      return;
    }

    final room = roomState['room'] as RoomModel?;
    final property = roomState['propertyData'] as DetailPropertyModel?;
    final checkInDate = roomState['checkInDate'] as DateTime?;
    final checkOutDate = roomState['checkOutDate'] as DateTime?;
    if (room != null && property != null && checkInDate != null && checkOutDate != null) {
      AppLogger.d('🔄 Memicu pengecekan ketersediaan...', 'ROOM-DETAILS');
      ref.read(availabilityCheckProvider.notifier).checkRoomAvailability(
        propertyId: property.id!,
        roomId: room.id!,
        checkInDate: DateFormat('yyyy-MM-dd').format(checkInDate),
        checkOutDate: DateFormat('yyyy-MM-dd').format(checkOutDate),
      );
    }
  }

  void _checkFormValidityAndAvailability(WidgetRef ref, AuthState authState) {
    final localizations = AppLocalizations.of(context)!;
    final roomDetailsState = ref.watch(roomDetailsProvider).value;
    final availabilityCheckState = ref.watch(availabilityCheckProvider); 

    
    bool isFormComplete = false;
    if (roomDetailsState != null) {
      final rentType = roomDetailsState['rentType'] as String?;
      final duration = roomDetailsState['duration'] as int?;
      final checkInDate = roomDetailsState['checkInDate'] as DateTime?;
      final checkOutDate = roomDetailsState['checkOutDate'] as DateTime?;

      isFormComplete = rentType != null &&
          duration != null && duration > 0 &&
          checkInDate != null &&
          checkOutDate != null;
    }

    
    bool isLoggedIn = authState.isLoggedIn;

    

    // Riverpod 3.x: .value is now nullable by default, replacing .valueOrNull
    final user = authState.user.value;
    final profilePhotoUrl = user?.profilePhotoUrl ?? '';
    final profilePhotoPath = user?.profilePhotoPath ?? '';
    bool hasProfilePic = isLoggedIn &&
        (profilePhotoUrl.isNotEmpty || profilePhotoPath.isNotEmpty);


    final availabilityAsyncValue = availabilityCheckState.isRoomAvailable;
    // Riverpod 3.x: .value is now nullable by default, replacing .valueOrNull
    bool isRoomAvailable = availabilityAsyncValue.value ?? false;

    String? newWarningText;
    bool isValidNow = false;

    // Get room data to check status
    final roomData = roomDetailsState?['room'] as RoomModel?;
    final roomStatus = roomData?.status ?? 0;
    final rentalStatus = roomData?.rentalStatus ?? 0;

    // Priority 1: Check room status (cannot book if not status 1 or rental_status is 1)
    if (roomStatus == 0) {
      // Status 0: Not Available
      newWarningText = localizations.roomDetailsRoomStatusNotAvailable;
      isValidNow = false;
    } else if (roomStatus == 2) {
      // Status 2: Under Maintenance
      newWarningText = localizations.roomDetailsRoomStatusUnderMaintenance;
      isValidNow = false;
    } else if (roomStatus == 3 || rentalStatus == 1) {
      // Status 3 or rental_status 1: Currently Rented/Occupied
      newWarningText = rentalStatus == 1
          ? localizations.roomDetailsRoomStatusOccupied
          : localizations.roomDetailsRoomStatusCurrentlyRented;
      isValidNow = false;
    } else if (roomStatus != 1) {
      // Unknown status (not 0, 1, 2, or 3)
      newWarningText = localizations.roomDetailsRoomStatusCannotBook;
      isValidNow = false;
    } else if (!isLoggedIn) {
      // Priority 2: Check login
      newWarningText = localizations.roomDetailsLoginRequired;
      isValidNow = false;
    } else if (!isFormComplete) {
      newWarningText = null;
      isValidNow = false;
    } else if (availabilityAsyncValue.isLoading) {
      newWarningText = localizations.roomDetailsCheckingAvailability;
      isValidNow = false;
    } else if (availabilityAsyncValue.hasError) {
      newWarningText = localizations.roomDetailsFailedToCheckAvailability;
      isValidNow = false;
    } else if (isRoomAvailable) {
      newWarningText = localizations.roomDetailsRoomAvailable;
      isValidNow = true;
    } else {
      newWarningText = localizations.roomDetailsRoomNotAvailable;
      isValidNow = false;
    }

    _isFormValid = isValidNow;
    _availabilityWarningText = newWarningText;

    AppLogger.d('✅ RoomDetailsPage: isFormValid Final: $_isFormValid', 'ROOM-DETAILS');
    AppLogger.d('✅ RoomDetailsPage: availabilityWarningText Final: $_availabilityWarningText', 'ROOM-DETAILS');
  }
  void _onImagePageChanged() {
    if (_imagePageController.hasClients) {
      int currentPage = (_imagePageController.page?.round() ?? 0);
      final roomData = ref.read(roomDetailsProvider).value?['room'] as RoomModel?;
      if (roomData != null) {
        final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(roomData);
        if (sliderImageProviders.isNotEmpty) {
          _currentImagePageNotifier.value = currentPage % sliderImageProviders.length;
        }
      }
    }
  }

  void _startImageAutoSlide() {
    _imageAutoSlideTimer?.cancel();
    final roomData = ref.read(roomDetailsProvider).value?['room'] as RoomModel?;
    if (roomData == null) {
      AppLogger.d('DEBUG GAMBAR: roomData null, auto-slide tidak dimulai.', 'ROOM-DETAILS');
      return;
    }

    final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(roomData);
    AppLogger.d('DEBUG GAMBAR: Memulai auto-slide. Jumlah gambar: ${sliderImageProviders.length}', 'ROOM-DETAILS');

    if (sliderImageProviders.length > 1) {
      _imageAutoSlideTimer = Timer.periodic(const Duration(seconds: 3), (Timer timer) {
        // PENTING: Cek apakah widget masih mounted sebelum melakukan animasi
        if (!mounted) {
          timer.cancel();
          return;
        }

        if (_imagePageController.hasClients) {
          int nextPage = (_imagePageController.page!.toInt() + 1);
          _imagePageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 400),
            curve: Curves.easeIn,
          );
          AppLogger.d('DEBUG GAMBAR: Auto-sliding ke halaman: $nextPage', 'ROOM-DETAILS');
        }
      });
    } else {
      AppLogger.d('DEBUG GAMBAR: Tidak ada cukup gambar untuk auto-slide.', 'ROOM-DETAILS');
    }
  }

  bool _isPriceValid(String? priceString) {
    if (priceString == null || priceString.isEmpty) {
      return false;
    }
    final value = double.tryParse(priceString.replaceAll('.', ''));
    return value != null && value != 0.0;
  }

  List<ImageProvider> _getValidImageProvidersForSlider(RoomModel room) {
    List<ImageProvider> imageProviders = [];
    bool roomImageShowAdded = false;

    // Add roomimageshow first (if exists)
    if (room.roomimageshow != null && room.roomimageshow!.isNotEmpty) {
      ImageProvider? provider = _getImageProviderFromString(room.roomimageshow!);
      if (provider != null) {
        imageProviders.add(provider);
        roomImageShowAdded = true;
      }
    }

    // Add remaining images from roomimages (skip first if roomimageshow was added to avoid duplicate)
    if (room.roomimages != null && room.roomimages!.isNotEmpty) {
      int startIndex = roomImageShowAdded ? 1 : 0; // Skip first image if roomimageshow already added
      for (int i = startIndex; i < room.roomimages!.length; i++) {
        final imgModel = room.roomimages![i];
        if (imgModel.imageData != null && imgModel.imageData!.isNotEmpty) {
          ImageProvider? provider = _getImageProviderFromString(imgModel.imageData!);
          if (provider != null) {
            imageProviders.add(provider);
          }
        }
      }
    }

    // Fallback to property images if no room images
    if (imageProviders.isEmpty && widget.propertyData.images != null && widget.propertyData.images!.isNotEmpty) {
      AppLogger.d('DEBUG GAMBAR: Tidak ada gambar kamar, mencoba menambahkan gambar properti.', 'ROOM-DETAILS');
      for (final imgModel in widget.propertyData.images!) {
        ImageProvider? provider = _getImageProviderFromString(imgModel.imageData ?? '');
        if (provider != null) {
          imageProviders.add(provider);
        }
      }
    }

    // Final fallback to default image
    if (imageProviders.isEmpty) {
      imageProviders.add(const AssetImage(AppImage.defaultRoomImage));
      AppLogger.d('DEBUG GAMBAR: Tidak ada gambar valid, menggunakan gambar default.', 'ROOM-DETAILS');
    }
    return imageProviders;
  }

  ImageProvider? _getImageProviderFromString(String imageString) {
    if (imageString.startsWith('data:image')) {
      try {
        String base64String = imageString.split(',').last.trim().replaceAll(RegExp(r'\s'), '');
        return MemoryImage(base64Decode(base64String));
      } catch (e) {
        return null;
      }
    } else if (imageString.startsWith('http://') || imageString.startsWith('https://')) {
      return NetworkImage(imageString);
    } else if (imageString.startsWith('/9j/') || imageString.startsWith('iVBORw0KGgoAAAANSUhEU') || imageString.length > 100) {
      try {
        String rawBase64Data = imageString.trim().replaceAll(RegExp(r'\s'), '');
        return MemoryImage(base64Decode(rawBase64Data));
      } catch (e) {
        return null;
      }
    } else {
      return null;
    }
  }

  Widget _buildImagePageIndicator(List<ImageProvider> sliderImageProviders) {
    if (sliderImageProviders.length <= 1) {
      return const SizedBox.shrink();
    }
    return Positioned(
      top: 20,
      left: 0,
      right: 0,
      child: Align(
        alignment: Alignment.center,
        child: ValueListenableBuilder<int>(
          valueListenable: _currentImagePageNotifier,
          builder: (context, currentPage, child) {
            return Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                sliderImageProviders.length,
                    (index) => GestureDetector(
                  onTap: () {
                    _imagePageController.animateToPage(
                      index,
                      duration: const Duration(milliseconds: 400),
                      curve: Curves.easeIn,
                    );
                  },
                  child: Container(
                    width: 6.0,
                    height: 6.0,
                    margin: const EdgeInsets.symmetric(horizontal: 5.0),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      color: currentPage == index
                          ? AppColors.primaryAdaptive(context)
                          : Colors.grey.withOpacity(0.7),
                    ),
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final authState = ref.watch(authProvider);
    _checkFormValidityAndAvailability(ref,authState);
    final roomDetailsState = ref.watch(roomDetailsProvider);
    final textTheme = Theme.of(context).textTheme;
    final screenHeight = MediaQuery.of(context).size.height;

    // Watch roomByIdProvider to get fresh data
    final roomByIdAsync = ref.watch(roomByIdProvider(widget.room.id!));

    return roomDetailsState.when(
      loading: () => MainLayout(
        currentIndex: 0,
        showNavBar: false,
        showBottomNav: false,
        showContactBar: true,
        child: Skeletonizer(
          enabled: true,
          child: SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  height: 300,
                  color: Colors.grey[200],
                ),
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'Loading Room Name',
                        style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold),
                      ),
                      const SizedBox(height: 8),
                      Text('Room Type: Loading'),
                      const SizedBox(height: 16),
                      Text('Price: Rp 0 / night'),
                      const SizedBox(height: 24),
                      Text('Facilities', style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
                      const SizedBox(height: 12),
                      Wrap(
                        spacing: 8,
                        runSpacing: 8,
                        children: List.generate(
                          4,
                          (index) => Chip(label: Text('Loading')),
                        ),
                      ),
                      const SizedBox(height: 24),
                      Container(
                        height: 56,
                        decoration: BoxDecoration(
                          color: Colors.grey[300],
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                      const SizedBox(height: 12),
                      Container(
                        height: 56,
                        decoration: BoxDecoration(
                          color: Colors.grey[300],
                          borderRadius: BorderRadius.circular(8),
                        ),
                      ),
                    ],
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
      error: (err, stack) => MainLayout(
        currentIndex: 0,
        showNavBar: false,
        showBottomNav: false,
        showContactBar: true,
        child: Center(
            child: Text(
                '${localizations.roomDetailsError}: $err',
                style: textTheme.bodyLarge?.copyWith(color: AppColors.secondaryColor)
            )
        ),
      ),
      data: (data) {
        // Use fresh room data from API if available, otherwise use passed data
        // Riverpod 3.x: .value is now nullable by default, replacing .valueOrNull
        final _roomData = roomByIdAsync.value ?? data['room'] as RoomModel?;
        final _propertyData = data['propertyData'] as DetailPropertyModel?;
        final _rentType = data['rentType'] as String?;
        final _duration = data['duration'] as int?;
        final _checkInDate = data['checkInDate'] as DateTime?;
        final _checkOutDate = data['checkOutDate'] as DateTime?;

        // Debug logging for room data
        if (_roomData != null) {
          AppLogger.d('🏠 Room Data in UI:', 'ROOM-DETAILS-PAGE');
          AppLogger.d('  - Room Name: ${_roomData.name}', 'ROOM-DETAILS-PAGE');
          AppLogger.d('  - Deposit Fee: ${_roomData.depositFee}', 'ROOM-DETAILS-PAGE');
          AppLogger.d('  - Parking Fees Count: ${_roomData.parkingFees.length}', 'ROOM-DETAILS-PAGE');
          for (var parking in _roomData.parkingFees) {
            AppLogger.d('    * Type: ${parking.parkingType}, Fee: ${parking.fee}', 'ROOM-DETAILS-PAGE');
          }
        }

        _checkInDateController.text = _checkInDate != null
            ? DateFormat('dd-MM-yyyy').format(_checkInDate)
            : '';
        _checkOutDateController.text = _checkOutDate != null
            ? DateFormat('dd-MM-yyyy').format(_checkOutDate)
            : '';

        if (_roomData == null || _propertyData == null) {
          return MainLayout(
            currentIndex: 0,
            showNavBar: false,
            showBottomNav: false,
            showContactBar: true,
            child: Center(
                child: Text(
                    localizations.roomDetailsLoading,
                    style: textTheme.bodyLarge
                )
            ),
          );
        }

        final List<String> availableRentTypes = [];
        if (widget.room.periode_daily == 1) {
          availableRentTypes.add('daily');
        }
        if (widget.room.periode_monthly == 1) {
          availableRentTypes.add('monthly');
        }

        final List<String> roomFacility = _roomData.facility ?? [];

        final bookingData = {
          'room': _roomData,
          'propertyData': _propertyData,
          'rentType': _rentType,
          'duration': _duration,
          'checkInDate': _checkInDate,
          'checkOutDate': _checkOutDate,
          'roomImage': _roomData.roomimageshow,
          'depositFee': _roomData.depositFee,
          'parkingFees': _roomData.parkingFees,
        };

        // Calculate subtotal (duration × price)
        String displayPrice = "N/A";
        String? displayRentType;
        double basePrice = 0;

        if (_rentType == 'Daily') {
          if (_isPriceValid(_roomData.priceOriginalDaily)) {
            basePrice = double.tryParse(_roomData.priceOriginalDaily ?? "0") ?? 0;
            displayRentType = localizations.roomDetailsDaily;
          }
        } else if (_rentType == 'Monthly') {
          if (_isPriceValid(_roomData.priceOriginalMonthly)) {
            basePrice = double.tryParse(_roomData.priceOriginalMonthly ?? "0") ?? 0;
            displayRentType = localizations.roomDetailsMonthly;
          }
        } else {
          // Fallback if rentType not selected
          if (_isPriceValid(_roomData.priceOriginalDaily)) {
            basePrice = double.tryParse(_roomData.priceOriginalDaily ?? "0") ?? 0;
            displayRentType = localizations.roomDetailsDaily;
          } else if (_isPriceValid(_roomData.priceOriginalMonthly)) {
            basePrice = double.tryParse(_roomData.priceOriginalMonthly ?? "0") ?? 0;
            displayRentType = localizations.roomDetailsMonthly;
          }
        }

        // Calculate subtotal: price × duration
        double subtotal = basePrice * (_duration ?? 1);
        displayPrice = formatCurrency(subtotal.toStringAsFixed(0));

        final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(_roomData);
        int? pageViewItemCount = sliderImageProviders.length > 1 ? null : 1;
        AppLogger.d('form validation???$_isFormValid', 'ROOM-DETAILS');

        // Dark mode detection for scaffold and content card colors
        final isDark = Theme.of(context).brightness == Brightness.dark;

        return MainLayout(
          currentIndex: 0,
          showNavBar: false,
          showBottomNav: false,
          showContactBar: true,
          bottomcontactbar_pesansekarang: true,
          bottomcontactbar_data: bookingData,
          bottomcontactbar_buttonenabled: _isFormValid,
          bottomcontactbar_price: displayPrice,
          bottomcontactbar_renttype: displayRentType,
          bottomcontactbar_pricelabel: localizations.bottomBarSubtotal,
          bottomcontactbar_warningtext: _availabilityWarningText,
          bottomcontactbar_buttonpressed: () {
            final authState = ref.read(authProvider);
            final roomDetailsState = ref.read(roomDetailsProvider).value;

            // Priority 1: Check if user is logged in
            if (!authState.isLoggedIn) {
              showNotificationDialog(
                context,
                localizations.roomDetailsLoginRequired,
                title: 'Login Required',
                defaultIcon: Icons.lock_outline,
                iconColor: AppColors.primaryAdaptive(context),
                okButtonText: 'Login',
                onOkPressed: () {
                  // Navigate to login page
                  context.push('/login');
                },
              );
              return;
            }

            // Priority 2: Check if form is complete
            bool isFormComplete = false;
            if (roomDetailsState != null) {
              final rentType = roomDetailsState['rentType'] as String?;
              final duration = roomDetailsState['duration'] as int?;
              final checkInDate = roomDetailsState['checkInDate'] as DateTime?;
              final checkOutDate = roomDetailsState['checkOutDate'] as DateTime?;

              isFormComplete = rentType != null &&
                  duration != null && duration > 0 &&
                  checkInDate != null &&
                  checkOutDate != null;
            }

            if (!isFormComplete) {
              showNotificationDialog(
                context,
                localizations.roomDetailsCompleteForm,
                defaultIcon: Icons.edit_outlined,
                iconColor: AppColors.secondaryColor,
              );
              return;
            }

            // Priority 3: Check if validation passed (availability check)
            if (_isFormValid) {
              context.push('/payment', extra: bookingData);
            } else {
              // Show availability warning or other validation error
              String errorMessage = _availabilityWarningText ?? localizations.roomDetailsCompleteForm;
              showNotificationDialog(
                context,
                errorMessage,
                defaultIcon: Icons.error_outline,
                iconColor: AppColors.secondaryColor,
              );
            }
          },
          child: Scaffold(
            backgroundColor: isDark ? const Color(0xFF111827) : Colors.white,
            appBar: CustomAppBar(
              title: localizations.roomDetailsPageTitle,
            ),
            body: SafeArea(
              child: RefreshIndicator(
                onRefresh: _refreshRoomData,
                child: SingleChildScrollView(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      SizedBox(
                        height: screenHeight * 0.45,
                        child: Stack(
                          children: [
                            PageView.builder(
                              controller: _imagePageController,
                              itemCount: pageViewItemCount,
                              itemBuilder: (context, index) {
                                final int actualIndex = sliderImageProviders.isNotEmpty
                                    ? index % sliderImageProviders.length
                                    : 0;

                                if (sliderImageProviders.isEmpty) {
                                  return Container(
                                    color: Colors.grey[200],
                                    child: const Center(
                                      child: Icon(Icons.image_not_supported, size: 50, color: Colors.grey),
                                    ),
                                  );
                                }
                                return GestureDetector(
                                  onTap: () {
                                    showImageViewerPopup(
                                      context,
                                      sliderImageProviders,
                                      initialIndex: actualIndex,
                                    );
                                  },
                                  child: Container(
                                    decoration: BoxDecoration(
                                      image: DecorationImage(
                                        image: sliderImageProviders[actualIndex],
                                        fit: BoxFit.cover,
                                        alignment: Alignment.center,
                                      ),
                                    ),
                                  ),
                                );
                              },
                            ),
                            _buildImagePageIndicator(sliderImageProviders),
                          ],
                        ),
                      ),
                      Transform.translate(
                        offset: const Offset(0, -50),
                        child: Container(
                          margin: const EdgeInsets.symmetric(horizontal: 16),
                          padding: const EdgeInsets.all(16.0),
                          decoration: BoxDecoration(
                            color: isDark ? const Color(0xFF1F2937) : Colors.white,
                            borderRadius: BorderRadius.circular(15),
                            boxShadow: [
                              BoxShadow(
                                color: Colors.black.withValues(alpha: 0.12),
                                blurRadius: 16,
                                offset: const Offset(0, 4),
                              ),
                            ],
                          ),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              Text(
                                _roomData.name ?? '-',
                                style: textTheme.headlineMedium?.copyWith(
                                  fontWeight: FontWeight.bold,
                                  color: isDark ? Colors.white : Colors.black,
                                ),
                                softWrap: true,
                                overflow: TextOverflow.visible,
                              ),
                              const SizedBox(height: 4),
                              Row(
                                children: [
                                  if (_isPriceValid(_roomData.priceOriginalMonthly) && widget.room.periode_monthly == 1) ...[
                                    Text(
                                      formatCurrency(_roomData.priceOriginalMonthly!).toString(),
                                      style: TextStyle(fontSize: 18, color: isDark ? Colors.white : Colors.black),
                                    ),
                                    Text(
                                      localizations.roomDetailsPerMonth,
                                      style: TextStyle(fontSize: 14, color: isDark ? Colors.grey[300] : Colors.black),
                                    ),
                                  ],
                                  if (_isPriceValid(_roomData.priceOriginalMonthly) && _isPriceValid(_roomData.priceOriginalDaily))
                                    const SizedBox(width: 12),
                                  if (_isPriceValid(_roomData.priceOriginalDaily) && widget.room.periode_daily == 1) ...[
                                    Text(
                                      formatCurrency(_roomData.priceOriginalDaily!).toString(),
                                      style: TextStyle(fontSize: 18, color: isDark ? Colors.white : Colors.black),
                                    ),
                                    Text(
                                      localizations.roomDetailsPerDay,
                                      style: TextStyle(fontSize: 14, color: isDark ? Colors.grey[300] : Colors.black),
                                    ),
                                  ],
                                ],
                              ),
                          // Multi-language: resolve room description by current locale with fallback chain
                          Builder(builder: (context) {
                            final locale = Localizations.localeOf(context).languageCode;
                            final description = _roomData.descriptionsParsed?[locale]
                                ?? _roomData.descriptionsParsed?['en']
                                ?? _roomData.descriptionsParsed?['id']
                                ?? _roomData.descriptions
                                ?? '';
                            if (description.isNotEmpty) {
                              return HtmlDescription(
                                html: description,
                                textStyle: textTheme.bodyMedium,
                              );
                            }
                            return const SizedBox.shrink();
                          }),
                              const SizedBox(height: 4),
                              if (_roomData.level != null && _roomData.level!.isNotEmpty)
                                Row(
                                  children: [
                                    Icon(Icons.stairs, color: AppColors.primaryAdaptive(context), size: textTheme.bodyLarge?.fontSize),
                                    const SizedBox(width: 4),
                                    Text(localizations.roomDetailsFloor, style: textTheme.bodyMedium),
                                    Expanded(
                                      child: Text(_roomData.level!, style: textTheme.bodyMedium, overflow: TextOverflow.ellipsis),
                                    ),
                                  ],
                                ),
                              if (_roomData.size != null && _roomData.size! > 0)
                                Row(
                                  children: [
                                    Icon(Icons.square, color: AppColors.primaryAdaptive(context), size: textTheme.bodyLarge?.fontSize),
                                    const SizedBox(width: 4),
                                    Text(localizations.roomDetailsArea, style: textTheme.bodyMedium),
                                    Expanded(
                                      child: Text('${_roomData.size!.toString()} m²', style: textTheme.bodyMedium, overflow: TextOverflow.ellipsis),
                                    ),
                                  ],
                                ),
                              if (_roomData.capacity != null && _roomData.capacity! > 0)
                                Row(
                                  children: [
                                    Icon(Icons.people, color: AppColors.primaryAdaptive(context), size: textTheme.bodyLarge?.fontSize),
                                    const SizedBox(width: 4),
                                    Text(localizations.roomDetailsCapacity, style: textTheme.bodyMedium),
                                    Expanded(
                                      child: Text(localizations.roomDetailsCapacityCount(_roomData.capacity!), style: textTheme.bodyMedium, overflow: TextOverflow.ellipsis),
                                    ),
                                  ],
                                ),
                              if (_roomData.bed_type != null && _roomData.bed_type!.isNotEmpty)
                                Row(
                                  children: [
                                    Icon(Icons.king_bed_rounded, color: AppColors.primaryAdaptive(context), size: textTheme.bodyLarge?.fontSize),
                                    const SizedBox(width: 4),
                                    Text(localizations.roomDetailsBed, style: textTheme.bodyMedium),
                                    Expanded(
                                      child: Text(_roomData.bed_type!, style: textTheme.bodyMedium, overflow: TextOverflow.ellipsis),
                                    ),
                                  ],
                                ),
                              if (_roomData.no != null && _roomData.no!.isNotEmpty)
                                Row(
                                  children: [
                                    Icon(Icons.door_front_door_rounded, color: AppColors.primaryAdaptive(context), size: textTheme.bodyLarge?.fontSize),
                                    const SizedBox(width: 4),
                                    Expanded(
                                      child: Text(_roomData.no!, style: textTheme.bodyMedium, overflow: TextOverflow.ellipsis),
                                    ),
                                  ],
                                ),

                              // Biaya Tambahan Section (dipindahkan ke atas Informasi Pesanan)
                              const SizedBox(height: 24),
                              Text(
                                localizations.roomDetailsAdditionalFeesTitle,
                                style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 16),

                              // Deposit (only show if exists and > 0)
                              if (_roomData.depositFee != null && _roomData.depositFee! > 0) ...[
                                Row(
                                  children: [
                                    Icon(Icons.money, color: AppColors.primaryAdaptive(context), size: 20),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Text(
                                        localizations.roomDetailsDepositFee,
                                        style: textTheme.bodyMedium,
                                      ),
                                    ),
                                    Text(
                                      formatCurrency(_roomData.depositFee!),
                                      style: textTheme.bodyMedium?.copyWith(
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primaryAdaptive(context),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Padding(
                                  padding: const EdgeInsets.only(left: 32),
                                  child: Text(
                                    localizations.roomDetailsDepositNote,
                                    style: textTheme.bodySmall?.copyWith(
                                      color: Colors.grey[600],
                                      fontStyle: FontStyle.italic,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 12),
                              ],

                              // Parkir Mobil (Opsional)
                              if (_roomData.parkingFees.any((p) => p.parkingType?.toLowerCase() == 'car')) ...[
                                Row(
                                  children: [
                                    Icon(Icons.directions_car, color: AppColors.primaryAdaptive(context), size: 20),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Text(
                                        localizations.roomDetailsParkingCar,
                                        style: textTheme.bodyMedium,
                                      ),
                                    ),
                                    Text(
                                      formatCurrency(_roomData.parkingFees.firstWhere((p) => p.parkingType?.toLowerCase() == 'car').fee ?? 0),
                                      style: textTheme.bodyMedium?.copyWith(
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primaryAdaptive(context),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Padding(
                                  padding: const EdgeInsets.only(left: 32),
                                  child: Text(
                                    localizations.roomDetailsParkingOptional,
                                    style: textTheme.bodySmall?.copyWith(
                                      color: Colors.grey[600],
                                      fontStyle: FontStyle.italic,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 12),
                              ],

                              // Parkir Motor (Opsional)
                              if (_roomData.parkingFees.any((p) => p.parkingType?.toLowerCase() == 'motorcycle')) ...[
                                Row(
                                  children: [
                                    Icon(Icons.two_wheeler, color: AppColors.primaryAdaptive(context), size: 20),
                                    const SizedBox(width: 12),
                                    Expanded(
                                      child: Text(
                                        localizations.roomDetailsParkingMotorcycle,
                                        style: textTheme.bodyMedium,
                                      ),
                                    ),
                                    Text(
                                      formatCurrency(_roomData.parkingFees.firstWhere((p) => p.parkingType?.toLowerCase() == 'motorcycle').fee ?? 0),
                                      style: textTheme.bodyMedium?.copyWith(
                                        fontWeight: FontWeight.bold,
                                        color: AppColors.primaryAdaptive(context),
                                      ),
                                    ),
                                  ],
                                ),
                                const SizedBox(height: 4),
                                Padding(
                                  padding: const EdgeInsets.only(left: 32),
                                  child: Text(
                                    localizations.roomDetailsParkingOptional,
                                    style: textTheme.bodySmall?.copyWith(
                                      color: Colors.grey[600],
                                      fontStyle: FontStyle.italic,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 12),
                              ],

                              // Informasi Pesanan Section
                              const SizedBox(height: 24),
                              Text(
                                localizations.roomBookinginfoTitle,
                                style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                              ),
                              RoomInputSection(
                                rentType: _rentType,
                                duration: _duration,
                                checkInDate: _checkInDate,
                                checkOutDate: _checkOutDate,
                                checkInDateController: _checkInDateController,
                                checkOutDateController: _checkOutDateController,
                                onRentTypeChanged: _onRentTypeChanged,
                                onDurationChanged: _onDurationChanged,
                                onSelectCheckInDate: _selectCheckInDate,
                                availableRentTypes: availableRentTypes,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                localizations.roomDetailsFacilitiesTitle,
                                style: textTheme.titleLarge?.copyWith(fontWeight: FontWeight.bold),
                              ),
                              const SizedBox(height: 16),
                              // Use new format with icons if available, otherwise fallback to old format
                              if (_roomData.facilities != null && _roomData.facilities!.isNotEmpty)
                                Center(
                                  child: RoomFacilitiesIconifyGrid(facilities: _roomData.facilities!),
                                )
                              else if (roomFacility.isNotEmpty)
                                Center(
                                  child: RoomFacilitiesTextGrid(facilities: roomFacility),
                                )
                              else
                                Text(
                                  localizations.roomDetailsNoFacilities,
                                  style: textTheme.bodyMedium?.copyWith(
                                    fontStyle: FontStyle.italic,
                                    color: Colors.grey[600],
                                  ),
                                ),
                            ],
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            ),
          ),
        );
      },
    );
  }
}