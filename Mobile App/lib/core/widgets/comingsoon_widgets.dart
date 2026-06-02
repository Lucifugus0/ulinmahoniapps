import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter/services.dart';
import '../constants/appcolor_constants.dart';
import '../constants/app_asset_constants.dart';
import '../../l10n/app_localizations.dart';

class ComingSoonWidget extends StatelessWidget {
  final double? customHeight;

  const ComingSoonWidget({
    super.key,
    this.customHeight,
  });

  @override
  Widget build(BuildContext context) {
    SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp, DeviceOrientation.portraitDown]);
    final screenSize = MediaQuery.of(context).size;
    final double widgetHeight = customHeight ?? screenSize.height * 0.3;
    final localizations = AppLocalizations.of(context)!;

    return Container(
      height: widgetHeight,
      padding: const EdgeInsets.symmetric(horizontal: 16.0),
      child: Column(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Image.asset(
            AppImage.logo,
            width: screenSize.width * 0.25,
            height: screenSize.width * 0.25,
          ),
          const SizedBox(height: 8),
          Text(
            localizations.comingSoonTitle1,
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.secondaryColor,
            ),
          ),
          Text(
            localizations.comingSoonTitle2,
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: AppColors.primaryAdaptive(context),
            ),
          ),
          const SizedBox(height: 4),
          Text(
            localizations.comingSoonMessage,
            textAlign: TextAlign.center,
            style: const TextStyle(
              fontSize: 11,
              height: 1.5,
            ),
          ),
        ],
      ),
    );
  }
}