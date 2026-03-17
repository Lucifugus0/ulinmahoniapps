import 'package:flutter/material.dart';
import '../../constants/app_asset_constants.dart';


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

  return showDialog<bool>(
    context: context,
    builder: (ctx) => AlertDialog(
      backgroundColor: Colors.white,

      title: title != null ? Text(
        title,
        textAlign: TextAlign.center,
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
            style: const TextStyle(
              fontSize: 14,
              color: Colors.black87,
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
            style: const TextStyle(
              color: Colors.black,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
      ],
    ),
  );
}