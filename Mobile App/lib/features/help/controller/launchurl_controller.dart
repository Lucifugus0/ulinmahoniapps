import 'package:url_launcher/url_launcher.dart';
import 'package:flutter/rendering.dart';

void launchWhatsApp() async {
  final phone = '6285694608028';
  final message = Uri.encodeComponent('Halo, saya tertarik');
  final url = 'https://wa.me/$phone?text=$message';
  launchCustomUrl(url);
}

void launchCustomUrl(String url) async {
  final uri = Uri.parse(url);
  try {
    if (await canLaunchUrl(uri)) {
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    } else {
      debugPrint('Could not launch $url');
    }
  } catch (e) {
    debugPrint('Error launching $url: $e');
  }
}