import 'package:flutter/material.dart';
import 'package:cached_network_image/cached_network_image.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../model/message_model.dart';

/// Message bubble widget - WhatsApp-like design
class MessageBubble extends StatelessWidget {
  final MessageModel message;
  final bool isFromCurrentUser;
  final VoidCallback? onLongPress;
  final VoidCallback? onImageTap;

  const MessageBubble({
    super.key,
    required this.message,
    required this.isFromCurrentUser,
    this.onLongPress,
    this.onImageTap,
  });

  @override
  Widget build(BuildContext context) {
    // System messages (centered)
    if (message.isSystem) {
      return _buildSystemMessage(context);
    }

    // Regular messages (left/right alignment)
    return Align(
      alignment: isFromCurrentUser ? Alignment.centerRight : Alignment.centerLeft,
      child: GestureDetector(
        onLongPress: onLongPress,
        child: Container(
          margin: EdgeInsets.only(
            left: isFromCurrentUser ? 64 : 8,
            right: isFromCurrentUser ? 8 : 64,
            top: 4,
            bottom: 4,
          ),
          child: Column(
            crossAxisAlignment: isFromCurrentUser
                ? CrossAxisAlignment.end
                : CrossAxisAlignment.start,
            children: [
              // Sender name (for messages not from current user)
              if (!isFromCurrentUser) ...[
                Padding(
                  padding: const EdgeInsets.only(left: 12, bottom: 2),
                  child: Text(
                    message.sender.fullName,
                    style: TextStyle(
                      fontSize: 11,
                      color: Colors.grey.shade600,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ),
              ],

              // Message bubble
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 12,
                  vertical: 8,
                ),
                decoration: BoxDecoration(
                  color: isFromCurrentUser
                      ? AppColors.secondaryColor // Customer: hijau terang
                      : AppColors.primaryColor, // HO/FO: primary color
                  borderRadius: BorderRadius.only(
                    topLeft: const Radius.circular(16),
                    topRight: const Radius.circular(16),
                    bottomLeft: Radius.circular(isFromCurrentUser ? 16 : 4),
                    bottomRight: Radius.circular(isFromCurrentUser ? 4 : 16),
                  ),
                ),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    // Image attachment
                    if (message.hasAttachments &&
                        message.attachments.first.isImage) ...[
                      GestureDetector(
                        onTap: onImageTap,
                        child: ClipRRect(
                          borderRadius: BorderRadius.circular(8),
                          child: CachedNetworkImage(
                            imageUrl: message.attachments.first.fileUrl,
                            width: 200,
                            fit: BoxFit.cover,
                            placeholder: (context, url) => Container(
                              width: 200,
                              height: 150,
                              color: Colors.grey.shade300,
                              child: const Center(
                                child: CircularProgressIndicator(),
                              ),
                            ),
                            errorWidget: (context, url, error) => Container(
                              width: 200,
                              height: 150,
                              color: Colors.grey.shade300,
                              child: const Icon(Icons.error),
                            ),
                          ),
                        ),
                      ),
                      if (message.messageText.isNotEmpty)
                        const SizedBox(height: 8),
                    ],

                    // Text message
                    if (message.messageText.isNotEmpty)
                      Text(
                        message.messageText,
                        style: const TextStyle(
                          fontSize: 14,
                          color: Colors.white, // Putih untuk semua bubble (customer & HO/FO)
                        ),
                      ),

                    // Time and edited indicator
                    const SizedBox(height: 4),
                    Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        if (message.isEdited) ...[
                          Text(
                            'Edited',
                            style: TextStyle(
                              fontSize: 10,
                              color: Colors.white.withOpacity(0.7),
                              fontStyle: FontStyle.italic,
                            ),
                          ),
                          const SizedBox(width: 4),
                        ],
                        Text(
                          message.formattedTime,
                          style: TextStyle(
                            fontSize: 10,
                            color: Colors.white.withOpacity(0.7),
                          ),
                        ),
                      ],
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }

  /// Build system message (centered, italic)
  Widget _buildSystemMessage(BuildContext context) {
    return Center(
      child: Container(
        margin: const EdgeInsets.symmetric(vertical: 8, horizontal: 16),
        padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
        decoration: BoxDecoration(
          color: Colors.grey.shade100,
          borderRadius: BorderRadius.circular(12),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.08),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Text(
          message.messageText,
          textAlign: TextAlign.center,
          style: TextStyle(
            fontSize: 12,
            color: Colors.grey.shade700,
            fontStyle: FontStyle.italic,
          ),
        ),
      ),
    );
  }
}
