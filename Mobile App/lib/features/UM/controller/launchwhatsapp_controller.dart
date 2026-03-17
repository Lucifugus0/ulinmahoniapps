import 'package:url_launcher/url_launcher.dart';
import 'package:flutter/rendering.dart';
import '../../../core/constants/contact_constant.dart';

void launchWhatsApp() async {
  final phone = Contact.phone;
  final message = Uri.encodeComponent('Halo pak/bu customer service');
  final url = 'https://wa.me/$phone?text=$message';
  _launchUrl(url);
}

void _launchUrl(String url) async {
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