import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// SharedPreferences key for persisting theme mode
const _themeKey = 'theme_mode';

/// Riverpod 3.x Notifier that manages the app theme mode (dark/light/system).
/// Persists the user's choice in SharedPreferences.
/// Default: ThemeMode.dark (matches the liquid glass dark-first design).
/// Migrated from StateNotifier to Notifier for Riverpod 3.x compatibility.
class ThemeNotifier extends Notifier<ThemeMode> {
  /// Returns the initial theme mode and triggers async load from SharedPreferences
  @override
  ThemeMode build() {
    _loadTheme();
    return ThemeMode.dark;
  }

  /// Load saved theme from SharedPreferences on startup
  Future<void> _loadTheme() async {
    final prefs = await SharedPreferences.getInstance();
    final value = prefs.getString(_themeKey);
    if (value != null) {
      switch (value) {
        case 'light':
          state = ThemeMode.light;
          break;
        case 'dark':
          state = ThemeMode.dark;
          break;
        case 'system':
          state = ThemeMode.system;
          break;
      }
    }
  }

  /// Set theme and persist to SharedPreferences
  Future<void> setTheme(ThemeMode mode) async {
    state = mode;
    final prefs = await SharedPreferences.getInstance();
    await prefs.setString(_themeKey, mode.name);
  }

  /// Toggle between dark and light mode
  Future<void> toggle() async {
    final newMode = state == ThemeMode.dark ? ThemeMode.light : ThemeMode.dark;
    await setTheme(newMode);
  }

  /// Check if current mode is dark (resolves system mode using platform brightness)
  bool isDark(BuildContext context) {
    if (state == ThemeMode.system) {
      return MediaQuery.platformBrightnessOf(context) == Brightness.dark;
    }
    return state == ThemeMode.dark;
  }
}

/// Global theme mode provider — migrated from StateNotifierProvider to NotifierProvider
final themeProvider = NotifierProvider<ThemeNotifier, ThemeMode>(ThemeNotifier.new);
