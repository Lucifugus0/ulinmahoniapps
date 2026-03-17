import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';

/// Widget untuk menampilkan section "Syarat & Ketentuan"
/// Menampilkan 4 syarat ketentuan promo dengan checkmark icons
class PromoSyaratKetentuanSection extends StatelessWidget {
  const PromoSyaratKetentuanSection({super.key});

  // Hardcoded syarat ketentuan (generic untuk semua promo)
  static const List<String> _syaratKetentuan = [
    'Syarat dan ketentuan berlaku',
    'Tidak dapat digabung dengan promo lain',
    'Periode promo terbatas',
    'Hanya berlaku untuk pengguna secara terbatas',
  ];

  @override
  Widget build(BuildContext context) {
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
            'Syarat & Ketentuan',
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 16),

          // List of terms
          ...List.generate(_syaratKetentuan.length, (index) {
            return Padding(
              padding: const EdgeInsets.only(bottom: 12),
              child: Row(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  // Checkmark icon
                  const Icon(
                    Icons.check,
                    color: AppColors.primaryColor,
                    size: 20,
                  ),
                  const SizedBox(width: 12),

                  // Term text
                  Expanded(
                    child: Text(
                      _syaratKetentuan[index],
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
