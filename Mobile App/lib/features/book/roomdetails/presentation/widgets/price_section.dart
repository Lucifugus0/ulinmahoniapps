import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/app_asset_constants.dart';
import '../../../../../core/widgets/dialog/notificationdialog.dart';

Widget buildPriceSection(
    BuildContext context,
    List<Map<String, String>> prices,
    String checkIn,
    String checkOut,
    ) {
  return IntrinsicHeight(
    child: Row(
      crossAxisAlignment: CrossAxisAlignment.stretch,
      children: prices.map((price) {
        return Expanded(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 6),
            child: GestureDetector(
              onTap: () {
                if (checkIn.isEmpty || checkOut.isEmpty) {
                  showNotificationDialog(
                    context,
                    'Silakan pilih tanggal check-in dan check-out',
                    defaultIcon: Icons.error_outline,
                    iconColor: AppColors.secondaryColor,
                  );
                  return;
                }
                context.push('/payment', extra: {
                  'roomData': {
                    'name': price['label'] == 'Daily' ? 'Alpha Room' : 'Beta Room',
                    'image': AppImage.defaultRoomImage,
                    'checkIn': checkIn,
                    'checkOut': checkOut,
                    'location': price['location'] ?? '',
                  },
                  'itemDetails': [
                    {
                      'name': price['label'] ?? '',
                      'price': price['price'] ?? '',
                    }
                  ],
                });
              },
              child: _buildPriceContent(
                label: price['label'] ?? '',
                image: AppImage.defaultRoomImage,
                price: price['price'] ?? '',
                originalPrice: price['originalPrice'] ?? '',
                discount: price['discount'] ?? '',
                location: price['location'] ?? '',
                paymentOption: price['paymentOption'] ?? '',
                context: context,
              ),
            ),
          ),
        );
      }).toList(),
    ),
  );
}

Widget _buildPriceContent({
  required String label,
  required String image,
  required String price,
  required String originalPrice,
  required String discount,
  required String location,
  required String paymentOption,
  required BuildContext context,
}) {
  return Column(
    crossAxisAlignment: CrossAxisAlignment.start,
    children: [
      Stack(
        children: [
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: Image.asset(
              image,
              width: double.infinity, 
              height: 180,
              fit: BoxFit.cover,
            ),
          ),
          
          Positioned(
            top: 8,
            left: 8,
            child: Container(
              padding: const EdgeInsets.all(4),
              decoration: BoxDecoration(
                color: Colors.white,
                borderRadius: BorderRadius.circular(4),
              ),
              child: Image.asset(
                AppImage.logo,
                width: 24,
                height: 24,
              ),
            ),
          ),
          
          Positioned(
            top: 8,
            right: 8,
            child: Container(
              padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
              decoration: BoxDecoration(
                color: Colors.black.withOpacity(0.7),
                borderRadius: BorderRadius.circular(4),
              ),
              child: Text(
                label,
                style: const TextStyle(color: Colors.white, fontSize: 12),
              ),
            ),
          ),
        ],
      ),
      const SizedBox(height: 8),
      Padding(
        padding: const EdgeInsets.symmetric(horizontal: 4.0),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            
            Text(
              originalPrice,
              style: const TextStyle(
                fontSize: 12,
                color: Colors.grey,
                decoration: TextDecoration.lineThrough,
              ),
            ),
            
            Text(
              price,
              style: const TextStyle(
                fontSize: 16, 
                fontWeight: FontWeight.bold,
              ),
            ),
            const SizedBox(height: 4),
            
            Text(
              discount,
              style: const TextStyle(
                fontSize: 12,
                color: AppColors.primaryColor,
                fontWeight: FontWeight.w500,
              ),
            ),
            const SizedBox(height: 4),
            
            Text(
              '$location ',
              style: const TextStyle(fontSize: 12),
            ),
            Text(
              paymentOption,
              style: const TextStyle(fontSize: 12, color: Colors.grey),
            ),
          ],
        ),
      ),
    ],
  );
}