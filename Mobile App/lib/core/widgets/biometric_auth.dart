import 'package:flutter/material.dart';
import 'package:local_auth/local_auth.dart';
import 'package:flutter/services.dart';
import 'package:shared_preferences/shared_preferences.dart';

/// Biometric authentication service — manages Face ID / fingerprint auth.
/// Stores user preference in SharedPreferences:
///   - `biometric_enabled` (bool) — whether user opted in
///   - `biometric_opt_in_shown` (bool) — whether opt-in dialog was shown
class BiometricAuthService {
  final LocalAuthentication _auth = LocalAuthentication();

  static const String _enabledKey = 'biometric_enabled';
  static const String _optInShownKey = 'biometric_opt_in_shown';

  /// Check if biometric is enabled by user preference
  Future<bool> isEnabled() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_enabledKey) ?? false;
  }

  /// Set biometric enabled/disabled
  Future<void> setEnabled(bool enabled) async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_enabledKey, enabled);
  }

  /// Check if opt-in dialog has been shown
  Future<bool> wasOptInShown() async {
    final prefs = await SharedPreferences.getInstance();
    return prefs.getBool(_optInShownKey) ?? false;
  }

  /// Mark opt-in dialog as shown
  Future<void> setOptInShown() async {
    final prefs = await SharedPreferences.getInstance();
    await prefs.setBool(_optInShownKey, true);
  }

  /// Check if device supports biometrics
  Future<bool> isAvailable() async {
    try {
      final canCheck = await _auth.canCheckBiometrics;
      final isSupported = await _auth.isDeviceSupported();
      return canCheck && isSupported;
    } catch (_) {
      return false;
    }
  }

  /// Prompt biometric authentication (Face ID / fingerprint / passcode)
  /// Returns true on success, false on failure. Returns true if biometrics unavailable.
  Future<bool> authenticate() async {
    try {
      final available = await isAvailable();
      if (!available) {
        debugPrint("Biometrics not available on this device, allowing bypass.");
        return true;
      }

      // local_auth 3.x removed AuthenticationOptions — use named params directly
      final didAuthenticate = await _auth.authenticate(
        localizedReason: 'Verify your identity to continue',
      );

      return didAuthenticate;
    } on PlatformException catch (e) {
      debugPrint("Biometric PlatformException: ${e.message}");
      return true;
    } catch (e) {
      debugPrint("Biometric error: $e");
      return true;
    }
  }

  /// Legacy method — kept for profile update page compatibility
  Future<bool> authenticateOnLoad(BuildContext context) async {
    return authenticate();
  }

  /// Show opt-in dialog after first login — asks user to enable biometric
  /// Returns true if user chose to enable, false otherwise
  Future<bool> showOptInDialog(BuildContext context) async {
    final available = await isAvailable();
    if (!available) return false;

    // Determine biometric type label
    final biometrics = await _auth.getAvailableBiometrics();
    String biometricLabel = 'Biometric';
    if (biometrics.contains(BiometricType.face)) {
      biometricLabel = 'Face ID';
    } else if (biometrics.contains(BiometricType.fingerprint)) {
      biometricLabel = 'Fingerprint';
    }

    if (!context.mounted) return false;

    final result = await showDialog<bool>(
      context: context,
      barrierDismissible: false,
      builder: (ctx) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(16)),
        title: Row(
          children: [
            Icon(
              biometricLabel == 'Face ID' ? Icons.face : Icons.fingerprint,
              color: Theme.of(ctx).colorScheme.primary,
              size: 28,
            ),
            const SizedBox(width: 12),
            Expanded(
              child: Text(
                'Enable $biometricLabel?',
                style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold),
              ),
            ),
          ],
        ),
        content: Text(
          'Use $biometricLabel to quickly unlock the app next time you open it.',
          style: const TextStyle(fontSize: 14),
        ),
        actions: [
          TextButton(
            onPressed: () => Navigator.of(ctx).pop(false),
            child: const Text('Not Now'),
          ),
          ElevatedButton(
            onPressed: () => Navigator.of(ctx).pop(true),
            child: Text('Enable $biometricLabel'),
          ),
        ],
      ),
    );

    final enabled = result ?? false;
    await setEnabled(enabled);
    await setOptInShown();
    return enabled;
  }
}
