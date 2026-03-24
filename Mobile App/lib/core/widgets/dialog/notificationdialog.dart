import 'package:flutter/material.dart';
import '../../constants/app_asset_constants.dart';

/// Shows a themed notification dialog with icon and message.
/// Adapts background and text colors to dark/light mode.
Future<bool?> showNotificationDialog(
    BuildContext context,
    String message, {
      String? title,
      IconData defaultIcon = Icons.info_outline,
      Color iconColor = Colors.blue,
      String okButtonText = 'OK',
      VoidCallback? onOkPressed,
      bool barrierDismissible = false,
    }) {

  final isDark = Theme.of(context).brightness == Brightness.dark;

  return showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      // Glass-style dialog background from theme
      backgroundColor: isDark ? const Color(0xFF374151) : Colors.white,

      title: title != null ? Text(
        title,
        textAlign: TextAlign.center,
        style: TextStyle(color: isDark ? Colors.white : Colors.black87),
      ) : null,
      contentPadding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
            Image.asset(
              AppImage.logo,
              width: 80,
              height: 80,
              errorBuilder: (context, error, stackTrace) {

                return Icon(
                  defaultIcon,
                  color: iconColor,
                  size: 80,
                );
              },
            ),

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
      ),
      actionsAlignment: MainAxisAlignment.center,
      actions: [
        TextButton(
          onPressed: () {
            final bool popValue = onOkPressed != null ? false : true;
            onOkPressed?.call();
            Navigator.of(ctx).pop(popValue);
          },
          child: Text(
            okButtonText,
            style: TextStyle(
              color: isDark ? Colors.white : Colors.black,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    ),
  );
}
