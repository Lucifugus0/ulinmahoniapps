import 'package:flutter/material.dart';
import 'package:url_launcher/url_launcher.dart';
import 'package:flutter_svg/flutter_svg.dart';

final Uri _url = Uri.parse('https://web.ulinmahoni.com/login');

Future<void> _launchUrl() async {
  if (!await launchUrl(_url)) {
    throw Exception('Could not launch $_url');
  }
}

GestureDetector socialMedia(String imagePath, {VoidCallback? onTap, bool isNetworkImage = false, bool isSvg = false}) {
  return GestureDetector(
    onTap: onTap ?? _launchUrl,
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
      child: _buildImage(imagePath, isNetworkImage, isSvg),
    ),
  );
}

Widget _buildImage(String imagePath, bool isNetworkImage, bool isSvg) {
  if (isNetworkImage && isSvg) {
    return SvgPicture.network(
      imagePath,
      height: 48,
      width: 48,
      placeholderBuilder: (context) => CircularProgressIndicator(
        strokeWidth: 2,
        valueColor: AlwaysStoppedAnimation<Color>(Colors.grey),
      ),
    );
  } else if (isNetworkImage) {
    return Image.network(
      imagePath,
      height: 48,
      width: 48,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return CircularProgressIndicator(
          strokeWidth: 2,
          valueColor: AlwaysStoppedAnimation<Color>(Colors.grey),
        );
      },
      errorBuilder: (context, error, stackTrace) {
        return Icon(Icons.error, size: 48, color: Colors.grey);
      },
    );
  } else {
    return Image.asset(imagePath, height: 48, width: 48);
  }
}