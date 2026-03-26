import 'package:flutter/material.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/constants/app_asset_constants.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/constants/appfontweight_constants.dart';
import '../../../../core/utils/app_logger.dart';
import 'package:go_router/go_router.dart';

class ErrorPage extends StatelessWidget {
  final String errorMessage;
  final StackTrace? errorStack;

  const ErrorPage({
    Key? key,
    required this.errorMessage,
    this.errorStack,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Log the technical error for debugging (developers only)
    AppLogger.e(
      'Error Page Displayed',
      errorMessage,
      errorStack,
      'ERROR-PAGE',
    );

    return MainLayout(
      currentIndex: 0,
      showNavBar: false,
      showBottomNav: false,
      child: Scaffold(
        backgroundColor: Colors.white,
        body: SafeArea(
          child: Center(
            child: SingleChildScrollView(
              padding: const EdgeInsets.all(24.0),
              child: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                children: [
                  // Logo
                  Image.asset(
                    AppImage.logo,
                    width: 120,
                    height: 120,
                  ),
                  const SizedBox(height: 40),

                  // Error icon
                  Icon(
                    Icons.error_outline,
                    size: 64,
                    color: Colors.red[400],
                  ),
                  const SizedBox(height: 24),

                  // User-friendly title
                  Text(
                    'Terjadi Kesalahan Teknis',
                    style: TextStyle(
                      fontSize: 22,
                      fontWeight: AppFontWeight.bold,
                      color: AppColors.fontcolor,
                    ),
                    textAlign: TextAlign.center,
                  ),
                  const SizedBox(height: 16),

                  // User-friendly message
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: 16.0),
                    child: Text(
                      'Mohon maaf, aplikasi mengalami gangguan teknis. Silakan coba kembali atau hubungi Customer Service kami untuk bantuan.',
                      style: TextStyle(
                        fontSize: 15,
                        color: Colors.grey[700],
                        height: 1.5,
                      ),
                      textAlign: TextAlign.center,
                    ),
                  ),
                  const SizedBox(height: 40),

                  // Back to home button
                  SizedBox(
                    width: double.infinity,
                    child: ElevatedButton.icon(
                      onPressed: () {
                        // Navigate to home
                        context.go('/home');
                      },
                      icon: const Icon(Icons.home, color: Colors.white),
                      label: const Text(
                        'Kembali ke Beranda',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
                      style: ElevatedButton.styleFrom(
                        // Use primaryAdaptive for dark/light mode compatibility
                        backgroundColor: AppColors.primaryAdaptive(context),
                        foregroundColor: Colors.white,
                        padding: const EdgeInsets.symmetric(vertical: 16),
                        shape: RoundedRectangleBorder(
                          borderRadius: BorderRadius.circular(12),
                        ),
                        elevation: 2,
                      ),
                    ),
                  ),
                ],
              ),
            ),
          ),
        ),
      ),
    );
  }
}
