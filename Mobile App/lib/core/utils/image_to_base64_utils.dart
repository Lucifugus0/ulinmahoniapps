import 'dart:io';
import 'dart:convert';
import 'package:image_picker/image_picker.dart';

/// Utility class for converting images to base64 strings
class ImageToBase64Utils {
  /// Convert a File to base64 string
  static Future<String> fileToBase64(File file) async {
    try {
      final bytes = await file.readAsBytes();
      return base64Encode(bytes);
    } catch (e) {
      throw Exception('Failed to convert file to base64: $e');
    }
  }

  /// Convert an XFile (from image_picker) to base64 string
  static Future<String> xFileToBase64(XFile xFile) async {
    try {
      final bytes = await xFile.readAsBytes();
      return base64Encode(bytes);
    } catch (e) {
      throw Exception('Failed to convert XFile to base64: $e');
    }
  }

  /// Convert bytes to base64 string
  static String bytesToBase64(List<int> bytes) {
    try {
      return base64Encode(bytes);
    } catch (e) {
      throw Exception('Failed to convert bytes to base64: $e');
    }
  }

  /// Decode base64 string to bytes
  static List<int> base64ToBytes(String base64String) {
    try {
      return base64Decode(base64String);
    } catch (e) {
      throw Exception('Failed to decode base64 string: $e');
    }
  }

  /// Validate if a string is valid base64
  static bool isValidBase64(String value) {
    try {
      base64Decode(value);
      return true;
    } catch (e) {
      return false;
    }
  }

  /// Get file size in KB from base64 string
  static double getBase64SizeInKB(String base64String) {
    final bytes = base64Decode(base64String);
    return bytes.length / 1024;
  }

  /// Get file size in MB from base64 string
  static double getBase64SizeInMB(String base64String) {
    return getBase64SizeInKB(base64String) / 1024;
  }

  /// Compress and convert file to base64 with size limit
  /// If the file is larger than maxSizeInKB, it will throw an exception
  static Future<String> fileToBase64WithSizeCheck(
    File file, {
    int maxSizeInKB = 5120, // Default 5MB
  }) async {
    try {
      final bytes = await file.readAsBytes();
      final sizeInKB = bytes.length / 1024;

      if (sizeInKB > maxSizeInKB) {
        throw Exception(
          'File size (${sizeInKB.toStringAsFixed(2)} KB) exceeds maximum allowed size ($maxSizeInKB KB)',
        );
      }

      return base64Encode(bytes);
    } catch (e) {
      throw Exception('Failed to convert file to base64: $e');
    }
  }

  /// Convert XFile to base64 with size limit
  static Future<String> xFileToBase64WithSizeCheck(
    XFile xFile, {
    int maxSizeInKB = 5120, // Default 5MB
  }) async {
    try {
      final bytes = await xFile.readAsBytes();
      final sizeInKB = bytes.length / 1024;

      if (sizeInKB > maxSizeInKB) {
        throw Exception(
          'File size (${sizeInKB.toStringAsFixed(2)} KB) exceeds maximum allowed size ($maxSizeInKB KB)',
        );
      }

      return base64Encode(bytes);
    } catch (e) {
      throw Exception('Failed to convert XFile to base64: $e');
    }
  }
}
