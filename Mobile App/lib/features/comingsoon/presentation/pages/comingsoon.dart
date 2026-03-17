import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter/services.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/app_asset_constants.dart';
import '../../../../l10n/app_localizations.dart';

class ComingSoonPage extends StatelessWidget {
  const ComingSoonPage({super.key});
  @override
  Widget build(BuildContext context) {
    SystemChrome.setPreferredOrientations([DeviceOrientation.portraitUp, DeviceOrientation.portraitDown]);
    final screenSize = MediaQuery.of(context).size;
    final localizations = AppLocalizations.of(context)!;

    return MainLayout(
      currentIndex: 4,
      showBottomNav: false,
      showNavBar: false,
      child: SafeArea(
        child: LayoutBuilder(
          builder: (context, constraints) {
            return SingleChildScrollView(
              padding: const EdgeInsets.all(16),
              child: ConstrainedBox(
                constraints: BoxConstraints(
                  minHeight: constraints.maxHeight,
                ),
                child: IntrinsicHeight(
                  child: Column(
                    children: [
                      Align(
                        alignment: Alignment.centerLeft,
                        child: GestureDetector(
                          onTap: () => context.pop(),
                          child: const Icon(Icons.arrow_back),
                        ),
                      ),
                      Center(
                        child: Image.asset(
                          AppImage.logo,
                          width: screenSize.width * 0.7,
                          height: screenSize.width * 0.7,
                        ),
                      ),

                      Text(
                        localizations.comingSoonTitle1,
                        style: TextStyle(
                          fontSize: 45,
                          fontWeight: FontWeight.bold,
                          color: AppColors.secondaryColor,
                        ),
                      ),
                      Text(
                        localizations.comingSoonTitle2,
                        style: TextStyle(
                          fontSize: 45,
                          fontWeight: FontWeight.bold,
                          color: AppColors.primaryColor,
                        ),
                      ),

                      Padding(
                        padding: const EdgeInsets.symmetric(horizontal: 16.0),
                        child: Text(
                          localizations.comingSoonMessage,
                          textAlign: TextAlign.center,
                          style: const TextStyle(
                            fontSize: 16,
                            height: 1.5,
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
              ),
            );
          },
        ),
      ),
    );
  }
}
