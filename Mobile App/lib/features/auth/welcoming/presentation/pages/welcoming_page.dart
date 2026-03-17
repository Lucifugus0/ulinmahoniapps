import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:video_player/video_player.dart';
import 'package:shared_preferences/shared_preferences.dart';
import 'package:ulinmahoniapps/core/constants/app_asset_constants.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';
import 'package:ulinmahoniapps/features/auth/login/provider/auth_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class WelcomePage extends ConsumerStatefulWidget {
  const WelcomePage({super.key});

  @override
  ConsumerState<WelcomePage> createState() => _WelcomePageState();
}

class _WelcomePageState extends ConsumerState<WelcomePage> {
  late VideoPlayerController _controller;
  bool _isVideoInitialized = false;

  // UI Constants
  static const double _logoSize = 120.0;
  static const double _brandNameFontSize = 32.0;
  static const double _brandNameLetterSpacing = 2.0;
  static const double _buttonHeight = 56.0;
  static const double _buttonBorderRadius = 12.0;
  static const double _buttonFontSize = 18.0;
  static const double _buttonPadding = 32.0;
  static const double _signUpFontSize = 15.0;
  static const double _bottomPadding = 60.0;
  static const double _blurSigma = 3.0;
  static const double _darkOverlayOpacity = 0.3;
  static const double _gradientOpacity = 0.8;
  static const double _textOpacity = 0.9;

  @override
  void initState() {
    super.initState();
    AppLogger.d('🚀 WelcomePage: initState called', 'WELCOME-PAGE');
    _checkFirstTime();
    _initializeVideo();
  }

  Future<void> _checkFirstTime() async {
    AppLogger.d('🔍 WelcomePage: Checking authentication status', 'WELCOME-PAGE');
    try {
      final prefs = await SharedPreferences.getInstance();

      // Check for token and user data in SharedPreferences
      final token = prefs.getString('auth_token');
      final userJson = prefs.getString('user_profile');

      AppLogger.i('📋 WelcomePage: Token exists = ${token != null && token.isNotEmpty}', 'WELCOME-PAGE');
      AppLogger.i('📋 WelcomePage: User data exists = ${userJson != null && userJson.isNotEmpty}', 'WELCOME-PAGE');

      // Check auth state from provider
      final authState = ref.read(authProvider);
      final isLoggedIn = authState.isLoggedIn;
      final hasUser = authState.user.value != null;

      AppLogger.i('📋 WelcomePage: Auth provider isLoggedIn = $isLoggedIn', 'WELCOME-PAGE');
      AppLogger.i('📋 WelcomePage: Auth provider hasUser = $hasUser', 'WELCOME-PAGE');

      // Check if BOTH token AND user data exist in SharedPreferences
      final hasToken = token != null && token.isNotEmpty;
      final hasUserData = userJson != null && userJson.isNotEmpty;

      if (hasToken && hasUserData && mounted) {
        AppLogger.s('✅ WelcomePage: User is authenticated, navigating to login page for biometric', 'WELCOME-PAGE');
        AppLogger.d('   Token: ${token.substring(0, token.length > 20 ? 20 : token.length)}...', 'WELCOME-PAGE');
        AppLogger.d('   User ID: ${authState.user.value?.id}', 'WELCOME-PAGE');
        // User has Remember Me enabled, go to login page for biometric auth
        context.go('/login');
      } else {
        AppLogger.i('👋 WelcomePage: No authentication found, showing welcome page', 'WELCOME-PAGE');
        if (!hasToken) {
          AppLogger.d('   Reason: No token found', 'WELCOME-PAGE');
        }
        if (!hasUserData) {
          AppLogger.d('   Reason: No user data found', 'WELCOME-PAGE');
        }
      }
    } catch (e, stackTrace) {
      AppLogger.e('❌ WelcomePage: Error checking authentication', e, stackTrace, 'WELCOME-PAGE');
      // On error, show welcome page (safe default)
      AppLogger.w('⚠️ WelcomePage: Showing welcome page due to error', 'WELCOME-PAGE');
    }
  }

  Future<void> _initializeVideo() async {
    AppLogger.d('🎥 WelcomePage: Initializing video player', 'WELCOME-PAGE');
    try {
      _controller = VideoPlayerController.asset(AppVideo.homeVideo)
        ..initialize().then((_) {
          if (mounted) {
            setState(() {
              _isVideoInitialized = true;
            });
            _controller.setLooping(true);
            _controller.setVolume(0); // Mute the video
            _controller.play();
            AppLogger.s('✅ WelcomePage: Video initialized and playing', 'WELCOME-PAGE');
          }
        }).catchError((error) {
          AppLogger.e('❌ WelcomePage: Error initializing video', error, StackTrace.current, 'WELCOME-PAGE');
        });
    } catch (e, stackTrace) {
      AppLogger.e('❌ WelcomePage: Failed to create video controller', e, stackTrace, 'WELCOME-PAGE');
    }
  }

  @override
  void dispose() {
    AppLogger.d('🗑️ WelcomePage: Disposing video controller', 'WELCOME-PAGE');
    _controller.dispose();
    super.dispose();
  }

  void _navigateToLogin() {
    AppLogger.d('🔐 WelcomePage: Navigating to login page', 'WELCOME-PAGE');
    if (!mounted) {
      AppLogger.w('⚠️ WelcomePage: Widget not mounted, skipping navigation', 'WELCOME-PAGE');
      return;
    }
    context.push('/home');
    AppLogger.i('➡️ WelcomePage: Navigation to login initiated', 'WELCOME-PAGE');
  }

  void _navigateToRegister() {
    AppLogger.d('📝 WelcomePage: Navigating to register page', 'WELCOME-PAGE');
    if (!mounted) {
      AppLogger.w('⚠️ WelcomePage: Widget not mounted, skipping navigation', 'WELCOME-PAGE');
      return;
    }
    context.push('/register');
    AppLogger.i('➡️ WelcomePage: Navigation to register initiated', 'WELCOME-PAGE');
  }

  @override
  Widget build(BuildContext context) {
    final size = MediaQuery.of(context).size;
    final localizations = AppLocalizations.of(context)!;
    AppLogger.d('🎨 WelcomePage: Building UI (video initialized: $_isVideoInitialized)', 'WELCOME-PAGE');

    return Scaffold(
      body: Stack(
        children: [
          // Video Background
          if (_isVideoInitialized)
            SizedBox.expand(
              child: FittedBox(
                fit: BoxFit.cover,
                child: SizedBox(
                  width: _controller.value.size.width,
                  height: _controller.value.size.height,
                  child: VideoPlayer(_controller),
                ),
              ),
            )
          else
            // Fallback gradient while video is loading
            Container(
              decoration: BoxDecoration(
                gradient: LinearGradient(
                  begin: Alignment.topCenter,
                  end: Alignment.bottomCenter,
                  colors: [
                    AppColors.primaryColor.withValues(alpha: _gradientOpacity),
                    AppColors.secondaryColor.withValues(alpha: _gradientOpacity),
                  ],
                ),
              ),
            ),

          // Blur overlay
          BackdropFilter(
            filter: ImageFilter.blur(sigmaX: _blurSigma, sigmaY: _blurSigma),
            child: Container(
              color: AppColors.black.withValues(alpha: _darkOverlayOpacity),
            ),
          ),

          // Content
          SafeArea(
            child: SizedBox(
              height: size.height,
              width: size.width,
              child: Column(
                mainAxisAlignment: MainAxisAlignment.spaceBetween,
                children: [
                  const Spacer(),

                  // Logo and Brand Name Section
                  Column(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      // Logo without background container
                      Image.asset(
                        AppImage.logo,
                        width: _logoSize,
                        height: _logoSize,
                        fit: BoxFit.contain,
                      ),
                      const SizedBox(height: 24),

                      // Brand Name: Primary & Secondary color with white shadow outline
                      RichText(
                        text: TextSpan(
                          style: TextStyle(
                            fontSize: _brandNameFontSize,
                            fontWeight: FontWeight.bold,
                            letterSpacing: _brandNameLetterSpacing,
                            shadows: [
                              // White shadow outline effect (multiple shadows for thickness)
                              Shadow(
                                offset: const Offset(-1.5, -1.5),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(1.5, -1.5),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(1.5, 1.5),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(-1.5, 1.5),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(0, -2),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(0, 2),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(-2, 0),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                              Shadow(
                                offset: const Offset(2, 0),
                                color: AppColors.white,
                                blurRadius: 0,
                              ),
                            ],
                          ),
                          children: const [
                            TextSpan(
                              text: 'ULIN ',
                              style: TextStyle(
                                color: AppColors.secondaryColor,
                              ),
                            ),
                            TextSpan(
                              text: 'MAHONI',
                              style: TextStyle(
                                color: AppColors.primaryColor,
                              ),
                            ),
                          ],
                        ),
                      ),
                      const SizedBox(height: 12),

                      // Tagline
                      Text(
                        localizations.welcomeTagline,
                        style: TextStyle(
                          fontSize: 16,
                          color: AppColors.white.withValues(alpha: 0.9),
                          fontStyle: FontStyle.italic,
                        ),
                      ),
                    ],
                  ),

                  const Spacer(),

                  // Buttons Section
                  Padding(
                    padding: const EdgeInsets.symmetric(horizontal: _buttonPadding),
                    child: Column(
                      children: [
                        // Welcome message above button
                        Text(
                          localizations.welcomeMessage,
                          textAlign: TextAlign.center,
                          style: TextStyle(
                            fontSize: 18,
                            color: AppColors.white.withValues(alpha: 0.95),
                            fontWeight: FontWeight.w500,
                            height: 1.4,
                          ),
                        ),
                        const SizedBox(height: 32),

                        // "Get Started" Button (Primary - goes to Login)
                        SizedBox(
                          width: double.infinity,
                          height: _buttonHeight,
                          child: ElevatedButton(
                            onPressed: _navigateToLogin,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.primaryColor,
                              foregroundColor: AppColors.white,
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(_buttonBorderRadius),
                              ),
                              elevation: 0,
                            ),
                            child: Text(
                              localizations.welcomeGetStartedButton,
                              style: const TextStyle(
                                fontSize: _buttonFontSize,
                                fontWeight: FontWeight.w600,
                                letterSpacing: 0.5,
                              ),
                            ),
                          ),
                        ),

                        const SizedBox(height: 20),

                        // "Sign Up" Link
                        Row(
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            Text(
                              localizations.welcomeSignUpPrompt,
                              style: TextStyle(
                                color: AppColors.white.withValues(alpha: _textOpacity),
                                fontSize: _signUpFontSize,
                              ),
                            ),
                            GestureDetector(
                              onTap: _navigateToRegister,
                              child: Text(
                                localizations.welcomeSignUpButton,
                                style: const TextStyle(
                                  color: AppColors.secondaryColor,
                                  fontSize: _signUpFontSize,
                                  fontWeight: FontWeight.bold,
                                  decoration: TextDecoration.underline,
                                  decorationColor: AppColors.secondaryColor,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: _bottomPadding),
                ],
              ),
            ),
          ),
        ],
      ),
    );
  }
}
