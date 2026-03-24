import 'dart:ui';
import 'package:flutter/material.dart';

/// Glass design tokens for Apple-style liquid glass (glassmorphism) UI.
/// Provides static constants for blur, transparency, borders, and shadows
/// used across the app's glass-effect widgets.
class GlassTheme {
  GlassTheme._();

  // -- Blur values for BackdropFilter --
  /// Standard blur for glass surfaces (navbar, cards)
  static const double blurStandard = 20.0;

  /// Heavy blur for modals and overlays
  static const double blurHeavy = 30.0;

  /// Light blur for subtle glass effects
  static const double blurLight = 10.0;

  // -- Glass surface colors (light mode) --
  /// Light mode glass surface fill
  static Color glassSurfaceLight = Colors.white.withValues(alpha: 0.65);

  /// Light mode glass surface fill (more opaque, for cards)
  static Color glassSurfaceLightOpaque = Colors.white.withValues(alpha: 0.80);

  // -- Glass surface colors (dark mode) --
  /// Dark mode glass surface fill
  static Color glassSurfaceDark = const Color(0xFF1F2937).withValues(alpha: 0.70);

  /// Dark mode glass surface fill (more opaque, for cards)
  static Color glassSurfaceDarkOpaque = const Color(0xFF1F2937).withValues(alpha: 0.85);

  // -- Glass border colors --
  /// Light mode glass border
  static Color glassBorderLight = Colors.white.withValues(alpha: 0.35);

  /// Dark mode glass border
  static Color glassBorderDark = Colors.white.withValues(alpha: 0.12);

  // -- Border radius --
  /// Standard border radius for glass cards
  static const double radiusSmall = 12.0;
  static const double radiusMedium = 16.0;
  static const double radiusLarge = 24.0;
  static const double radiusXLarge = 35.0;

  // -- Border width --
  static const double borderWidth = 0.5;

  // -- Shadow for glass containers --
  /// Subtle shadow for light mode glass surfaces
  static List<BoxShadow> glassShadowLight = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.06),
      blurRadius: 16,
      offset: const Offset(0, 4),
    ),
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.04),
      blurRadius: 8,
      offset: const Offset(0, 2),
    ),
  ];

  /// Subtle shadow for dark mode glass surfaces
  static List<BoxShadow> glassShadowDark = [
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.30),
      blurRadius: 20,
      offset: const Offset(0, 6),
    ),
    BoxShadow(
      color: Colors.black.withValues(alpha: 0.15),
      blurRadius: 10,
      offset: const Offset(0, 3),
    ),
  ];

  // -- Helper: build a glass decoration for a given brightness --
  /// Returns a BoxDecoration for a glass container.
  /// [isDark] determines the color scheme.
  /// [opaque] uses the more opaque surface variant.
  static BoxDecoration glassDecoration({
    required bool isDark,
    bool opaque = false,
    double radius = radiusMedium,
  }) {
    return BoxDecoration(
      color: isDark
          ? (opaque ? glassSurfaceDarkOpaque : glassSurfaceDark)
          : (opaque ? glassSurfaceLightOpaque : glassSurfaceLight),
      borderRadius: BorderRadius.circular(radius),
      border: Border.all(
        color: isDark ? glassBorderDark : glassBorderLight,
        width: borderWidth,
      ),
      boxShadow: isDark ? glassShadowDark : glassShadowLight,
    );
  }

  // -- Helper: ImageFilter for BackdropFilter --
  /// Standard blur filter for glass surfaces
  static ImageFilter get standardBlur =>
      ImageFilter.blur(sigmaX: blurStandard, sigmaY: blurStandard);

  /// Heavy blur filter for modals
  static ImageFilter get heavyBlur =>
      ImageFilter.blur(sigmaX: blurHeavy, sigmaY: blurHeavy);

  /// Light blur filter for subtle effects
  static ImageFilter get lightBlur =>
      ImageFilter.blur(sigmaX: blurLight, sigmaY: blurLight);
}
