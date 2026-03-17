import 'package:flutter/material.dart';
import '../../../../../core/utils/app_logger.dart';

/// Popup dialog shown when user tries to login with unverified email
/// Provides options to resend verification email or cancel
class EmailVerificationPopup extends StatelessWidget {
  final String email;
  final VoidCallback onResendEmail;
  final VoidCallback onCancel;

  const EmailVerificationPopup({
    super.key,
    required this.email,
    required this.onResendEmail,
    required this.onCancel,
  });

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      title: Row(
        children: [
          Icon(
            Icons.warning_amber_rounded,
            color: Colors.orange[700],
            size: 32,
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Text(
              'Email Belum Diverifikasi',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Akun Anda belum diverifikasi. Silakan periksa email Anda untuk verifikasi.',
            style: TextStyle(
              fontSize: 16,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 16),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey[100],
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.email_outlined,
                  color: Colors.grey[700],
                  size: 20,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    email,
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey[800],
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Tidak menerima email verifikasi?',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey,
            ),
          ),
        ],
      ),
      actions: [
        // Cancel button
        TextButton(
          onPressed: () {
            AppLogger.d('User cancelled email verification popup', 'EMAIL-VERIFICATION-POPUP');
            Navigator.pop(context);
            onCancel();
          },
          child: Text(
            'Batal',
            style: TextStyle(
              fontSize: 16,
              color: Colors.grey[700],
            ),
          ),
        ),
        // Resend email button
        ElevatedButton.icon(
          onPressed: () {
            AppLogger.d('User requested to resend verification email', 'EMAIL-VERIFICATION-POPUP');
            Navigator.pop(context);
            onResendEmail();
          },
          icon: const Icon(Icons.send, size: 18),
          label: const Text(
            'Kirim Ulang Email',
            style: TextStyle(fontSize: 16),
          ),
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.blue,
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
          ),
        ),
      ],
    );
  }

  /// Static method to show the popup
  static Future<void> show({
    required BuildContext context,
    required String email,
    required VoidCallback onResendEmail,
    required VoidCallback onCancel,
  }) async {
    return showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => EmailVerificationPopup(
        email: email,
        onResendEmail: onResendEmail,
        onCancel: onCancel,
      ),
    );
  }
}

/// Simple success popup shown after resending verification email
class EmailVerificationSentPopup extends StatelessWidget {
  final String email;

  const EmailVerificationSentPopup({
    super.key,
    required this.email,
  });

  @override
  Widget build(BuildContext context) {
    return AlertDialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      title: Row(
        children: [
          Icon(
            Icons.check_circle,
            color: Colors.green[700],
            size: 32,
          ),
          const SizedBox(width: 12),
          const Expanded(
            child: Text(
              'Email Terkirim',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
      content: Column(
        mainAxisSize: MainAxisSize.min,
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          const Text(
            'Email verifikasi telah dikirim ke:',
            style: TextStyle(
              fontSize: 16,
              height: 1.5,
            ),
          ),
          const SizedBox(height: 12),
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(
              color: Colors.grey[100],
              borderRadius: BorderRadius.circular(8),
            ),
            child: Row(
              children: [
                Icon(
                  Icons.email_outlined,
                  color: Colors.grey[700],
                  size: 20,
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: Text(
                    email,
                    style: TextStyle(
                      fontSize: 14,
                      fontWeight: FontWeight.w600,
                      color: Colors.grey[800],
                    ),
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
          const SizedBox(height: 16),
          const Text(
            'Silakan periksa inbox atau folder spam Anda.',
            style: TextStyle(
              fontSize: 14,
              color: Colors.grey,
            ),
          ),
        ],
      ),
      actions: [
        ElevatedButton(
          onPressed: () {
            AppLogger.d('User closed email sent popup', 'EMAIL-VERIFICATION-POPUP');
            Navigator.pop(context);
          },
          style: ElevatedButton.styleFrom(
            backgroundColor: Colors.green,
            foregroundColor: Colors.white,
            padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 12),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(8),
            ),
          ),
          child: const Text(
            'OK',
            style: TextStyle(fontSize: 16),
          ),
        ),
      ],
    );
  }

  /// Static method to show the popup
  static Future<void> show({
    required BuildContext context,
    required String email,
  }) async {
    return showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => EmailVerificationSentPopup(
        email: email,
      ),
    );
  }
}
