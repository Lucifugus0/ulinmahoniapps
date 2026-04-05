import 'package:flutter/material.dart';
import '../constants/appcolor_constants.dart';

/// Central ThemeData factory for light and dark modes.
/// Uses Inter font, glass-friendly colors, and rounded corners
/// to match the Apple liquid glass design language.
class AppTheme {
  AppTheme._();

  // -- Light Theme --
  static ThemeData get lightTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.light,
      fontFamily: 'Inter',
      primaryColor: AppColors.primaryColor,
      scaffoldBackgroundColor: AppColors.backgroundLight,
      canvasColor: Colors.transparent,

      // -- Color scheme --
      colorScheme: ColorScheme.light(
        primary: AppColors.primaryColor,
        secondary: AppColors.secondaryColor,
        surface: AppColors.backgroundLight,
        onPrimary: Colors.white,
        onSecondary: Colors.white,
        onSurface: AppColors.fontColorLight,
        outline: AppColors.glassBorderLight,
      ),

      // -- AppBar --
      appBarTheme: AppBarTheme(
        backgroundColor: Colors.white.withValues(alpha: 0.65),
        foregroundColor: AppColors.fontColorLight,
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        titleTextStyle: const TextStyle(
          fontFamily: 'Inter',
          fontSize: 18,
          fontWeight: FontWeight.bold,
          color: Colors.black,
        ),
      ),

      // -- Card --
      cardTheme: CardThemeData(
        color: Colors.white.withValues(alpha: 0.80),
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(
            color: Colors.white.withValues(alpha: 0.35),
            width: 0.5,
          ),
        ),
      ),

      // -- ElevatedButton --
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primaryColor,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          textStyle: const TextStyle(
            fontFamily: 'Inter',
            fontSize: 15,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // -- TextButton --
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.primaryColor,
          textStyle: const TextStyle(
            fontFamily: 'Inter',
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // -- Input fields --
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.grey.shade100.withValues(alpha: 0.7),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: Colors.grey.shade200,
            width: 0.5,
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: AppColors.primaryColor.withValues(alpha: 0.5),
            width: 1.0,
          ),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        hintStyle: TextStyle(
          color: Colors.grey.shade500,
          fontFamily: 'Inter',
        ),
      ),

      // -- Divider --
      dividerTheme: DividerThemeData(
        color: Colors.grey.shade300,
        thickness: 0.5,
      ),

      // -- Icon --
      iconTheme: const IconThemeData(
        color: Colors.black87,
        size: 22,
      ),

      // -- Bottom Sheet --
      bottomSheetTheme: BottomSheetThemeData(
        backgroundColor: Colors.white.withValues(alpha: 0.85),
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
      ),

      // -- Dialog --
      dialogTheme: DialogThemeData(
        backgroundColor: Colors.white.withValues(alpha: 0.92),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),

      // -- DatePicker --
      datePickerTheme: DatePickerThemeData(
        backgroundColor: Colors.white,
        headerBackgroundColor: AppColors.primaryColor,
        headerForegroundColor: Colors.white,
        surfaceTintColor: Colors.transparent,
        dayForegroundColor: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) return Colors.white;
          if (states.contains(WidgetState.disabled)) return Colors.grey.shade400;
          return Colors.black87;
        }),
        dayBackgroundColor: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) return AppColors.primaryColor;
          return null;
        }),
        todayForegroundColor: WidgetStateProperty.all(AppColors.primaryColor),
        todayBorder: BorderSide(color: AppColors.primaryColor),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }

  // -- Dark Theme --
  static ThemeData get darkTheme {
    return ThemeData(
      useMaterial3: true,
      brightness: Brightness.dark,
      fontFamily: 'Inter',
      // Use brighter primary so green is legible on dark backgrounds
      primaryColor: AppColors.primaryColorBright,
      scaffoldBackgroundColor: AppColors.backgroundDark,
      canvasColor: Colors.transparent,

      // -- Color scheme --
      colorScheme: ColorScheme.dark(
        primary: AppColors.primaryColorBright,
        secondary: AppColors.secondaryColor,
        surface: AppColors.surfaceDark,
        onPrimary: Colors.white,
        onSecondary: Colors.white,
        onSurface: AppColors.fontColorDark,
        outline: AppColors.glassBorderDark,
      ),

      // -- AppBar --
      appBarTheme: AppBarTheme(
        backgroundColor: AppColors.surfaceDark.withValues(alpha: 0.70),
        foregroundColor: AppColors.fontColorDark,
        elevation: 0,
        scrolledUnderElevation: 0,
        centerTitle: false,
        titleTextStyle: const TextStyle(
          fontFamily: 'Inter',
          fontSize: 18,
          fontWeight: FontWeight.bold,
          color: Colors.white,
        ),
      ),

      // -- Card --
      cardTheme: CardThemeData(
        color: AppColors.surfaceDark.withValues(alpha: 0.85),
        elevation: 0,
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(16),
          side: BorderSide(
            color: Colors.white.withValues(alpha: 0.12),
            width: 0.5,
          ),
        ),
      ),

      // -- ElevatedButton --
      elevatedButtonTheme: ElevatedButtonThemeData(
        style: ElevatedButton.styleFrom(
          backgroundColor: AppColors.primaryColorBright,
          foregroundColor: Colors.white,
          elevation: 0,
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(12),
          ),
          padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
          textStyle: const TextStyle(
            fontFamily: 'Inter',
            fontSize: 15,
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // -- TextButton --
      textButtonTheme: TextButtonThemeData(
        style: TextButton.styleFrom(
          foregroundColor: AppColors.primaryColorBright,
          textStyle: const TextStyle(
            fontFamily: 'Inter',
            fontWeight: FontWeight.w600,
          ),
        ),
      ),

      // -- Input fields --
      inputDecorationTheme: InputDecorationTheme(
        filled: true,
        fillColor: Colors.white.withValues(alpha: 0.08),
        border: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide.none,
        ),
        enabledBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: Colors.white.withValues(alpha: 0.12),
            width: 0.5,
          ),
        ),
        focusedBorder: OutlineInputBorder(
          borderRadius: BorderRadius.circular(12),
          borderSide: BorderSide(
            color: AppColors.primaryColorBright.withValues(alpha: 0.7),
            width: 1.0,
          ),
        ),
        contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
        hintStyle: TextStyle(
          color: Colors.grey.shade600,
          fontFamily: 'Inter',
        ),
      ),

      // -- Divider --
      dividerTheme: DividerThemeData(
        color: Colors.white.withValues(alpha: 0.12),
        thickness: 0.5,
      ),

      // -- Icon --
      iconTheme: const IconThemeData(
        color: Colors.white70,
        size: 22,
      ),

      // -- Bottom Sheet --
      bottomSheetTheme: BottomSheetThemeData(
        backgroundColor: AppColors.surfaceDark.withValues(alpha: 0.92),
        shape: const RoundedRectangleBorder(
          borderRadius: BorderRadius.vertical(top: Radius.circular(24)),
        ),
      ),

      // -- Dialog --
      dialogTheme: DialogThemeData(
        backgroundColor: AppColors.surfaceDarkElevated.withValues(alpha: 0.95),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(20),
        ),
      ),

      // -- DatePicker (dark) --
      datePickerTheme: DatePickerThemeData(
        backgroundColor: AppColors.surfaceDark,
        headerBackgroundColor: AppColors.surfaceDarkElevated,
        headerForegroundColor: Colors.white,
        surfaceTintColor: Colors.transparent,
        dayForegroundColor: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) return Colors.white;
          if (states.contains(WidgetState.disabled)) return Colors.grey.shade700;
          return Colors.white;
        }),
        dayBackgroundColor: WidgetStateProperty.resolveWith((states) {
          if (states.contains(WidgetState.selected)) return AppColors.primaryColorBright;
          return null;
        }),
        todayForegroundColor: WidgetStateProperty.all(AppColors.primaryColorBright),
        todayBorder: BorderSide(color: AppColors.primaryColorBright),
        yearForegroundColor: WidgetStateProperty.all(Colors.white),
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      ),
    );
  }
}
