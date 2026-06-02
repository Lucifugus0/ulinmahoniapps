import 'package:flutter/material.dart';

/// App color constants with light mode, dark mode, and glass-specific palettes.
/// Original light-mode colors are preserved for API/backend compatibility.
class AppColors {
  // -- Original colors (light mode, unchanged for compatibility) --
  static const Color primaryColor = Color(0xFF134E3A);
  static const Color secondaryColor = Color(0xFF6B0101);
  static const Color backgroundColor = Color(0xFFFFFFFF);
  static const Color secondaryBackgroundColor = Color.fromARGB(255, 241, 241, 240);
  static const Color tertiaryBackgroundColor = Color.fromARGB(255, 244, 244, 240);
  static const Color black = Color(0xFF000000);
  static const Color white = Color(0xFFFFFFFF);
  static const Color fontcolor = Color(0xFF000000);
  static const Color blue = Color(0xFF2196F3);
  static const Color green = Color(0xFF4CAF50);
  static const Color orange = Color(0xFFFF9800);
  static const Color purple = Color(0xFF9C27B0);
  static const Color teal = Color(0xFF00BCD4);
  static const Color red = Color(0xFFF44336);
  static const Color amber = Color(0xFFFFC107);
  static const Color indigo = Color(0xFF3F51B5);

  // -- Dark mode background & surface colors (matching Frontend #111827) --
  /// Main dark background — matches Frontend's dark:bg-gray-900 (#111827)
  static const Color backgroundDark = Color(0xFF111827);

  /// Dark surface for cards and containers — matches Frontend's dark:bg-gray-800
  static const Color surfaceDark = Color(0xFF1F2937);

  /// Elevated dark surface (modals, sheets) — matches Frontend's dark:bg-gray-700
  static const Color surfaceDarkElevated = Color(0xFF374151);

  /// Light mode background alias
  static const Color backgroundLight = Color(0xFFF8F8FA);

  // -- Font colors per mode --
  static const Color fontColorLight = Color(0xFF1A1A1A);
  static const Color fontColorDark = Color(0xFFF5F5F7);

  /// Muted text color for dark mode
  static const Color fontColorDarkMuted = Color(0xFFAAAAAC);

  /// Muted text color for light mode
  static const Color fontColorLightMuted = Color(0xFF6B6B6D);

  // -- Glass-specific colors --
  /// Glass border for light mode
  static Color glassBorderLight = Colors.white.withValues(alpha: 0.35);

  /// Glass border for dark mode
  static Color glassBorderDark = Colors.white.withValues(alpha: 0.12);

  /// Glass surface for light mode
  static Color glassSurfaceLight = Colors.white.withValues(alpha: 0.65);

  /// Glass surface for dark mode — based on Frontend gray-800
  static Color glassSurfaceDark = const Color(0xFF1F2937).withValues(alpha: 0.70);

  // -- Brighter brand variants for UI text/icons --
  /// Slightly brighter green — for "Mahoni" text and bottom nav selected state
  static const Color primaryColorBright = Color(0xFF1F7A55);

  /// Slightly brighter red — for "Ulin" text in navbar
  static const Color secondaryColorBright = Color(0xFFB71C1C);

  // -- Adaptive primary: returns bright variant in dark mode, normal in light mode --
  /// Use this instead of [primaryColor] in widget builds where [BuildContext] is available.
  static Color primaryAdaptive(BuildContext context) =>
      Theme.of(context).brightness == Brightness.dark ? primaryColorBright : primaryColor;

  // -- Accent colors --
  /// Bright green accent for glass UI highlights
  static const Color accentGreen = Color(0xFF34C759);

  /// Accent teal for interactive elements in dark mode
  static const Color accentTeal = Color(0xFF64D2FF);
}
