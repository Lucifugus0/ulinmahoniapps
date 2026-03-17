import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../constants/appcolor_constants.dart';
import '../../constants/app_asset_constants.dart';

class ErrorDisplayWidget extends StatelessWidget {
  final Object error;
  final StackTrace? stackTrace;
  final double? customHeight;

  const ErrorDisplayWidget({
    super.key,
    required this.error,
    this.stackTrace,
    this.customHeight,
  });

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!; 
    SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp, DeviceOrientation.portraitDown]);
    final screenSize = MediaQuery.of(context).size;

    final double widgetHeight = customHeight ?? screenSize.height * 0.3;

    return SizedBox(
      height: widgetHeight,
      child: SafeArea(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Image.asset(
              AppImage.logo,
              width: screenSize.width * 0.3,
              height: screenSize.width * 0.3,
            ),
            const SizedBox(height: 16),
            Text(
              localizations.errorDisplayTitle, 
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: AppColors.secondaryColor,
              ),
            ),
          ],
        ),
      ),
    );
  }
}