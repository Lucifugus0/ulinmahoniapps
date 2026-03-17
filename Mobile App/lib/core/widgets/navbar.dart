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
    final user = authState.user.value;
    final localizations = AppLocalizations.of(context)!;
    final currentLocale = ref.watch(localeProvider);

    return Column(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          decoration: BoxDecoration(
            color: Colors.white,
            border: Border(
              bottom: BorderSide(
                color: Colors.grey.shade300,
                width: 1,
              ),
            ),
          ),
          child: AppBar(
            backgroundColor: Colors.white,
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
                    text: const TextSpan(
                      style: TextStyle(
                        fontSize: 18,
                        fontWeight: FontWeight.bold,
                      ),
                      children: [
                        TextSpan(
                          text: 'Ulin ',
                          style: TextStyle(color: AppColors.secondaryColor),
                        ),
                        TextSpan(
                          text: 'Mahoni',
                          style: TextStyle(color: AppColors.primaryColor),
                        ),
                      ],
                    ),
                  ),
              ],
            ),
          ),
          leadingWidth: authState.isLoggedIn ? 200 : 80,
          actions: [
            // Compact Language Switcher
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 8),
              child: Container(
                padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                decoration: BoxDecoration(
                  border: Border.all(color: Colors.grey.shade300, width: 1),
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
                            const Text('ID', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
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
                            const Text('EN', style: TextStyle(fontSize: 14, fontWeight: FontWeight.w500)),
                          ],
                        ),
                      ),
                    ],
                    icon: Icon(
                      Icons.arrow_drop_down,
                      color: const Color(0xFF006400),
                      size: 20,
                    ),
                    dropdownColor: Colors.white,
                    isDense: true,
                  ),
                ),
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 20),
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
                        style: const TextStyle(
                          fontWeight: FontWeight.w400,
                          color: Colors.black,
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
                          backgroundColor: const Color(0xFF134E3A),
                          textStyle: const TextStyle(
                            fontWeight: FontWeight.w400,
                            color: Colors.white,
                          ),
                          padding: EdgeInsets.symmetric(horizontal: 10),
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
      ],
    );
  }
}
