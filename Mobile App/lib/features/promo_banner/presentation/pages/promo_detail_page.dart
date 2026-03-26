import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/widgets/appbar.dart';
import '../../provider/promo_banner_provider.dart';
import '../widgets/promo_detail_banner.dart';
import '../widgets/promo_detail_description.dart';
import '../widgets/promo_cara_klaim_section.dart';
import '../widgets/promo_syarat_ketentuan_section.dart';

/// Halaman detail promo banner
/// Menampilkan banner image, deskripsi, cara klaim, dan syarat & ketentuan
class PromoDetailPage extends ConsumerWidget {
  final int bannerId;

  const PromoDetailPage({
    super.key,
    required this.bannerId,
  });

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // Watch banner data from provider
    final bannerAsyncValue = ref.watch(bannerByIdProvider(bannerId));

    return Scaffold(
      backgroundColor: AppColors.backgroundColor,
      appBar: const CustomAppBar(
        title: 'Detail Promo',
        showBackButton: true,
      ),
      body: bannerAsyncValue.when(
        // Loading state
        loading: () => _buildLoadingState(),

        // Error state
        error: (error, stackTrace) => _buildErrorState(context, ref, error),

        // Success state
        data: (banner) => RefreshIndicator(
          onRefresh: () async {
            // Invalidate provider to trigger a fresh fetch
            ref.invalidate(bannerByIdProvider(bannerId));

            // Wait for the provider to complete the refresh
            await ref.read(bannerByIdProvider(bannerId).future);
          },
          child: SingleChildScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                // Banner image with title overlay
                PromoDetailBanner(banner: banner),

                // Description section
                PromoDetailDescription(banner: banner),

                // Cara Klaim section
                PromoCaraKlaimSection(banner: banner),

                // Syarat & Ketentuan section
                const PromoSyaratKetentuanSection(),

                // Bottom spacing (extra padding for bottom navbar)
                const SizedBox(height: 120),
              ],
            ),
          ),
        ),
      ),
    );
  }

  /// Build loading state with skeletonizer
  Widget _buildLoadingState() {
    return Skeletonizer(
      enabled: true,
      child: SingleChildScrollView(
        child: Column(
          children: [
            // Banner skeleton
            Container(
              width: double.infinity,
              height: 250,
              color: Colors.grey[300],
            ),
            // Description skeleton
            Padding(
              padding: const EdgeInsets.all(16),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Container(
                    width: 150,
                    height: 20,
                    color: Colors.grey[300],
                  ),
                  const SizedBox(height: 12),
                  Container(
                    width: double.infinity,
                    height: 60,
                    color: Colors.grey[300],
                  ),
                ],
              ),
            ),
            // Section skeletons
            Container(
              margin: const EdgeInsets.all(16),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(12),
              ),
              height: 200,
            ),
            Container(
              margin: const EdgeInsets.all(16),
              padding: const EdgeInsets.all(16),
              decoration: BoxDecoration(
                color: Colors.grey[300],
                borderRadius: BorderRadius.circular(12),
              ),
              height: 150,
            ),
          ],
        ),
      ),
    );
  }

  /// Build error state with retry button
  Widget _buildErrorState(BuildContext context, WidgetRef ref, Object error) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Error icon
            Icon(
              Icons.error_outline,
              size: 64,
              color: Colors.red[400],
            ),
            const SizedBox(height: 16),

            // Error message
            Text(
              'Gagal memuat detail promo',
              style: const TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.bold,
                color: AppColors.fontcolor,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 8),

            // Error details
            Text(
              error.toString(),
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey[600],
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),

            // Retry button
            ElevatedButton.icon(
              onPressed: () {
                // Refresh provider to retry
                ref.invalidate(bannerByIdProvider(bannerId));
              },
              icon: const Icon(Icons.refresh),
              label: const Text('Coba Lagi'),
              style: ElevatedButton.styleFrom(
                // Use primaryAdaptive for dark/light mode compatibility
                backgroundColor: AppColors.primaryAdaptive(context),
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(
                  horizontal: 24,
                  vertical: 12,
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
