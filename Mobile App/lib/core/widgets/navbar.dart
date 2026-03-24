import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../features/auth/login/provider/auth_provider.dart';
import 'package:flutter_svg/flutter_svg.dart';
import '../constants/app_asset_constants.dart';
import '../constants/appcolor_constants.dart';
import '../../../../core/provider/language_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../utils/app_logger.dart';
import '../theme/glass_theme.dart';
import '../theme/theme_provider.dart';

/// Glass-effect AppBar with backdrop blur and theme-aware colors.
/// Adapts to dark/light mode automatically using Theme.of(context).
class Navbar extends ConsumerStatefulWidget implements PreferredSizeWidget {
  final String initialLanguage;

  const Navbar({
    Key? key,
    this.initialLanguage = 'ID',
  }) : super(key: key);

  @override
  ConsumerState<Navbar> createState() => _NavbarState();

  @override
  Size get preferredSize => const Size.fromHeight(80.0);
}

class _NavbarState extends ConsumerState<Navbar> {
  late String selectedLanguage;
  bool isDropdownOpen = false;

  @override
  void initState() {
    super.initState();
    selectedLanguage = widget.initialLanguage;
  }

  @override
  Widget build(BuildContext context) {
    final authState = ref.watch(authProvider);
    final localizations = AppLocalizations.of(context)!;
    final currentLocale = ref.watch(localeProvider);
    // Detect dark/light mode from theme
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        // Glass container with backdrop blur
        ClipRect(
          child: BackdropFilter(
            filter: GlassTheme.standardBlur,
            child: Container(
              decoration: BoxDecoration(
                // Semi-transparent glass surface
                color: isDark
                    ? GlassTheme.glassSurfaceDark
                    : GlassTheme.glassSurfaceLight,
                border: Border(
                  bottom: BorderSide(
                    color: isDark
                        ? GlassTheme.glassBorderDark
                        : GlassTheme.glassBorderLight,
                    width: GlassTheme.borderWidth,
                  ),
                ),
              ),
              child: AppBar(
                backgroundColor: Colors.transparent,
                elevation: 0,
                scrolledUnderElevation: 0,
              leading: Padding(
                padding: const EdgeInsets.symmetric(horizontal: 3),
                child: Row(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    SizedBox(
                      height: 70,
                      width: 70,
                      child: Image.asset(
                        AppImage.logo,
                        fit: BoxFit.contain,
                      ),
                    ),
                    if (authState.isLoggedIn)
                      RichText(
                        text: TextSpan(
                          style: const TextStyle(
                            fontSize: 18,
                            fontWeight: FontWeight.bold,
                          ),
                          children: [
                            TextSpan(
                              text: 'Ulin ',
                              style: TextStyle(
                                color: isDark
                                    ? AppColors.accentTeal
                                    : AppColors.secondaryColor,
                              ),
                            ),
                            TextSpan(
                              text: 'Mahoni',
                              style: TextStyle(
                                color: isDark
                                    ? AppColors.accentGreen
                                    : AppColors.primaryColor,
                              ),
                            ),
                          ],
                        ),
                      ),
                  ],
                ),
              ),
              leadingWidth: authState.isLoggedIn ? 200 : 80,
              actions: [
                // Language Switcher with glass pill style
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 8),
                  child: Container(
                    padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                    decoration: BoxDecoration(
                      color: isDark
                          ? Colors.white.withValues(alpha: 0.08)
                          : Colors.white.withValues(alpha: 0.5),
                      border: Border.all(
                        color: isDark
                            ? Colors.white.withValues(alpha: 0.15)
                            : Colors.grey.shade300,
                        width: 1,
                      ),
                      borderRadius: BorderRadius.circular(20),
                    ),
                    child: DropdownButtonHideUnderline(
                      child: DropdownButton<String>(
                        value: currentLocale.languageCode.toUpperCase(),
                        onChanged: (String? newValue) {
                          if (newValue != null) {
                            final newLocale = newValue == 'ID' ? const Locale('id') : const Locale('en');
                            ref.read(localeProvider.notifier).state = newLocale;
                            setState(() {
                              isDropdownOpen = false;
                            });
                          }
                        },
                        onTap: () {
                          setState(() {
                            isDropdownOpen = !isDropdownOpen;
                          });
                        },
                        items: [
                          DropdownMenuItem(
                            value: 'ID',
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Image.network(
                                  AppImage.indonesiaFlagurl,
                                  width: 24,
                                  height: 24,
                                  loadingBuilder: (context, child, loadingProgress) {
                                    if (loadingProgress == null) return child;
                                    return const SizedBox(
                                      width: 24,
                                      height: 24,
                                      child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                                    );
                                  },
                                  errorBuilder: (context, error, stackTrace) => const Icon(Icons.flag, size: 24),
                                ),
                                const SizedBox(width: 6),
                                Text('ID', style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                  color: isDark ? AppColors.fontColorDark : Colors.black,
                                )),
                              ],
                            ),
                          ),
                          DropdownMenuItem(
                            value: 'EN',
                            child: Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                Image.network(
                                  AppImage.usaFlagurl,
                                  width: 24,
                                  height: 24,
                                  loadingBuilder: (context, child, loadingProgress) {
                                    if (loadingProgress == null) return child;
                                    return const SizedBox(
                                      width: 24,
                                      height: 24,
                                      child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
                                    );
                                  },
                                  errorBuilder: (context, error, stackTrace) => const Icon(Icons.flag, size: 24),
                                ),
                                const SizedBox(width: 6),
                                Text('EN', style: TextStyle(
                                  fontSize: 14,
                                  fontWeight: FontWeight.w500,
                                  color: isDark ? AppColors.fontColorDark : Colors.black,
                                )),
                              ],
                            ),
                          ),
                        ],
                        icon: Icon(
                          Icons.arrow_drop_down,
                          color: isDark ? AppColors.accentGreen : const Color(0xFF006400),
                          size: 20,
                        ),
                        dropdownColor: isDark
                            ? AppColors.surfaceDarkElevated
                            : Colors.white,
                        isDense: true,
                      ),
                    ),
                  ),
                ),
                // Dark/Light mode toggle — compact icon button
                GestureDetector(
                  onTap: () => ref.read(themeProvider.notifier).toggle(),
                  child: Icon(
                    isDark ? Icons.light_mode_outlined : Icons.dark_mode_outlined,
                    color: isDark ? AppColors.accentGreen : Colors.grey.shade600,
                    size: 20,
                  ),
                ),
                Padding(
                  padding: const EdgeInsets.only(left: 6, right: 16),
                  child: Row(
                    children: [

                      if (!authState.isLoggedIn) ...[
                        TextButton(
                          onPressed: () {
                            AppLogger.d('Login Pressed via Navbar', 'NAVBAR');
                            context.push('/login');
                          },
                          child: Text(
                            localizations.loginButton,
                            style: TextStyle(
                              fontWeight: FontWeight.w400,
                              color: isDark ? AppColors.fontColorDark : Colors.black,
                            ),
                          ),
                        ),
                        SizedBox(
                          height: 30,
                          child: ElevatedButton(
                            onPressed: () {
                              AppLogger.d('Register Pressed via Navbar', 'NAVBAR');
                              context.push('/register');
                            },
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppColors.primaryColor,
                              textStyle: const TextStyle(
                                fontWeight: FontWeight.w400,
                                color: Colors.white,
                              ),
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              shape: RoundedRectangleBorder(
                                borderRadius: BorderRadius.circular(5),
                              ),
                            ),
                            child: Text(
                              localizations.registerButton,
                              style: const TextStyle(
                                color: Colors.white,
                                fontSize: 12,
                              ),
                            ),
                          ),
                        ),
                      ] else ...[



























                      ],
                    ],
                  ),
                ),
              ],
            ),
            ),
          ),
        ),
      ],
    );
  }
}
