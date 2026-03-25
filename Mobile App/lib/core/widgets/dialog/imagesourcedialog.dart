import 'package:flutter/material.dart';
import '../../constants/app_asset_constants.dart';
import '../../constants/appcolor_constants.dart';

/// Shows a dialog for selecting image source (Camera or Gallery)
/// Returns ImageSource? - Camera or Gallery, or null if dismissed
Future<ImageSourceOption?> showImageSourceDialog(
  BuildContext context, {
  String? title,
  String? message,
  String cameraButtonText = 'Camera',
  String galleryButtonText = 'Gallery',
  String cancelButtonText = 'Cancel',
  bool barrierDismissible = true,
}) {
  final isDark = Theme.of(context).brightness == Brightness.dark;

  return showDialog<ImageSourceOption>(
    context: context,
    barrierDismissible: barrierDismissible,
    builder: (ctx) => AlertDialog(
      backgroundColor: isDark ? const Color(0xFF1F2937) : Colors.white,
      title: title != null
          ? Text(
              title,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: isDark ? Colors.white : Colors.black87,
              ),
            )
          : null,
      contentPadding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          // Logo
          Image.asset(
            AppImage.logo,
            width: 80,
            height: 80,
            errorBuilder: (context, error, stackTrace) {
              return const Icon(
                Icons.image_outlined,
                color: AppColors.primaryColor,
                size: 80,
              );
            },
          ),
          if (message != null) ...[
            const SizedBox(height: 16),
            Text(
              message,
              style: TextStyle(
                fontSize: 14,
                color: isDark ? Colors.white70 : Colors.black87,
              ),
              textAlign: TextAlign.center,
            ),
          ],
          const SizedBox(height: 24),
          // Camera Button
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () {
                Navigator.of(ctx).pop(ImageSourceOption.camera);
              },
              icon: const Icon(Icons.camera_alt, color: Colors.white),
              label: Text(cameraButtonText),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ),
          const SizedBox(height: 12),
          // Gallery Button
          SizedBox(
            width: double.infinity,
            child: ElevatedButton.icon(
              onPressed: () {
                Navigator.of(ctx).pop(ImageSourceOption.gallery);
              },
              icon: const Icon(Icons.photo_library, color: Colors.white),
              label: Text(galleryButtonText),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.secondaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(vertical: 14),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(8),
                ),
              ),
            ),
          ),
        ],
      ),
      actionsAlignment: MainAxisAlignment.center,
      actions: [
        TextButton(
          onPressed: () {
            Navigator.of(ctx).pop(null);
          },
          child: Text(
            cancelButtonText,
            style: TextStyle(
              color: isDark ? Colors.grey[400] : Colors.black54,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    ),
  );
}

/// Enum for image source options
enum ImageSourceOption {
  camera,
  gallery,
}
