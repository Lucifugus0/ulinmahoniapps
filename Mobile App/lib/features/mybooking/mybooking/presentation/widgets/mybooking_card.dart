import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../../../core/utils/formatdate.dart';
import '../../provider/mybooking_provider.dart';
import '../../../../../core/utils/imageconverter_utils.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../core/utils/app_logger.dart';
import '../../../../../core/constants/appcolor_constants.dart';

class BookingCard extends ConsumerStatefulWidget {
  final int id;
  final String orderId;
  final String title;
  final String roomName;
  final String? roomNo;
  final String checkIn;
  final String checkOut;
  final String status;
  final Map<String, dynamic> dataDetail;
  final int propertyId;

  const BookingCard({
    Key? key,
    required this.id,
    required this.orderId,
    required this.title,
    required this.roomName,
    this.roomNo,
    required this.checkIn,
    required this.checkOut,
    required this.status,
    required this.dataDetail,
    required this.propertyId,
  }) : super(key: key);

  @override
  ConsumerState<BookingCard> createState() => _BookingCardState();
}

class _BookingCardState extends ConsumerState<BookingCard> {
  Color _statusColor(String statusText) {
    switch (statusText.toLowerCase()) {
      case 'pending':
        return Colors.red;
      case 'waiting':
        return Colors.orange;
      case 'paid':
        return Colors.green;
      case 'cancelled':
        return Colors.red;
      case 'rejected':
        return Colors.red;
      case 'expired':
        return Colors.grey;
      default:
        return Colors.grey;
    }
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final screenWidth = MediaQuery.of(context).size.width;
    final isSmallScreen = screenWidth < 360;
    final isDark = Theme.of(context).brightness == Brightness.dark;

    final propertyImagesAsync = ref.watch(propertyImagesProvider(widget.propertyId));

    return GestureDetector(
      onTap: () {
        context.push('/mybookingdetails', extra: widget.dataDetail);
      },
      child: Card(
        color: isDark ? AppColors.surfaceDark : Colors.white,
        margin: const EdgeInsets.symmetric(vertical: 8),
        child: Padding(
          padding: const EdgeInsets.all(10),
          child: Row(
            crossAxisAlignment: CrossAxisAlignment.center,
            children: [
              Container(
                height: 110,
                width: 110,
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(8),
                  color: Colors.grey[200],
                ),
                clipBehavior: Clip.hardEdge,
                child: propertyImagesAsync.when(
                  data: (images) {
                    if (images.isNotEmpty) {
                      return ImageConverter.convertStringToImageWidget(
                        images.first.imageShow,
                        width: 130,
                        height: 130,
                        fit: BoxFit.cover,
                      );
                    } else {
                      return Center(
                        child: ImageConverter.convertStringToImageWidget(
                            null,
                            width: 110,
                            height: 110,
                            fit: BoxFit.cover
                        ),
                      );
                    }
                  },
                  loading: () => const Center(
                    child: SizedBox(
                      height: 40,
                      width: 40,
                      child: CircularProgressIndicator(strokeWidth: 2),
                    ),
                  ),
                  error: (error, stack) {
                    AppLogger.w('Error fetching images for Property ID ${widget.propertyId}: $error', 'MYBOOKING');
                    return Center(
                      child: ImageConverter.convertStringToImageWidget(
                          null,
                          width: 110,
                          height: 110,
                          fit: BoxFit.cover
                      ),
                    );
                  },
                ),
              ),
              const SizedBox(width: 12),
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      widget.title,
                      style: TextStyle(
                        fontSize: isSmallScreen ? 12 : 14,
                        fontWeight: FontWeight.bold,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    Text(
                      widget.orderId,
                      style: TextStyle(
                        fontSize: isSmallScreen ? 10 : 11,
                        color: isDark ? Colors.grey[400] : Colors.grey[600],
                        fontWeight: FontWeight.w500,
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 2),
                    Text(
                      widget.roomNo != null && widget.roomNo!.isNotEmpty
                          ? '${widget.roomName} - No. ${widget.roomNo}'
                          : widget.roomName,
                      style: TextStyle(
                        fontSize: isSmallScreen ? 11 : 12,
                        color: isDark ? Colors.grey[400] : Colors.grey[700],
                      ),
                      overflow: TextOverflow.ellipsis,
                    ),
                    const SizedBox(height: 6),
                    Container(
                      padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                      decoration: BoxDecoration(
                        color: _statusColor(widget.status).withOpacity(0.1),
                        border: Border.all(color: _statusColor(widget.status)),
                        borderRadius: BorderRadius.circular(6),
                      ),
                      child: Text(
                        widget.status,
                        style: TextStyle(
                          fontSize: isSmallScreen ? 10 : 11,
                          color: _statusColor(widget.status),
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                    ),
                    const SizedBox(height: 8),
                    Text(
                      '${localizations.checkInLabel}: ${formatDate(widget.checkIn) ?? widget.checkIn}',
                      style: TextStyle(fontSize: 12, color: isDark ? Colors.grey[300] : Colors.black87),
                    ),
                    Text(
                      '${localizations.checkOutLabel}: ${formatDate(widget.checkOut) ?? widget.checkOut}',
                      style: TextStyle(fontSize: 12, color: isDark ? Colors.grey[300] : Colors.black87),
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