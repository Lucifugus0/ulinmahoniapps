import 'package:flutter/material.dart';

/// Decorative golden leaves background overlay matching the Frontend web portal.
/// Renders a repeating leaf pattern image behind all page content.
/// Opacity adapts to dark/light mode for readability.
class LeafyBackground extends StatelessWidget {
  const LeafyBackground({super.key});

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return Positioned.fill(
      child: IgnorePointer(
        child: Opacity(
          // Lower opacity in light mode, slightly higher in dark mode
          opacity: isDark ? 0.06 : 0.10,
          child: Image.asset(
            'assets/golden-leaves-bg.jpg',
            fit: BoxFit.fitWidth,
            alignment: Alignment.topCenter,
            repeat: ImageRepeat.repeatY,
          ),
        ),
      ),
    );
  }
}
