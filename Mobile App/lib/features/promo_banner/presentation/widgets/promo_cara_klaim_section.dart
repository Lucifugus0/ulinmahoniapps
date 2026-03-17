import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../model/promo_banner_model.dart';

/// Widget untuk menampilkan section "Cara Klaim"
/// Menampilkan langkah cara klaim promo dengan numbered list
/// Menggunakan data dari API jika tersedia, fallback ke hardcoded steps
class PromoCaraKlaimSection extends StatelessWidget {
  final PromoBannerModel banner;

  const PromoCaraKlaimSection({
    super.key,
    required this.banner,
  });

  // Default cara klaim steps (fallback jika API tidak mengembalikan data)
  static const List<String> _defaultCaraKlaim = [
    'Pilih properti yang diinginkan',
    'Lalu Pilih Kamar yang diinginkan',
    'Masukkan Kode Voucher saat pemesanan',
    'Lakukan Pemesanan',
    'Nikmati promo/diskon yang didapatkan',
  ];

  @override
  Widget build(BuildContext context) {
    // Use API data if available, otherwise use default steps
    final List<String> caraKlaim =
        (banner.howToClaim != null && banner.howToClaim!.isNotEmpty)
            ? banner.howToClaim!
            : _defaultCaraKlaim;

    return Container(
      width: double.infinity,
      margin: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: AppColors.backgroundColor, // white
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: Colors.grey[300]!,
          width: 1,
        ),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section header
          const Text(
            'Cara Klaim',
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 16),

          // List of steps
          ...List.generate(caraKlaim.length, (index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Number indicator
                  Container(
                    width: 24,
                    height: 24,
                    decoration: BoxDecoration(
                      color: AppColors.primaryColor,
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Center(
                      child: Text(
                        '${index + 1}',
                        style: const TextStyle(
                          color: Colors.white,
                          fontSize: 12,
                          fontWeight: AppFontWeight.semiBold,
                        ),
                      ),
                    ),
                  ),
                  const SizedBox(width: 12),

                  // Step text
                  Expanded(
                    child: Text(
                      caraKlaim[index],
                      style: const TextStyle(
                        fontSize: 14,
                        color: AppColors.fontcolor,
                        height: 1.4,
                      ),
                    ),
                  ),
                ],
              ),
            );
          }),
        ],
      ),
    );
  }
}
