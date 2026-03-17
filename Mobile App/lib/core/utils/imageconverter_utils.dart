import 'dart:convert'; // Untuk base64Decode
import 'package:flutter/material.dart'; // Untuk ImageProvider, MemoryImage, AssetImage, Image, Container, CircularProgressIndicator
import 'package:flutter_svg/flutter_svg.dart'; // Untuk SvgPicture.network, SvgPicture.asset
import '../constants/app_asset_constants.dart'; // Asumsi AppImage ada dan berisi defaultRoomImage

class ImageConverter {
  static ImageProvider convertStringToImageProvider(String? imageString) {
    if (imageString == null || imageString.isEmpty) {
      return const AssetImage(AppImage.defaultRoomImage); // Placeholder default
    }

    // Periksa apakah string adalah Base64 (dengan atau tanpa prefix data:image)
    if (imageString.startsWith('data:image') ||
        imageString.startsWith('/9j/') || // Umum untuk JPEG Base64 tanpa prefix
        imageString.startsWith('iVBORw0KGgoAAAANSUhEU')) { // Umum untuk PNG Base64 tanpa prefix
      try {
        String base64Encoded = imageString;
        if (imageString.startsWith('data:image')) {
          base64Encoded = imageString.split(',').last;
        }
        base64Encoded = base64Encoded.trim().replaceAll(RegExp(r'\s'), ''); // Hapus whitespace
        return MemoryImage(base64Decode(base64Encoded));
      } catch (e) {
        debugPrint('Error decoding Base64 image in convertStringToImageProvider: $e');
        return const AssetImage(AppImage.defaultRoomImage); // Kembali ke placeholder
      }
    }
    // Jika itu adalah URL jaringan
    else if (imageString.startsWith('http://') || imageString.startsWith('https://')) {
      return NetworkImage(imageString);
    }
    // Asumsikan itu adalah path aset lokal
    else {
      try {
        return AssetImage(imageString);
      } catch (e) {
        debugPrint('Error loading asset image in convertStringToImageProvider: $e');
        return const AssetImage(AppImage.defaultRoomImage); // Kembali ke placeholder
      }
    }
  }

  // Metode ini mengembalikan Widget (Image atau SvgPicture)
  static Widget convertStringToImageWidget(
      String? imageString, {
        double? width,
        double? height,
        BoxFit fit = BoxFit.cover,
        AlignmentGeometry alignment = Alignment.bottomCenter, // Default alignment
      }) {
    // Jika string gambar null atau kosong, kembalikan placeholder default.
    if (imageString == null || imageString.isEmpty) {
      return _buildAssetImagePlaceholder(width, height, fit, alignment);
    }

    final String lowerCaseImageString = imageString.toLowerCase();
    if (lowerCaseImageString.endsWith('.svg')) {
      // Jika URL jaringan
      if (lowerCaseImageString.startsWith('http://') || lowerCaseImageString.startsWith('https://')) {
        return SvgPicture.network(
          imageString,
          width: width,
          height: height,
          fit: fit,
          alignment: alignment,
          placeholderBuilder: (context) => _buildLoadingPlaceholder(width, height),
          errorBuilder: (context, error, stackTrace) {
            debugPrint('Error loading network SVG: $error');
            return _buildAssetImagePlaceholder(width, height, fit, alignment);
          },
        );
      }
      // Jika path aset lokal
      else {
        return SvgPicture.asset(
          imageString,
          width: width,
          height: height,
          fit: fit,
          alignment: alignment,
          placeholderBuilder: (context) => _buildLoadingPlaceholder(width, height),
          errorBuilder: (context, error, stackTrace) {
            debugPrint('Error loading asset SVG: $error');
            return _buildAssetImagePlaceholder(width, height, fit, alignment);
          },
        );
      }
    }

    else if (lowerCaseImageString.startsWith('data:image') ||
        imageString.startsWith('/9j/') || // Umum untuk JPEG Base64 tanpa prefix
        imageString.startsWith('iVBORw0KGgoAAAANSUhEU') || // Umum untuk PNG Base64 tanpa prefix
        imageString.length > 100) { // Indikasi lain string Base64 yang panjang
      try {
        String base64Encoded = imageString;
        if (imageString.startsWith('data:image')) {
          base64Encoded = imageString.split(',').last;
        }
        base64Encoded = base64Encoded.trim().replaceAll(RegExp(r'\s'), ''); // Hapus whitespace
        return Image.memory(
          base64Decode(base64Encoded),
          width: width,
          height: height,
          fit: fit,
          alignment: alignment,
          errorBuilder: (context, error, stackTrace) {
            debugPrint('Error decoding Base64 image in convertStringToImageWidget: $error');
            return _buildAssetImagePlaceholder(width, height, fit, alignment);
          },
        );
      } catch (e) {
        debugPrint('Error decoding Base64 image in convertStringToImageWidget (catch): $e');
        return _buildAssetImagePlaceholder(width, height, fit, alignment); // Fallback jika decode gagal
      }
    }
    // --- LOGIKA RASTER (NETWORK) ---
    // Jika string adalah URL jaringan (bukan SVG dan bukan Base64)
    else if (lowerCaseImageString.startsWith('http://') || lowerCaseImageString.startsWith('https://')) {
      return Image.network(
        imageString,
        width: width,
        height: height,
        fit: fit,
        alignment: alignment,
        loadingBuilder: (context, child, loadingProgress) {
          if (loadingProgress == null) return child;
          return _buildLoadingPlaceholder(width, height, loadingProgress: loadingProgress);
        },
        errorBuilder: (context, error, stackTrace) {
          debugPrint('Error loading network image in convertStringToImageWidget: $error');
          return _buildAssetImagePlaceholder(width, height, fit, alignment);
        },
      );
    }
    // --- LOGIKA RASTER (ASSET) ---
    // Jika string adalah path aset lokal (bukan SVG, bukan Base64, bukan URL jaringan)
    else {
      return Image.asset(
        imageString,
        width: width,
        height: height,
        fit: fit,
        alignment: alignment,
        errorBuilder: (context, error, stackTrace) {
          debugPrint('Error loading asset image in convertStringToImageWidget: $error');
          return _buildAssetImagePlaceholder(width, height, fit, alignment);
        },
      );
    }
  }

  // Helper method untuk placeholder loading
  static Widget _buildLoadingPlaceholder(
      double? width,
      double? height, {
        ImageChunkEvent? loadingProgress,
      }) {
    return Container(
      width: width,
      height: height,
      color: Colors.grey[200],
      child: Center(
        child: CircularProgressIndicator(
          value: loadingProgress != null && loadingProgress.expectedTotalBytes != null
              ? loadingProgress.cumulativeBytesLoaded / loadingProgress.expectedTotalBytes!
              : null,
        ),
      ),
    );
  }

  // Helper method untuk placeholder gambar default (ulinhouse.jpg)
  static Widget _buildAssetImagePlaceholder(
      double? width,
      double? height,
      BoxFit fit,
      AlignmentGeometry alignment,
      ) {
    // Menggunakan AppImage.defaultRoomImage sebagai placeholder
    return Image.asset(
      AppImage.defaultRoomImage,
      width: width,
      height: height,
      fit: fit,
      alignment: alignment,
    );
  }
}