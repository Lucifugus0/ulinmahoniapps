import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class CustomBackButton extends StatelessWidget {
  final Color iconColor;
  final double iconSize;
  final String? redirectRoute;
  final bool isInAppBar;

  const CustomBackButton({
    super.key,
    this.iconColor = Colors.white,
    this.iconSize = 24.0,
    this.redirectRoute,
    this.isInAppBar = false,
  });

  @override
  Widget build(BuildContext context) {
    final bool shouldShowButton = redirectRoute != null || context.canPop();
    if (!shouldShowButton) {
      return const SizedBox.shrink();
    }

    // Jika di dalam AppBar, gunakan style lama (IconButton biasa)
    if (isInAppBar) {
      return IconButton(
        icon: Icon(Icons.arrow_back, color: iconColor, size: iconSize),
        onPressed: () {
          if (redirectRoute != null) {
            context.go(redirectRoute!);
          } else {
            context.pop();
          }
        },
      );
    }

    // Style baru dengan background blur bulat
    return GestureDetector(
      onTap: () {
        if (redirectRoute != null) {
          context.go(redirectRoute!);
        } else {
          context.pop();
        }
      },
      child: Container(
        width: 40,
        height: 40,
        decoration: BoxDecoration(
          shape: BoxShape.circle,
          color: Colors.black.withOpacity(0.3),
        ),
        child: ClipOval(
          child: BackdropFilter(
            filter: ImageFilter.blur(sigmaX: 10, sigmaY: 10),
            child: Container(
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                color: Colors.white.withOpacity(0.2),
              ),
              child: Icon(
                Icons.arrow_back,
                color: iconColor,
                size: iconSize,
              ),
            ),
          ),
        ),
      ),
    );
  }
}
