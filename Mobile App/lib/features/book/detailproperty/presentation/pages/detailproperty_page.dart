import 'dart:async';
import 'dart:convert';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/widgets/html_description.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../roomdetails/provider/rooms_provider.dart';
import '../../../../../core/layout/mainlayout.dart';
import '../../../../../core/widgets/appbar/custom_appbar.dart';
import '../widgets/section/rooms.dart';
import '../../model/detailproperty_model.dart';
import '../../provider/detailproperty_provider.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/utils/formatcurrency.dart';
import '../widgets/section/facility.dart';
import '../widgets/section/facility_iconify.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/widgets/image_viewer_popup.dart';
import 'package:flutter_map/flutter_map.dart';
import 'package:latlong2/latlong.dart';
import 'package:url_launcher/url_launcher.dart';

class DetailPropertyPage extends ConsumerStatefulWidget {
  final int id;

  const DetailPropertyPage({Key? key, required this.id}) : super(key: key);

  @override
  ConsumerState<DetailPropertyPage> createState() => _DetailHousePageState();
}

class _DetailHousePageState extends ConsumerState<DetailPropertyPage> {
  late PageController _pageController;
  Timer? _timer;
  final ValueNotifier<int> _currentPageNotifier = ValueNotifier<int>(0);
  // Key to locate the rooms section for scroll-to-rooms
  final GlobalKey _roomsSectionKey = GlobalKey();

  @override
  void initState() {
    super.initState();
    _pageController = PageController(initialPage: 0);
    _pageController.addListener(_onPageChanged);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      _startAutoSlide();
    });
  }

  @override
  void dispose() {
    _timer?.cancel();
    _pageController.removeListener(_onPageChanged);
    _currentPageNotifier.dispose();
    _pageController.dispose();
    super.dispose();
  }

  void _onPageChanged() {
    if (_pageController.hasClients) {
      int currentPage = (_pageController.page?.round() ?? 0);
      final property = ref.read(detailPropertyProvider(widget.id)).value;
      final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(property ?? DetailPropertyModel());
      if (sliderImageProviders.isNotEmpty) {
        _currentPageNotifier.value = currentPage % sliderImageProviders.length;
      }
    }
  }

  void _startAutoSlide() {
    _timer?.cancel();
    final property = ref.read(detailPropertyProvider(widget.id)).value;
    final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(property ?? DetailPropertyModel());

    if (sliderImageProviders.length > 1) {
      _timer = Timer.periodic(const Duration(seconds: 3), (Timer timer) {
        // PENTING: Cek apakah widget masih mounted sebelum melakukan animasi
        if (!mounted) {
          timer.cancel();
          return;
        }

        if (_pageController.hasClients) {
          int nextPage = (_pageController.page!.toInt() + 1);
          _pageController.animateToPage(
            nextPage,
            duration: const Duration(milliseconds: 400),
            curve: Curves.easeIn,
          );
        }
      });
    }
  }

  Future<void> _onRefresh() async {
    AppLogger.d("🔄 DetailHousePage: Memuat ulang data properti...", 'DETAIL-PROPERTY');
    ref.invalidate(roomListProvider(widget.id));
    ref.invalidate(detailPropertyProvider(widget.id));
    AppLogger.d("✅ DetailHousePage: Data properti dimuat ulang.", 'DETAIL-PROPERTY');
  }

  Widget _buildLocationSection(DetailPropertyModel property, TextTheme textTheme) {
    if (property.location == null || property.location!.isEmpty) {
      return const SizedBox.shrink();
    }
    // Dark mode detection for map button
    final isDark = Theme.of(context).brightness == Brightness.dark;

    // Parse location string (format: "latitude,longitude")
    final locationParts = property.location!.split(',');
    if (locationParts.length != 2) {
      return const SizedBox.shrink();
    }

    final double? latitude = double.tryParse(locationParts[0].trim());
    final double? longitude = double.tryParse(locationParts[1].trim());

    if (latitude == null || longitude == null) {
      return const SizedBox.shrink();
    }

    final mapCenter = LatLng(latitude, longitude);

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        // OpenStreetMap with flutter_map
        Container(
          height: 200,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            border: Border.all(color: Colors.grey[300]!),
          ),
          clipBehavior: Clip.hardEdge,
          child: Stack(
            children: [
              FlutterMap(
                options: MapOptions(
                  initialCenter: mapCenter,
                  initialZoom: 15.0,
                  interactionOptions: const InteractionOptions(
                    flags: InteractiveFlag.pinchZoom | InteractiveFlag.drag,
                  ),
                ),
                children: [
                  TileLayer(
                    urlTemplate: 'https://tile.openstreetmap.org/{z}/{x}/{y}.png',
                    userAgentPackageName: 'com.ulinmahoni.apps',
                  ),
                  MarkerLayer(
                    markers: [
                      Marker(
                        point: mapCenter,
                        width: 40,
                        height: 40,
                        child: Icon(
                          Icons.location_on,
                          // Use primaryAdaptive for the map marker icon color
                          color: AppColors.primaryAdaptive(context),
                          size: 40,
                        ),
                      ),
                    ],
                  ),
                ],
              ),
              // View Larger Map button
              Positioned(
                bottom: 8,
                right: 8,
                child: Material(
                  color: isDark ? const Color(0xFF1F2937) : Colors.white,
                  borderRadius: BorderRadius.circular(8),
                  elevation: 2,
                  child: InkWell(
                    onTap: () async {
                      final url = Uri.parse('https://www.google.com/maps?q=$latitude,$longitude');
                      if (await canLaunchUrl(url)) {
                        await launchUrl(url, mode: LaunchMode.externalApplication);
                      }
                    },
                    borderRadius: BorderRadius.circular(8),
                    child: Padding(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      child: Row(
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          // Bright green so it's legible on the map tile background
                          const Icon(Icons.open_in_new, size: 16, color: Color(0xFF34C759)),
                          const SizedBox(width: 4),
                          Text(
                            'View Larger Map',
                            style: textTheme.labelSmall?.copyWith(
                              color: const Color(0xFF34C759),
                              fontWeight: FontWeight.w600,
                            ),
                          ),
                        ],
                      ),
                    ),
                  ),
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 12),
        // Address details
        if (property.address != null && property.address!.isNotEmpty) ...[
          Text(
            'ALAMAT LENGKAP',
            style: textTheme.labelSmall?.copyWith(
              color: Colors.grey[600],
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            property.address!,
            style: textTheme.bodyMedium,
          ),
        ],
        if (property.village != null && property.village!.isNotEmpty) ...[
          const SizedBox(height: 8),
          Text(
            'KELURAHAN',
            style: textTheme.labelSmall?.copyWith(
              color: Colors.grey[600],
              fontWeight: FontWeight.bold,
            ),
          ),
          const SizedBox(height: 4),
          Text(
            property.village!,
            style: textTheme.bodyMedium,
          ),
        ],
        if (property.city != null && property.city!.isNotEmpty) ...[
          const SizedBox(height: 8),
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'KOTA/KABUPATEN',
                      style: textTheme.labelSmall?.copyWith(
                        color: Colors.grey[600],
                        fontWeight: FontWeight.bold,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      property.city!,
                      style: textTheme.bodyMedium,
                    ),
                  ],
                ),
              ),
              if (property.postalCode != null && property.postalCode!.isNotEmpty)
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        'KODE POS',
                        style: textTheme.labelSmall?.copyWith(
                          color: Colors.grey[600],
                          fontWeight: FontWeight.bold,
                        ),
                      ),
                      const SizedBox(height: 4),
                      Text(
                        property.postalCode!,
                        style: textTheme.bodyMedium,
                      ),
                    ],
                  ),
                ),
            ],
          ),
        ],
      ],
    );
  }

  Widget _buildNearbyLocationsSection(List<NearbyLocationModel> locations, TextTheme textTheme) {
    // Group locations by category
    final Map<String, List<NearbyLocationModel>> groupedLocations = {};

    for (var location in locations) {
      final category = location.category ?? 'other';
      if (!groupedLocations.containsKey(category)) {
        groupedLocations[category] = [];
      }
      groupedLocations[category]!.add(location);
    }

    // Category icons and labels
    final Map<String, Map<String, dynamic>> categoryInfo = {
      'transport': {'icon': Icons.directions_bus, 'label': 'TRANSPORTATION'},
      'health': {'icon': Icons.local_hospital, 'label': 'HEALTH'},
      'food_drink': {'icon': Icons.restaurant, 'label': 'FOOD & DRINK'},
      'finance': {'icon': Icons.account_balance, 'label': 'FINANCE'},
      'education': {'icon': Icons.school, 'label': 'EDUCATION'},
      'worship': {'icon': Icons.place, 'label': 'WORSHIP'},
      'shopping': {'icon': Icons.shopping_bag, 'label': 'SHOPPING'},
    };

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: groupedLocations.entries.map((entry) {
        final category = entry.key;
        final items = entry.value;
        final info = categoryInfo[category] ?? {'icon': Icons.place, 'label': category.toUpperCase()};

        return Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              children: [
                // Use primaryAdaptive for the info section icon and label color
                Icon(info['icon'] as IconData, size: 20, color: AppColors.primaryAdaptive(context)),
                const SizedBox(width: 8),
                Text(
                  info['label'] as String,
                  style: textTheme.labelMedium?.copyWith(
                    color: AppColors.primaryAdaptive(context),
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ],
            ),
            const SizedBox(height: 8),
            ...items.map((location) {
              return Padding(
                padding: const EdgeInsets.only(bottom: 8.0, left: 28),
                child: Row(
                  mainAxisAlignment: MainAxisAlignment.spaceBetween,
                  children: [
                    Expanded(
                      child: Text(
                        location.name ?? '',
                        style: textTheme.bodyMedium,
                      ),
                    ),
                    Text(
                      location.distanceText ?? '',
                      style: textTheme.bodySmall?.copyWith(
                        color: Colors.grey[600],
                      ),
                    ),
                  ],
                ),
              );
            }),
            const SizedBox(height: 16),
          ],
        );
      }).toList(),
    );
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    // Dark mode detection for scaffold and content backgrounds
    final isDark = Theme.of(context).brightness == Brightness.dark;
    _timer?.cancel();
    _startAutoSlide();
    final detailProperty = ref.watch(detailPropertyProvider(widget.id));
    final roomList = ref.watch(roomListProvider(widget.id));

    // Smart pricing logic: Monthly → Daily → Contact Customer Service
    double? priceToDisplay;
    String? rentTypeToDisplay;

    roomList.whenData((rooms) {
      if (rooms.isEmpty) {
        // No rooms available - reset all values
        priceToDisplay = null;
        rentTypeToDisplay = null;
      } else {
        // Find cheapest monthly price
        double? cheapestMonthly;
        double? cheapestDaily;

        for (var room in rooms) {
          // Check monthly price — skip 0 so daily-only properties fall back correctly
          if (room.priceOriginalMonthly != null && room.priceOriginalMonthly!.isNotEmpty) {
            try {
              final monthlyPrice = double.parse(room.priceOriginalMonthly!);
              if (monthlyPrice > 0 && (cheapestMonthly == null || monthlyPrice < cheapestMonthly)) {
                cheapestMonthly = monthlyPrice;
              }
            } catch (e) {
              AppLogger.w('Failed to parse monthly price: ${room.priceOriginalMonthly}', 'DETAIL-PROPERTY');
            }
          }

          // Check daily price — skip 0 as well
          if (room.priceOriginalDaily != null && room.priceOriginalDaily!.isNotEmpty) {
            try {
              final dailyPrice = double.parse(room.priceOriginalDaily!);
              if (dailyPrice > 0 && (cheapestDaily == null || dailyPrice < cheapestDaily)) {
                cheapestDaily = dailyPrice;
              }
            } catch (e) {
              AppLogger.w('Failed to parse daily price: ${room.priceOriginalDaily}', 'DETAIL-PROPERTY');
            }
          }
        }

        // Priority: Monthly → Daily → Contact Customer Service
        if (cheapestMonthly != null) {
          priceToDisplay = cheapestMonthly;
          rentTypeToDisplay = localizations.bottomBarPerMonth;
        } else if (cheapestDaily != null) {
          priceToDisplay = cheapestDaily;
          rentTypeToDisplay = localizations.bottomBarPerDay;
        } else {
          // No valid prices found - show contact customer service message
          priceToDisplay = null;
          rentTypeToDisplay = null;
        }
      }
    });

    return MainLayout(
      currentIndex: 0,
      showNavBar: false,
      showBottomNav: false,
      showContactBar: true,
      // Show "Pesan Sekarang" (Book Now) button in the bottom bar
      bottomcontactbar_pesansekarang: true,
      bottomcontactbar_buttonpressed: () {
        // Scroll to rooms section so user can pick a room to book
        final ctx = _roomsSectionKey.currentContext;
        if (ctx != null) {
          Scrollable.ensureVisible(ctx, duration: const Duration(milliseconds: 400), curve: Curves.easeInOut);
        }
      },
      bottomcontactbar_price: priceToDisplay != null
          ? formatCurrency(priceToDisplay)
          : localizations.bottomBarContactCustomerService,
      bottomcontactbar_renttype: priceToDisplay != null ? (rentTypeToDisplay ?? "") : null,
      bottomcontactbar_pricelabel: priceToDisplay != null
          ? localizations.bottomBarStartingFrom
          : localizations.bottomBarRoomUnavailable,
      child: detailProperty.when(
        data: (property) {
          _processAndLogAllImages(property);

          WidgetsBinding.instance.addPostFrameCallback((_) {
            _onPageChanged();
          });

          return Scaffold(
            backgroundColor: Colors.transparent,
            extendBodyBehindAppBar: true,
            appBar: CustomAppBar(
              title: localizations.detailPropertyPageTitle,
            ),
            body: SafeArea(
              top: false,
              bottom: false,
              child: RefreshIndicator(
                onRefresh: _onRefresh,
                child: _buildDetailHouseContent(context, property),
              ),
            ),
          );
        },
        loading: () => Scaffold(
          backgroundColor: isDark ? const Color(0xFF111827) : Colors.white,
          appBar: CustomAppBar(
            title: localizations.detailPropertyPageTitle,
          ),
          body: SafeArea(
            child: Skeletonizer(
              enabled: true,
              child: _buildSkeletonContent(context),
            ),
          ),
        ),
        error: (error, stackTrace) => Center(
          child: Text(
            '${localizations.detailPropertyError}: ${error.toString()}', 
            style: Theme.of(context).textTheme.bodyLarge?.copyWith(color: Colors.red),
          ),
        ),
      ),
    );
  }

  void _processAndLogAllImages(DetailPropertyModel property) {
    AppLogger.d("\n--- Memproses status gambar properti (menggunakan List<ImageModel>) ---", 'DETAIL-PROPERTY');

    if (property.images == null || property.images!.isEmpty) {
      AppLogger.d("Tidak ada gambar yang ditemukan.", 'DETAIL-PROPERTY');
      AppLogger.d("--- Selesai memproses status gambar properti ---\n", 'DETAIL-PROPERTY');
      return;
    }

    for (int i = 0; i < property.images!.length; i++) {
      final imageModel = property.images![i];
      final String? imageUrl = imageModel.imageData;
      final int imageId = imageModel.id ?? i + 1;

      String status = "null";

      if (imageUrl != null && imageUrl.isNotEmpty) {
        if (imageUrl.startsWith('data:image')) {
          try {
            String base64String = imageUrl.split(',').last.trim().replaceAll(RegExp(r'\s'), '');
            base64Decode(base64String);
            status = "berhasil (Base64 data:image)";
          } catch (e) {
            status = "gagal decode (Base64 data:image)";
          }
        } else if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
          status = "berhasil (URL)";
        } else if (imageUrl.startsWith('/9j/') || imageUrl.startsWith('iVBORw0KGgoAAAANSUhEU') || imageUrl.length > 100) {
          try {
            String rawBase64Data = imageUrl.trim().replaceAll(RegExp(r'\s'), '');
            base64Decode(rawBase64Data);
            status = "berhasil (raw Base64)";
          } catch (e) {
            status = "gagal decode (raw Base64)";
          }
        } else {
          status = "format tidak dikenal";
        }
      }
      AppLogger.d('Gambar ID $imageId: $status', 'DETAIL-PROPERTY');
    }
    AppLogger.d("--- Selesai memproses status gambar properti ---\n", 'DETAIL-PROPERTY');
  }

  List<ImageProvider> _getValidImageProvidersForSlider(DetailPropertyModel? property) {
    List<ImageProvider> imageProviders = [];

    if (property == null) {
      imageProviders.add(const AssetImage(AppImage.defaultPropertyImage));
      return imageProviders;
    }

    bool thumbnailAdded = false;

    // Prioritas 1: Gunakan thumbnail dari root level jika ada
    if (property.thumbnail != null && property.thumbnail!.isNotEmpty) {
      ImageProvider? provider;
      final String thumbnailUrl = property.thumbnail!;

      if (thumbnailUrl.startsWith('data:image')) {
        try {
          String base64String = thumbnailUrl.split(',').last.trim().replaceAll(RegExp(r'\s'), '');
          provider = MemoryImage(base64Decode(base64String));
        } catch (e) {}
      } else if (thumbnailUrl.startsWith('http://') || thumbnailUrl.startsWith('https://')) {
        provider = NetworkImage(thumbnailUrl);
      } else if (thumbnailUrl.startsWith('/9j/') || thumbnailUrl.startsWith('iVBORw0KGgoAAAANSUhEU') || thumbnailUrl.length > 100) {
        try {
          String rawBase64Data = thumbnailUrl.trim().replaceAll(RegExp(r'\s'), '');
          provider = MemoryImage(base64Decode(rawBase64Data));
        } catch (e) {}
      }

      if (provider != null) {
        imageProviders.add(provider);
        thumbnailAdded = true;
      }
    }

    // Tambahkan gambar dari array images (skip first if thumbnail was added to avoid duplicate)
    if (property.images != null && property.images!.isNotEmpty) {
      int startIndex = thumbnailAdded ? 1 : 0; // Skip first image if thumbnail already added
      for (int i = startIndex; i < property.images!.length; i++) {
        final imageModel = property.images![i];
        final String? imageUrl = imageModel.imageData;
        if (imageUrl != null && imageUrl.isNotEmpty) {
          ImageProvider? provider;
          if (imageUrl.startsWith('data:image')) {
            try {
              String base64String = imageUrl.split(',').last.trim().replaceAll(RegExp(r'\s'), '');
              provider = MemoryImage(base64Decode(base64String));
            } catch (e) {}
          } else if (imageUrl.startsWith('http://') || imageUrl.startsWith('https://')) {
            provider = NetworkImage(imageUrl);
          } else if (imageUrl.startsWith('/9j/') || imageUrl.startsWith('iVBORw0KGgoAAAANSUhEU') || imageUrl.length > 100) {
            try {
              String rawBase64Data = imageUrl.trim().replaceAll(RegExp(r'\s'), '');
              provider = MemoryImage(base64Decode(rawBase64Data));
            } catch (e) {}
          }
          if (provider != null) {
            imageProviders.add(provider);
          }
        }
      }
    }

    if (imageProviders.isEmpty) {
      imageProviders.add(const AssetImage(AppImage.defaultPropertyImage));
    }

    return imageProviders;
  }

  Widget _buildPageIndicator(DetailPropertyModel property) {
    final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(property);

    if (sliderImageProviders.length <= 1) {
      AppLogger.d("INFO: Hanya ada 0 atau 1 gambar, indikator tidak ditampilkan.", 'DETAIL-PROPERTY');
      return const SizedBox.shrink();
    }
    AppLogger.d("INFO: Ada ${sliderImageProviders.length} gambar, indikator ditampilkan.", 'DETAIL-PROPERTY');
    return Positioned(
      top: 20,
      left: 0,
      right: 0,
      child: Align(
        alignment: Alignment.center,
        child: ValueListenableBuilder<int>(
          valueListenable: _currentPageNotifier,
          builder: (context, currentPage, child) {
            return Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: List.generate(
                sliderImageProviders.length,
                    (index) => GestureDetector(
                  onTap: () {
                    _pageController.animateToPage(
                      index,
                      duration: const Duration(milliseconds: 300),
                      curve: Curves.easeInOut,
                    );
                  },
                  child: Container(
                    width: 6.0,
                    height: 6.0,
                    margin: const EdgeInsets.symmetric(horizontal: 4.0),
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      // Use primaryAdaptive for the active page indicator dot
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

  Widget _buildSkeletonContent(BuildContext context) {
    final screenHeight = MediaQuery.of(context).size.height;
    final screenWidth = MediaQuery.of(context).size.width;
    final double contentMarginHorizontal = screenWidth * 0.04;
    final double contentPaddingAll = screenWidth * 0.04;

    return SingleChildScrollView(
      physics: const NeverScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Skeleton Image Slider
          Container(
            color: Colors.grey[300],
            width: double.infinity,
            height: screenHeight * 0.45,
          ),
          // Skeleton Content Card
          Transform.translate(
            offset: const Offset(0, -50),
            child: Container(
              margin: EdgeInsets.symmetric(horizontal: contentMarginHorizontal),
              padding: EdgeInsets.all(contentPaddingAll),
              decoration: BoxDecoration(
                color: Colors.white,
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
                  // Property Name
                  Container(
                    width: screenWidth * 0.7,
                    height: 28,
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 8),
                  // Property Tag
                  Container(
                    width: screenWidth * 0.4,
                    height: 20,
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 16),
                  // Description
                  Container(
                    width: double.infinity,
                    height: 60,
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 16),
                  // Address
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(Icons.location_on, color: Colors.grey[400]),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Container(
                          height: 16,
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 12),
                  // Distance
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Icon(Icons.directions_walk, color: Colors.grey[400]),
                      const SizedBox(width: 4),
                      Expanded(
                        child: Container(
                          height: 16,
                          decoration: BoxDecoration(
                            color: Colors.grey[300],
                            borderRadius: BorderRadius.circular(4),
                          ),
                        ),
                      ),
                    ],
                  ),
                  const SizedBox(height: 16),
                  // Facilities Title
                  Container(
                    width: screenWidth * 0.5,
                    height: 24,
                    decoration: BoxDecoration(
                      color: Colors.grey[300],
                      borderRadius: BorderRadius.circular(4),
                    ),
                  ),
                  const SizedBox(height: 12),
                  // Facilities Grid
                  Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: List.generate(
                      6,
                      (index) => Container(
                        width: screenWidth * 0.25,
                        height: 30,
                        decoration: BoxDecoration(
                          color: Colors.grey[300],
                          borderRadius: BorderRadius.circular(4),
                        ),
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
          // Rooms Section Skeleton
          Padding(
            padding: EdgeInsets.symmetric(horizontal: contentMarginHorizontal),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Container(
                  width: screenWidth * 0.4,
                  height: 24,
                  decoration: BoxDecoration(
                    color: Colors.grey[300],
                    borderRadius: BorderRadius.circular(4),
                  ),
                ),
                const SizedBox(height: 16),
                // Room Cards
                ...List.generate(
                  2,
                  (index) => Padding(
                    padding: const EdgeInsets.only(bottom: 16.0),
                    child: Container(
                      height: 120,
                      decoration: BoxDecoration(
                        color: Colors.grey[300],
                        borderRadius: BorderRadius.circular(12),
                      ),
                    ),
                  ),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildDetailHouseContent(BuildContext context, DetailPropertyModel property) {
    final localizations = AppLocalizations.of(context)!;
    final textTheme = Theme.of(context).textTheme;
    final screenHeight = MediaQuery.of(context).size.height;
    final screenWidth = MediaQuery.of(context).size.width;
    // Dark mode detection for content card colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final double contentMarginHorizontal = screenWidth * 0.04;
    final double contentPaddingAll = screenWidth * 0.04;

    final List<ImageProvider> sliderImageProviders = _getValidImageProvidersForSlider(property);

    // Check if we have facilities (new format with icons or old format with strings)
    final bool hasNewFacilities = (property.generalFacilities?.isNotEmpty ?? false) ||
        (property.securityFacilities?.isNotEmpty ?? false) ||
        (property.amenitiesFacilities?.isNotEmpty ?? false);

    final bool hasOldFacilities = (property.general?.isNotEmpty ?? false) ||
        (property.security?.isNotEmpty ?? false) ||
        (property.amenities?.isNotEmpty ?? false);

    return SingleChildScrollView(
      physics: const AlwaysScrollableScrollPhysics(),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          SizedBox(
            height: screenHeight * 0.45,
            child: Stack(
              children: [
                PageView.builder(
                  controller: _pageController,
                  itemCount: sliderImageProviders.length > 1 ? null : 1,
                  itemBuilder: (context, index) {
                    final int actualIndex = sliderImageProviders.length > 1 ? index % sliderImageProviders.length : 0;
                    return GestureDetector(
                      onTap: () {
                        showImageViewerPopup(
                          context,
                          sliderImageProviders,
                          initialIndex: actualIndex,
                        );
                      },
                      child: Image(
                        image: sliderImageProviders[actualIndex],
                        fit: BoxFit.cover,
                        alignment: Alignment.center,
                        errorBuilder: (context, error, stackTrace) {
                          return Image.asset(
                            AppImage.defaultPropertyImage,
                            fit: BoxFit.cover,
                            alignment: Alignment.center,
                          );
                        },
                      ),
                    );
                  },
                ),
                _buildPageIndicator(property),
              ],
            ),
          ),
          Transform.translate(
            offset: const Offset(0, -50),
            child: Container(
              margin: EdgeInsets.symmetric(horizontal: contentMarginHorizontal),
              padding: EdgeInsets.all(contentPaddingAll),
              decoration: BoxDecoration(
                // 10% transparent (90% opacity) so leafy background shows through
                color: isDark
                    ? const Color(0xFF1F2937).withValues(alpha: 0.90)
                    : Colors.white.withValues(alpha: 0.90),
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
                  if (property.name != null && property.name!.isNotEmpty)
                    Text(
                      property.name!,
                      style: textTheme.headlineMedium?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: isDark ? Colors.white : Colors.black,
                      ),
                      softWrap: true,
                      overflow: TextOverflow.visible,
                    )
                  else
                    Text(localizations.detailPropertyNameNotAvailable),

                  if (property.tags != null && property.tags!.isNotEmpty)
                    Text(
                      property.tags!,
                      style: textTheme.titleMedium?.copyWith(
                        color: isDark ? Colors.grey[300] : Colors.black,
                      ),
                      softWrap: true,
                      overflow: TextOverflow.visible,
                    )
                  else
                    Text(localizations.detailPropertyTagNotAvailable), 

                  const SizedBox(height: 8),

                  // Multi-language: resolve property description by current locale with fallback chain
                  Builder(builder: (context) {
                    final locale = Localizations.localeOf(context).languageCode;
                    final description = property.descriptionParsed?[locale]
                        ?? property.descriptionParsed?['en']
                        ?? property.descriptionParsed?['id']
                        ?? property.description
                        ?? '';
                    if (description.isNotEmpty) {
                      return HtmlDescription(
                        html: description,
                        textStyle: textTheme.bodyMedium,
                      );
                    }
                    return const SizedBox.shrink();
                  }),

                  // Gender
                  if (property.gender != null && property.gender!.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    Row(
                      children: [
                        Icon(
                          property.gender!.toLowerCase() == 'male'
                              ? Icons.male
                              : property.gender!.toLowerCase() == 'female'
                                  ? Icons.female
                                  : Icons.group,
                          color: property.gender!.toLowerCase() == 'male'
                              ? Colors.blue
                              : property.gender!.toLowerCase() == 'female'
                                  ? Colors.pink
                                  : Colors.purple,
                          size: 20,
                        ),
                        const SizedBox(width: 8),
                        Text(
                          property.gender!.toLowerCase() == 'male'
                              ? localizations.genderMale
                              : property.gender!.toLowerCase() == 'female'
                                  ? localizations.genderFemale
                                  : localizations.genderMixed,
                          style: TextStyle(
                            color: property.gender!.toLowerCase() == 'male'
                                ? Colors.blue.shade700
                                : property.gender!.toLowerCase() == 'female'
                                    ? Colors.pink.shade700
                                    : Colors.purple.shade700,
                            fontSize: 15,
                            fontWeight: FontWeight.w600,
                          ),
                        ),
                      ],
                    ),
                  ],

                  const SizedBox(height: 16),
                  if (property.distance != null && property.distance!.isNotEmpty)
                    Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Use primaryAdaptive for the walking distance icon color
                        Icon(Icons.directions_walk,
                            color: AppColors.primaryAdaptive(context),
                            size: (textTheme.bodyLarge?.fontSize ?? 28) * 1.5),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            property.distance!,
                            style: textTheme.bodyMedium,
                            softWrap: true,
                            overflow: TextOverflow.visible,
                          ),
                        ),
                      ],
                    )
                  else
                    const SizedBox.shrink(),
                  property.level_count != null && property.level_count! > 0
                      ? Padding(
                    padding: const EdgeInsets.only(top: 8.0),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        // Use primaryAdaptive for the floor count icon color
                        Icon(Icons.layers,
                            color: AppColors.primaryAdaptive(context),
                            size: (textTheme.bodyLarge?.fontSize ?? 28) * 1.5),
                        const SizedBox(width: 4),
                        Expanded(
                          child: Text(
                            localizations.detailPropertyFloorCount(property.level_count!), 
                            style: textTheme.bodyMedium,
                            softWrap: true,
                            overflow: TextOverflow.visible,
                          ),
                        ),
                      ],
                    ),
                  )
                      : const SizedBox.shrink(),

                  // Lokasi Section
                  if (property.location != null && property.location!.isNotEmpty) ...[
                    const SizedBox(height: 12),
                    Text(
                      localizations.propertyDetailLocation,
                      style: textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: isDark ? Colors.white : Colors.black,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildLocationSection(property, textTheme),
                  ],

                  const SizedBox(height: 24),
                  Text(
                    localizations.detailPropertyFacilitiesTitle,
                    style: textTheme.titleLarge?.copyWith(
                      fontWeight: FontWeight.bold,
                      color: isDark ? Colors.white : Colors.black,
                    ),
                  ),
                  // Use new format with icons if available, otherwise fallback to old format
                  if (hasNewFacilities)
                    PropertyFacilitiesIconifyGrid(
                      general: property.generalFacilities,
                      security: property.securityFacilities,
                      amenities: property.amenitiesFacilities,
                    )
                  else if (hasOldFacilities)
                    PropertyFacilitiesTextGrid(
                      general: property.general,
                      security: property.security,
                      amenities: property.amenities,
                    )
                  else
                    Text(
                      localizations.detailPropertyNoFacilities,
                      style: textTheme.bodyMedium?.copyWith(
                        fontStyle: FontStyle.italic,
                        color: Colors.grey[600],
                      ),
                    ),

                  // Lokasi Terdekat Section
                  if (property.nearbyLocations != null && property.nearbyLocations!.isNotEmpty) ...[
                    const SizedBox(height: 24),
                    Text(
                      localizations.propertyDetailNearbyLocations,
                      style: textTheme.titleLarge?.copyWith(
                        fontWeight: FontWeight.bold,
                        color: isDark ? Colors.white : Colors.black,
                      ),
                    ),
                    const SizedBox(height: 12),
                    _buildNearbyLocationsSection(property.nearbyLocations!, textTheme),
                  ],
                ],
              ),
            ),
          ),
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 0, vertical: 0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                RoomTypeSection(
                  key: _roomsSectionKey,
                  propertyData: property,
                ),
              ],
            ),
          ),
          // "Properti Lainnya" button below the rooms section
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 16.0),
            child: SizedBox(
              width: double.infinity,
              child: OutlinedButton.icon(
                onPressed: () {
                  context.push('/browse-all');
                },
                icon: const Icon(Icons.house, color: Colors.white),
                label: Text(
                  AppLocalizations.of(context)!.contactBarOtherPropertiesButton,
                  style: const TextStyle(
                    color: Colors.white,
                    fontWeight: FontWeight.bold,
                    fontSize: 14,
                  ),
                ),
                style: OutlinedButton.styleFrom(
                  side: BorderSide(
                    color: AppColors.primaryAdaptive(context),
                    width: 1.5,
                  ),
                  backgroundColor: AppColors.primaryAdaptive(context).withValues(alpha: 0.2),
                  padding: const EdgeInsets.symmetric(vertical: 14),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(12),
                  ),
                ),
              ),
            ),
          ),
          // Extra padding so content can scroll behind the glass bottom bar
          const SizedBox(height: 80),
        ],
      ),
    );
  }
}