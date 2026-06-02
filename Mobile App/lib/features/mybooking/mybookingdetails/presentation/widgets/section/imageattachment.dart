import 'dart:typed_data';
import '../../../../../../core/constants/appcolor_constants.dart';
import 'package:flutter/material.dart';
import '../../../../../../core/widgets/button/customiconbutton_components.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../../../core/utils/app_logger.dart';

class ImageViewerWidget extends StatefulWidget {
  final Uint8List? decodedAttachmentImageBytes;
  final VoidCallback onUpdatePressed;
  final VoidCallback onToggleVisibilityPressed;
  final bool shouldShowUpdateButton;

  const ImageViewerWidget({
    Key? key,
    required this.decodedAttachmentImageBytes,
    required this.onUpdatePressed,
    required this.onToggleVisibilityPressed,
    this.shouldShowUpdateButton = true, // Default true untuk backward compatibility
  }) : super(key: key);

  @override
  State<ImageViewerWidget> createState() => _ImageViewerWidgetState();
}

class _ImageViewerWidgetState extends State<ImageViewerWidget> {
  bool _showImage = false;

  @override
  void didUpdateWidget(covariant ImageViewerWidget oldWidget) {
    super.didUpdateWidget(oldWidget);
    if (widget.decodedAttachmentImageBytes == null && oldWidget.decodedAttachmentImageBytes != null) {
      _showImage = false;
    }
  }

  void _showFullScreenImage(BuildContext context) {
    if (widget.decodedAttachmentImageBytes == null) return;

    showDialog(
      context: context,
      builder: (context) => Dialog(
        backgroundColor: Colors.black,
        insetPadding: EdgeInsets.zero,
        child: Stack(
          children: [
            Center(
              child: InteractiveViewer(
                minScale: 0.5,
                maxScale: 4.0,
                child: Image.memory(
                  widget.decodedAttachmentImageBytes!,
                  fit: BoxFit.contain,
                ),
              ),
            ),
            Positioned(
              top: 16,
              right: 16,
              child: IconButton(
                icon: const Icon(Icons.close, color: Colors.white, size: 30),
                onPressed: () => Navigator.of(context).pop(),
              ),
            ),
          ],
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final bool hasAttachment = widget.decodedAttachmentImageBytes != null;
    AppLogger.d('hasAttachment = $hasAttachment', 'IMAGEVIEWER');
    if (hasAttachment) {
      AppLogger.d('decodedAttachmentImageBytes length = ${widget.decodedAttachmentImageBytes!.length}', 'IMAGEVIEWER');
    } else {
      AppLogger.d('decodedAttachmentImageBytes is NULL', 'IMAGEVIEWER');
    }
    return Column(
      children: [
        if (hasAttachment)
          AnimatedOpacity(
            opacity: _showImage ? 1.0 : 0.0,
            duration: const Duration(milliseconds: 300),
            child: Visibility(
              visible: _showImage,
              child: Center(
                child: Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: GestureDetector(
                    onTap: () {
                      _showFullScreenImage(context);
                    },
                    child: Container(
                      constraints: const BoxConstraints(
                        maxHeight: 400,
                        maxWidth: double.infinity,
                      ),
                      child: Image.memory(
                        widget.decodedAttachmentImageBytes!,
                        fit: BoxFit.contain,
                      ),
                    ),
                  ),
                ),
              ),
            ),
          ),
        if (hasAttachment)
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16.0, vertical: 8.0),
            child: widget.shouldShowUpdateButton
                ? Row(
                    children: [
                      Expanded(
                        child: CustomIconButton(
                          onPressed: () {
                            setState(() {
                              _showImage = !_showImage;
                            });
                            widget.onToggleVisibilityPressed();
                          },
                          icon: _showImage ? Icons.visibility_off : Icons.visibility,
                          text: _showImage ? localizations.viewerHideAttachment : localizations.viewerShowPaymentProof,
                          // Use primaryAdaptive for dark/light mode compatibility
                          buttonColor: AppColors.primaryAdaptive(context),
                          textColor: Colors.white,
                          iconColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                          borderRadius: 8,
                          height: 60,
                          fontSize: 11,
                        ),
                      ),
                      const SizedBox(width: 12),
                      Expanded(
                        child: CustomIconButton(
                          onPressed: widget.onUpdatePressed,
                          icon: Icons.edit,
                          text: localizations.viewerUpdatePaymentProof,
                          buttonColor: AppColors.secondaryColor,
                          textColor: Colors.white,
                          iconColor: Colors.white,
                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                          borderRadius: 8,
                          height: 60,
                          fontSize: 11,
                        ),
                      ),
                    ],
                  )
                : CustomIconButton(
                    onPressed: () {
                      setState(() {
                        _showImage = !_showImage;
                      });
                      widget.onToggleVisibilityPressed();
                    },
                    icon: _showImage ? Icons.visibility_off : Icons.visibility,
                    text: _showImage ? localizations.viewerHideAttachment : localizations.viewerShowPaymentProof,
                    // Use primaryAdaptive for dark/light mode compatibility
                    buttonColor: AppColors.primaryAdaptive(context),
                    textColor: Colors.white,
                    iconColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 10),
                    borderRadius: 8,
                    height: 60,
                    fontSize: 11,
                  ),
          ),
      ],
    );
  }
}