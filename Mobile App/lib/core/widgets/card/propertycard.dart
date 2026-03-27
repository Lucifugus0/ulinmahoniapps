import 'package:flutter/material.dart';
import 'dart:convert';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../constants/appcolor_constants.dart';
import '../../constants/app_asset_constants.dart';
import '../../theme/glass_theme.dart';

/// Property listing card with glass effect border and theme-aware styling.
/// Rounded corners, subtle glass border, and shadow adapt to dark/light mode.
class PropertyCard extends StatefulWidget {
  final String? image;
  final String? title;
  final String? location;
  final String? detail;
  final String? price;
  final String? badgeText;
  final String? type;
  final VoidCallback? onTap;
  final double? width;
  final double? height;
  final double? imageHeight;
  final bool isRoomDetail;
  final int? roomStatus;
  final int? totalRooms;
  final int? availableRooms;
  final String? gender;

  const PropertyCard({
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
    this.type,
    this.totalRooms,
    this.availableRooms,
    this.gender,
  });

  @override
  State<PropertyCard> createState() => _PropertyCardState();
}

class _PropertyCardState extends State<PropertyCard> {
  @override
  Widget build(BuildContext context) {
    final Size screenSize = MediaQuery.of(context).size;
    final double cardWidth = widget.width ?? screenSize.width * 0.57;
    final double minCardHeight = screenSize.height * 0.22;
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    Color statusBgColor = Colors.black.withValues(alpha: 0.7);
    Color statusTextColor = Colors.white;

    IconData statusIcon = Icons.location_on;
    String? displayTextForBadge = widget.location;

    final int maxCharacters = 27;
    if (displayTextForBadge != null && displayTextForBadge.length > maxCharacters) {
      displayTextForBadge = '${displayTextForBadge.substring(0, maxCharacters)}...';
    }

    if (widget.isRoomDetail && widget.roomStatus != null) {
      if (widget.roomStatus == 1) {
        // Available: use adaptive primary color for the status badge
        statusBgColor = AppColors.primaryAdaptive(context);
        statusIcon = Icons.check_circle_outline;
        displayTextForBadge = localizations.availableStatus;
        statusTextColor = Colors.white;
      } else if (widget.roomStatus == 0) {
        statusBgColor = AppColors.secondaryColor;
        statusIcon = Icons.cancel_outlined;
        displayTextForBadge = localizations.unavailableStatus;
        statusTextColor = Colors.white;
      } else {
        displayTextForBadge = localizations.unknownStatus;
        statusBgColor = Colors.grey.shade700;
        statusIcon = Icons.help_outline;
        statusTextColor = Colors.white;
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
              return Image.asset(
                AppImage.defaultPropertyImage,
                fit: BoxFit.cover,
                alignment: Alignment.center,
              );
            },
          );
        } catch (e) {
          imageWidget = Image.asset(
            AppImage.defaultPropertyImage,
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
            return Image.asset(
              AppImage.defaultPropertyImage,
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
            return Image.asset(
              AppImage.defaultPropertyImage,
              fit: BoxFit.cover,
              alignment: Alignment.center,
            );
          },
        );
      }
    } else {
      imageWidget = Image.asset(
        AppImage.defaultPropertyImage,
        fit: BoxFit.cover,
        alignment: Alignment.center,
      );
    }

    return GestureDetector(
      onTap: widget.onTap,
      child: Container(
        width: cardWidth,
        height: minCardHeight,
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

              // Available Rooms Badge (Top Right)
              if (widget.availableRooms != null && !widget.isRoomDetail)
                Positioned(
                  top: 8,
                  right: 8,
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      // Use primaryAdaptive so the available-rooms badge adapts to dark/light
                      color: widget.availableRooms! > 0
                          ? AppColors.primaryAdaptive(context).withValues(alpha: 0.9)
                          : Colors.red.withValues(alpha: 0.9),
                      borderRadius: BorderRadius.circular(12),
                    ),
                    child: Row(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        Icon(
                          widget.availableRooms! > 0 ? Icons.meeting_room : Icons.cancel,
                          color: Colors.white,
                          size: 14,
                        ),
                        const SizedBox(width: 4),
                        Text(
                          widget.availableRooms! > 0
                              ? '${widget.availableRooms} ${Localizations.localeOf(context).languageCode == 'id' ? 'tersedia' : 'available'}'
                              : (Localizations.localeOf(context).languageCode == 'id' ? 'Penuh' : 'Full'),
                          style: const TextStyle(
                            color: Colors.white,
                            fontSize: 11,
                            fontWeight: FontWeight.w600,
                          ),
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
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),

                    const SizedBox(height: 4),

                    // Gender Badge
                    if (widget.gender != null && widget.gender!.isNotEmpty)
                      Container(
                        padding: const EdgeInsets.symmetric(horizontal: 6, vertical: 3),
                        decoration: BoxDecoration(
                          color: widget.gender!.toLowerCase() == 'male'
                              ? Colors.blue.withValues(alpha: 0.85)
                              : widget.gender!.toLowerCase() == 'female'
                                  ? Colors.pink.withValues(alpha: 0.85)
                                  : Colors.purple.withValues(alpha: 0.85),
                          borderRadius: BorderRadius.circular(8),
                        ),
                        child: Row(
                          mainAxisSize: MainAxisSize.min,
                          children: [
                            Icon(
                              widget.gender!.toLowerCase() == 'male'
                                  ? Icons.male
                                  : widget.gender!.toLowerCase() == 'female'
                                      ? Icons.female
                                      : Icons.group,
                              color: Colors.white,
                              size: 11,
                            ),
                            const SizedBox(width: 3),
                            Text(
                              widget.gender!.toLowerCase() == 'male'
                                  ? (Localizations.localeOf(context).languageCode == 'id' ? 'Pria' : 'Male')
                                  : widget.gender!.toLowerCase() == 'female'
                                      ? (Localizations.localeOf(context).languageCode == 'id' ? 'Wanita' : 'Female')
                                      : (Localizations.localeOf(context).languageCode == 'id' ? 'Campuran' : 'Mixed'),
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 10,
                                fontWeight: FontWeight.w600,
                              ),
                            ),
                          ],
                        ),
                      ),

                    const SizedBox(height: 4),

                    // Location
                    if (widget.location != null && widget.location!.isNotEmpty)
                      Row(
                        children: [
                          const Icon(
                            Icons.location_on,
                            color: Colors.white,
                            size: 12,
                          ),
                          const SizedBox(width: 2),
                          Expanded(
                            child: Text(
                              widget.location!,
                              style: const TextStyle(
                                fontSize: 11,
                                color: Colors.white,
                                fontWeight: FontWeight.w400,
                              ),
                              maxLines: 1,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),

                    const SizedBox(height: 6),

                    // Price
                    if (widget.price != null && widget.price!.isNotEmpty && widget.price != 'Rp 0')
                      Text(
                        // Use localized prefix — handles ID/EN/ZH correctly
                        '${localizations.bottomBarStartingFrom} ${widget.price!}',
                        style: const TextStyle(
                          fontSize: 12,
                          color: Colors.white,
                          fontWeight: FontWeight.w600,
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
