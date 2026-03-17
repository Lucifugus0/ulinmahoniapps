import 'package:flutter/material.dart';
import 'package:local_auth/local_auth.dart';
import 'package:flutter/services.dart';

class BiometricAuthService {
  final LocalAuthentication _auth = LocalAuthentication();

  Future<bool> authenticateOnLoad(BuildContext context) async {
    try {
      
      final bool canAuthenticate = await _auth.canCheckBiometrics;
      final bool isDeviceSupported = await _auth.isDeviceSupported();

      
      if (!canAuthenticate || !isDeviceSupported) {
        debugPrint("Biometrik tidak tersedia di perangkat ini, lanjut tanpa autentikasi.");
        return true;
      }

      
      final bool didAuthenticate = await _auth.authenticate(
        localizedReason: 'Gunakan sidik jari atau Face ID untuk melanjutkan',
        options: const AuthenticationOptions(
          stickyAuth: true,
          biometricOnly: false, 
        ),
      );

      return didAuthenticate;
    } on PlatformException catch (e) {
      
      debugPrint("Error saat autentikasi biometrik (PlatformException): ${e.message}");
      return true;
    } catch (e) {
      
      debugPrint("Error tak terduga saat autentikasi biometrik: $e");
      return true;
    }
  }
}
