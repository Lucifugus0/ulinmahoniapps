import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../model/promo_banner_model.dart';

/// Widget untuk menampilkan deskripsi promo
/// Menampilkan section title "Deskripsi Promo" dan description text
class PromoDetailDescription extends StatelessWidget {
  final PromoBannerModel banner;

  const PromoDetailDescription({
    super.key,
    required this.banner,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(16),
      color: AppColors.backgroundColor, // white
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Section title
          const Text(
            'Deskripsi Promo',
            style: TextStyle(
              fontSize: 18,
              fontWeight: AppFontWeight.bold,
              color: AppColors.fontcolor,
            ),
          ),
          const SizedBox(height: 12),

          // Description text
          Text(
            banner.description != null && banner.description!.isNotEmpty
                ? banner.description!
                : 'Tidak ada deskripsi',
            style: const TextStyle(
              fontSize: 14,
              color: AppColors.fontcolor,
              height: 1.5, // line height for readability
            ),
          ),
        ],
      ),
    );
  }
}
