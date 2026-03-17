import 'dart:io';
import 'package:flutter/material.dart';
import '../../../../core/constants/appcolor_constants.dart';

/// Image preview widget before sending
class ImagePreviewWidget extends StatelessWidget {
  final File image;
  final VoidCallback onRemove;
  final TextEditingController? captionController;

  const ImagePreviewWidget({
    super.key,
    required this.image,
    required this.onRemove,
    this.captionController,
  });

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        boxShadow: [
          BoxShadow(
            color: Colors.black.withOpacity(0.1),
            blurRadius: 8,
            offset: const Offset(0, -2),
          ),
        ],
      ),
      child: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // Header
          Row(
            children: [
              const Icon(
                Icons.image,
                color: AppColors.primaryColor,
                size: 20,
              ),
              const SizedBox(width: 8),
              const Text(
                'Image Preview',
                style: TextStyle(
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                  color: Colors.black87,
                ),
              ),
              const Spacer(),
              IconButton(
                onPressed: onRemove,
                icon: const Icon(Icons.close, size: 20),
                color: Colors.grey.shade600,
              ),
            ],
          ),
          const SizedBox(height: 12),

          // Image preview
          ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: Image.file(
              image,
              height: 200,
              width: double.infinity,
              fit: BoxFit.cover,
            ),
          ),
          const SizedBox(height: 12),

          // Caption field (optional)
          if (captionController != null) ...[
            Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
              decoration: BoxDecoration(
                color: Colors.grey.shade100,
                borderRadius: BorderRadius.circular(12),
              ),
              child: TextField(
                controller: captionController,
                maxLines: 2,
                decoration: InputDecoration(
                  hintText: 'Add a caption (optional)...',
                  hintStyle: TextStyle(
                    fontSize: 14,
                    color: Colors.grey.shade500,
                  ),
                  border: InputBorder.none,
                  contentPadding: EdgeInsets.zero,
                ),
                style: const TextStyle(
                  fontSize: 14,
                  color: Colors.black87,
                ),
              ),
            ),
          ],
        ],
      ),
    );
  }
}
