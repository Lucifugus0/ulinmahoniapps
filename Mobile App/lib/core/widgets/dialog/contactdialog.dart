import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart'; // Import wajib untuk SVG
import '../../constants/contact_constant.dart';
import '../../constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../features/UM/controller/launchemail_controller.dart';
import '../../../features/UM/controller/launchwhatsapp_controller.dart';

Future<void> showContactDialog(BuildContext context) async {
  final localizations = AppLocalizations.of(context)!;
  const String targetEmail = Contact.email;
  const String targetPhone = Contact.phone;
  final String emailSubject = localizations.contactUsTitle;
  const darkGreen = Color(0xFF134E3A);

  Widget buildNetworkIcon(String url, IconData fallbackIcon) {
    return SvgPicture.network(
      url,
      width: 24, // Sesuaikan ukuran icon
      height: 24,
      // Hapus colorFilter jika ingin warna asli SVG
      colorFilter: const ColorFilter.mode(darkGreen, BlendMode.srcIn),
      placeholderBuilder: (BuildContext context) {
        // Ini adalah "Bantalan" saat loading
        return Icon(fallbackIcon, color: darkGreen);
      },
    );
  }

  showDialog(
    context: context,
    builder: (BuildContext dialogContext) {
      return AlertDialog(
        backgroundColor: Colors.white,
        title: Text(localizations.contactUsTitle, textAlign: TextAlign.center),
        contentPadding: const EdgeInsets.symmetric(vertical: 24, horizontal: 16),
        content: Column(
          mainAxisSize: MainAxisSize.min,
          children: [
            Image.asset(
              AppImage.logo,
              width: 80,
              height: 80,
            ),
            const SizedBox(height: 16),
            ListTile(
              leading: buildNetworkIcon(AppImage.whatsappIconUrl, Icons.perm_phone_msg_rounded),
              title: Text("+$targetPhone"),
              onTap: () {
                Navigator.of(dialogContext).pop();
                launchWhatsApp();
              },
            ),
            ListTile(
              leading: buildNetworkIcon(AppImage.emailIconUrl, Icons.email_rounded),
              title: Text(Contact.email),
              onTap: () {
                Navigator.of(dialogContext).pop();
                launchEmail(targetEmail, subject: emailSubject);
              },
            ),
          ],
        ),
        actionsAlignment: MainAxisAlignment.center,
        actions: [
          TextButton(
            onPressed: () => Navigator.of(dialogContext).pop(),
            child: Text(
              localizations.cancelButtonLabel,
              style: const TextStyle(
                color: Colors.black,
                fontWeight: FontWeight.w500,
              ),
            ),
          ),
        ],
      );
    },
  );
}