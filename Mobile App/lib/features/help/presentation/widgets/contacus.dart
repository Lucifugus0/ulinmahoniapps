import 'package:flutter/material.dart';
import 'package:font_awesome_flutter/font_awesome_flutter.dart';
import 'package:go_router/go_router.dart';
import '../../controller/launchurl_controller.dart';

// font_awesome_flutter 11.x: FaIconData no longer extends IconData, accept dynamic
Widget _helpCard({
  required dynamic icons,
  required String name,
  VoidCallback? onTap,
}) {
  return Padding(
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 6),
    child: Material(
      color: const Color(0xFF134E3A),
      borderRadius: BorderRadius.circular(12),
      child: InkWell(
        onTap: onTap,
        borderRadius: BorderRadius.circular(12),
        child: Padding(
          padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 20),
          child: Row(
            children: [
              Icon(icons, color: Colors.white, size: 24),
              const SizedBox(width: 16),
              Expanded(
                child: Text(
                  name,
                  style: const TextStyle(
                    color: Colors.white,
                    fontSize: 18,
                    fontWeight: FontWeight.bold,
                  ),
                ),
              ),
              const Icon(Icons.arrow_forward_ios, color: Colors.white, size: 16),
            ],
          ),
        ),
      ),
    ),
  );
}

Widget help(BuildContext context) {
  return ListView(
    padding: const EdgeInsets.only(top: 16, bottom: 24),
    children: [
      _helpCard(
        icons: Icons.headphones_outlined,
        name: "Layanan Pelanggan",
        onTap: () {
          context.go('/um');
        },
      ),
      _helpCard(
        icons: Icons.phone_rounded,
        name: "WhatsApp",
        onTap: () {
          launchWhatsApp();
        },
      ),
      _helpCard(
        icons: Icons.language,
        name: "Website",
        onTap: () {
          launchCustomUrl("https://example.com"); 
        },
      ),
      _helpCard(
        icons: Icons.facebook,
        name: "Facebook",
        onTap: () {
          launchCustomUrl("https://facebook.com/yourpage"); 
        },
      ),
      _helpCard(
        icons: FontAwesomeIcons.twitter,
        name: "Twitter",
        onTap: () {
          launchCustomUrl("https://twitter.com/yourhandle"); 
        },
      ),
      _helpCard(
        icons: FontAwesomeIcons.instagram,
        name: "Instagram",
        onTap: () {
          launchCustomUrl("https://instagram.com/yourhandle"); 
        },
      ),
    ],
  );
}


