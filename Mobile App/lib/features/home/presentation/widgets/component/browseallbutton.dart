import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import '../../../../../core/utils/app_logger.dart';

class BrowseAllButton extends StatelessWidget {
  const BrowseAllButton({Key? key}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final screenWidth = MediaQuery.of(context).size.width;
    final buttonWidth = screenWidth * (7 / 8);
    final localizations = AppLocalizations.of(context)!;

    return Center(
      child: Container(
        width: buttonWidth,
        margin: const EdgeInsets.symmetric(horizontal: 16),
        child: ElevatedButton(
          onPressed: () {
            AppLogger.d(' ➡️Browse All Button Pressed', 'BROWSE-BTN');
            context.push('/browse-all');
          },
          style: ElevatedButton.styleFrom(
            // Use adaptive primary color to support light/dark theming
            backgroundColor: AppColors.primaryAdaptive(context),
            foregroundColor: Colors.white,
            elevation: 0,
            padding: const EdgeInsets.symmetric(vertical: 16, horizontal: 24),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
          ),
          child: Row(
            mainAxisAlignment: MainAxisAlignment.center,
            children: [
              const Icon(
                Icons.grid_view_rounded,
                color: Colors.white,
                size: 24,
              ),
              const SizedBox(width: 12),
              Text(
                localizations.browseAll,
                style: const TextStyle(
                  color: Colors.white,
                  fontSize: 16,
                  fontWeight: FontWeight.w600,
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
