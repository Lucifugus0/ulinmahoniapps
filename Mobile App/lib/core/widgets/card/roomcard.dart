import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../constants/appcolor_constants.dart';
import '../../constants/app_asset_constants.dart';
import '../../utils/app_logger.dart';
import '../../theme/glass_theme.dart';

/// Room card with glass-style border and theme-aware styling.
/// Rounded corners and shadow adapt to dark/light mode.
class RoomCard extends StatefulWidget {
  final String? image;
  final String? title;
  final String? location;
  final String? detail;
  final String? price;
  final String? badgeText;
  final String? no;
  final VoidCallback? onTap;
  final double? width;
  final double? height;
  final double? imageHeight;
  final bool isRoomDetail;
  final int? roomStatus;

  const RoomCard({
    super.key,
    required this.image,
    this.title,
    this.location,
    this.detail,
    this.price,
    this.badgeText,
    this.onTap,
    this.width,
    this.height,
    this.imageHeight,
    this.isRoomDetail = false,
    this.roomStatus,
    this.no,
  });

  @override
  State<RoomCard> createState() => _RoomCardState();
}

class _RoomCardState extends State<RoomCard> {
  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final Size screenSize = MediaQuery.of(context).size;
    final double cardWidth = widget.width ?? screenSize.width * 0.7;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    Color statusBgColor = Colors.black.withValues(alpha: 0.7);
    Color statusTextColor = Colors.white;

    IconData statusIcon = Icons.location_on;
    String? displayTextForBadge;

    if (widget.isRoomDetail && widget.roomStatus != null) {
      if (widget.roomStatus == 1) {
        // Available: use adaptive primary color for the status badge
        statusBgColor = AppColors.primaryAdaptive(context);
        statusIcon = Icons.check_circle_outline;
        displayTextForBadge = localizations.roomCardAvailable;
        statusTextColor = Colors.white;
      } else if (widget.roomStatus == 0) {
        statusBgColor = AppColors.secondaryColor;
        statusIcon = Icons.cancel_outlined;
        displayTextForBadge = localizations.roomCardNotAvailable;
        statusTextColor = Colors.white;
      } else if (widget.roomStatus == 2) {
        statusBgColor = AppColors.secondaryColor;
        statusIcon = Icons.cancel_outlined;
        displayTextForBadge = localizations.roomCardUnderMaintenance;
        statusTextColor = Colors.white;
      } else if (widget.roomStatus == 3) {
        statusBgColor = Colors.orange;
        statusIcon = Icons.home_work_outlined;
        displayTextForBadge = localizations.roomCardCurrentlyRented;
        statusTextColor = Colors.white;
      } else {
        displayTextForBadge = localizations.roomCardUnknown;
        statusBgColor = Colors.grey.shade700;
        statusIcon = Icons.help_outline;
        statusTextColor = Colors.white;
      }
    } else {
      displayTextForBadge = widget.location;
      final int maxCharacters = 20;
      if (displayTextForBadge != null && displayTextForBadge.length > maxCharacters) {
        displayTextForBadge = '${displayTextForBadge.substring(0, maxCharacters)}...';
      }
    }

    Widget imageWidget;
    if (widget.image != null && widget.image!.isNotEmpty) {
      if (widget.image!.startsWith('data:image') || widget.image!.length > 100) {
        try {
          String base64String = widget.image!.split(',').last;
          imageWidget = Image.memory(
            base64Decode(base64String),
            fit: BoxFit.cover,
            alignment: Alignment.center,
            errorBuilder: (context, error, stackTrace) {
              AppLogger.e('Error decoding Base64 image in RoomCard', error, stackTrace, 'ROOM-CARD');
              return Image.asset(
                AppImage.defaultRoomImage,
                fit: BoxFit.cover,
                alignment: Alignment.center,
              );
            },
          );
        } catch (e) {
          AppLogger.e('Error decoding Base64 image in RoomCard', e, StackTrace.current, 'ROOM-CARD');
          imageWidget = Image.asset(
            AppImage.defaultRoomImage,
            fit: BoxFit.cover,
            alignment: Alignment.center,
          );
        }
      } else if (widget.image!.startsWith('http://') ||
          widget.image!.startsWith('https://')) {
        imageWidget = Image.network(
          widget.image!,
          fit: BoxFit.cover,
          alignment: Alignment.center,
          loadingBuilder: (BuildContext context, Widget child,
              ImageChunkEvent? loadingProgress) {
            if (loadingProgress == null) return child;
            return Container(
              color: isDark ? Colors.grey[800] : Colors.grey[300],
            );
          },
          errorBuilder: (context, error, stackTrace) {
            AppLogger.e('Error loading network image in RoomCard', error, stackTrace, 'ROOM-CARD');
            return Image.asset(
              AppImage.defaultRoomImage,
              fit: BoxFit.cover,
              alignment: Alignment.center,
            );
          },
        );
      } else {
        imageWidget = Image.asset(
          widget.image!,
          fit: BoxFit.cover,
          alignment: Alignment.center,
          errorBuilder: (context, error, stackTrace) {
            AppLogger.e('Error loading asset image in RoomCard', error, stackTrace, 'ROOM-CARD');
            return Image.asset(
              AppImage.defaultRoomImage,
              fit: BoxFit.cover,
              alignment: Alignment.center,
            );
          },
        );
      }
    } else {
      imageWidget = Image.asset(
        AppImage.defaultRoomImage,
        fit: BoxFit.cover,
        alignment: Alignment.center,
      );
    }

    return GestureDetector(
      onTap: widget.onTap,
      child: Container(
        width: cardWidth,
        height: screenSize.height * 0.25,
        decoration: BoxDecoration(
          // Glass-style rounded corners with subtle border
          borderRadius: BorderRadius.circular(GlassTheme.radiusMedium),
          border: Border.all(
            color: isDark ? GlassTheme.glassBorderDark : GlassTheme.glassBorderLight,
            width: GlassTheme.borderWidth,
          ),
          boxShadow: isDark ? GlassTheme.glassShadowDark : GlassTheme.glassShadowLight,
        ),
        child: ClipRRect(
          borderRadius: BorderRadius.circular(GlassTheme.radiusMedium),
          child: Stack(
            fit: StackFit.expand,
            children: [
              // Background Image
              imageWidget,

              // Gradient Overlay
              Container(
                decoration: BoxDecoration(
                  gradient: LinearGradient(
                    begin: Alignment.topCenter,
                    end: Alignment.bottomCenter,
                    colors: [
                      Colors.transparent,
                      Colors.black.withValues(alpha: 0.3),
                      Colors.black.withValues(alpha: 0.8),
                    ],
                    stops: const [0.4, 0.7, 1.0],
                  ),
                ),
              ),

              // Status badge at top left
              if (displayTextForBadge != null && displayTextForBadge.isNotEmpty)
                Positioned(
                  top: 12,
                  left: 12,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: statusBgColor,
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(statusIcon, color: statusTextColor, size: 14),
                        const SizedBox(width: 4),
                        Text(
                          displayTextForBadge,
                          style: TextStyle(color: statusTextColor, fontSize: 12),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ],
                    ),
                  ),
                ),

              // Content at the bottom
              Positioned(
                left: 12,
                right: 12,
                bottom: 12,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    // Title
                    if (widget.title != null && widget.title!.isNotEmpty)
                      Text(
                        widget.title!,
                        style: const TextStyle(
                          fontSize: 15,
                          fontWeight: FontWeight.bold,
                          color: Colors.white,
                          shadows: [
                            Shadow(
                              color: Colors.black26,
                              offset: Offset(0, 1),
                              blurRadius: 2,
                            ),
                          ],
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),

                    const SizedBox(height: 4),

                    // Room number
                    if (widget.no != null && widget.no!.isNotEmpty)
                      Text(
                        "No. ${widget.no!}",
                        style: const TextStyle(
                          fontSize: 11,
                          color: Colors.white,
                          fontWeight: FontWeight.w400,
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
                      ),

                    const SizedBox(height: 6),

                    // Price
                    if (widget.price != null && widget.price!.isNotEmpty && widget.price != 'Rp 0')
                      RichText(
                        text: TextSpan(
                          style: const TextStyle(
                            fontSize: 12,
                            color: Colors.white,
                            fontWeight: FontWeight.w400,
                          ),
                          children: [
                            TextSpan(text: '${localizations.startingFrom('').split(' ').first} '),
                            TextSpan(
                              text: widget.price!,
                              style: const TextStyle(
                                fontWeight: FontWeight.bold,
                              ),
                            ),
                            TextSpan(text: '/${localizations.startingFrom('').split('/').last}'),
                          ],
                        ),
                        maxLines: 1,
                        overflow: TextOverflow.ellipsis,
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
}
