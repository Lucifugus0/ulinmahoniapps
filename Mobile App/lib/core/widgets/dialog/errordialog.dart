import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../constants/app_asset_constants.dart';

Future<bool?> showErrorDialog(
    BuildContext context,
    String message, {
      String? title, 
      List<Widget>? dialogActions, 
      String? routeName, 
      String? buttonText, 
    }) {
  
  assert(
  (routeName == null && buttonText == null) ||
      (routeName != null && buttonText != null),
  'Jika routeName disediakan, buttonText juga harus disediakan, dan sebaliknya.',
  );

  return showDialog<bool>( 
    context: context,
    builder: (ctx) => AlertDialog(
      backgroundColor: Colors.white,
      
      title: title != null ? Text(title) : null,
      contentPadding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        children: [
          Image.asset(
            AppImage.logo,
            width: 80,
            height: 80,
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
      
      actions: dialogActions ?? [
        TextButton(
          
          onPressed: () => Navigator.of(ctx).pop(false),
          child: const Text(
            'OK',
            style: TextStyle(
              color: Colors.black,
              fontWeight: FontWeight.w500,
            ),
          ),
        ),
        
        if (routeName != null && buttonText != null)
          TextButton(
            
            onPressed: () {
              Navigator.of(ctx).pop(true);
              context.push(routeName);
            },
            child: Text(
              buttonText,
              style: const TextStyle(
                color: Color(0xFF005F21),
                fontWeight: FontWeight.w600,
              ),
            ),
          ),
      ],
    ),
  );
}