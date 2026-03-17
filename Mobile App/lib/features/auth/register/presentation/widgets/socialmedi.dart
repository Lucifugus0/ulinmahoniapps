import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';

final Uri _url = Uri.parse('https://web.ulinmahoni.com/login');

Future<void> _launchUrl() async {
  if (!await launchUrl(_url)) {
    throw Exception('Could not launch $_url');
  }
}

GestureDetector socialMedia(String imagePath, {VoidCallback? onTap}) {
  return GestureDetector(
    onTap: _launchUrl, 
    child: Container(
      width: 64,
      height: 64,
      padding: EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.white,
        shape: BoxShape.circle,
        boxShadow: [
          BoxShadow(
            color: Colors.grey.withOpacity(0.3),
            spreadRadius: 1,
            blurRadius: 7,
            offset: Offset(0, 3), 
          ),
        ],
      ),
      child: Image.asset(imagePath, height: 48, width: 48),
    ),
  );
}