import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';

/// Riverpod 3.x Notifier that manages the app locale.
/// Migrated from StateProvider to Notifier for Riverpod 3.x compatibility.
class LocaleNotifier extends Notifier<Locale> {
  /// Returns the initial locale (Indonesian) via the build method
  @override
  Locale build() => const Locale('id');

  /// Update the current locale
  void setLocale(Locale locale) {
    state = locale;
  }
}

/// Global locale provider — migrated from StateProvider to NotifierProvider
final localeProvider = NotifierProvider<LocaleNotifier, Locale>(LocaleNotifier.new);
