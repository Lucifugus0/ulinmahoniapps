import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import '../../features/auth/login/provider/auth_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../features/home/presentation/widgets/section/searchfilter.dart';
// OLD UM DIALOG - Commented out but preserved
// import 'dialog/contactdialog.dart';

class BottomNavBar extends ConsumerWidget {
  final int currentIndex;
  final Map<String, dynamic>? extraData;

  const BottomNavBar({
    super.key,
    required this.currentIndex,
    this.extraData
  });

  void _showFilterDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (BuildContext context) {
        return const SearchFilterModal();
      },
    );
  }

  void _onTap(BuildContext context, int index, bool isLoggedIn) {
    switch (index) {
      case 0:
        context.go('/home');
        break;
      case 1:
        if (isLoggedIn) {
          context.go('/mybooking');
        } else {
          context.push('/login');
        }
        break;
      case 2:
        _showFilterDialog(context);
        break;
      case 3:
        // OLD UM DIALOG - Commented out but preserved
        // showContactDialog(context);

        // NEW: Navigate to CS (Customer Service) page
        if (isLoggedIn) {
          context.go('/cs');
        } else {
          context.push('/login');
        }
        break;
      case 4:
        if (isLoggedIn) {
          context.go('/profile');
        } else {
          context.push('/login');
        }
        break;
    }
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    // --- 1. LOGIKA RESPONSIF ---
    final screenWidth = MediaQuery.of(context).size.width;
    final isSmallScreen = screenWidth < 360; // Deteksi HP layar kecil

    // Margin kiri kanan dinamis (5% dari lebar layar)
    final double horizontalMargin = screenWidth * 0.05;

    // Font lebih kecil (9 untuk normal, 8 untuk hp kecil)
    final double fontSize = isSmallScreen ? 8.0 : 9.0;

    // Ukuran icon sedikit menyesuaikan
    final double iconSize = isSmallScreen ? 20.0 : 24.0;

    final localizations = AppLocalizations.of(context)!;
    final authState = ref.watch(authProvider);
    final bool isLoggedIn = authState.isLoggedIn;

    return SafeArea(
      child: Container(
        margin: EdgeInsets.only(
            left: horizontalMargin,
            right: horizontalMargin,
            bottom: 12,
        ),
        height: 65,
        decoration: BoxDecoration(
          color: Colors.white,
          borderRadius: BorderRadius.circular(35),
          border: Border.all(
            color: Colors.grey.shade300,
            width: 1,
          ),
        ),
        child: Row(
          crossAxisAlignment: CrossAxisAlignment.center,
          children: [
            // --- KIRI (2 Item) ---
            Expanded(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  _buildNavItem(
                      context,
                      icon: Icons.home_outlined,
                      activeIcon: Icons.home,
                      label: localizations.homeLabel,
                      index: 0,
                      currentIndex: currentIndex,
                      activeColor: AppColors.primaryColor,
                      fontSize: fontSize,
                      iconSize: iconSize,
                      onTap: () => _onTap(context, 0, isLoggedIn)
                  ),
                  _buildNavItem(
                      context,
                      icon: Icons.calendar_today_outlined,
                      activeIcon: Icons.calendar_today,
                      label: localizations.myBookingLabel,
                      index: 1,
                      currentIndex: currentIndex,
                      activeColor: AppColors.primaryColor,
                      fontSize: fontSize,
                      iconSize: iconSize,
                      onTap: () => _onTap(context, 1, isLoggedIn)
                  ),
                ],
              ),
            ),

            // --- TENGAH (Search Button) ---
            SizedBox(
              width: 70,
              height: 70,
              child: Center(
                child: Transform.translate(
                  offset: const Offset(0, -20),
                  child: GestureDetector(
                    onTap: () => _onTap(context, 2, isLoggedIn),
                    child: Container(
                      width: 60,
                      height: 60,
                      decoration: BoxDecoration(
                        color: AppColors.primaryColor,
                        shape: BoxShape.circle,
                        boxShadow: [
                          BoxShadow(
                            color: AppColors.primaryColor.withOpacity(0.4),
                            blurRadius: 10,
                            offset: const Offset(0, 4),
                          ),
                        ],
                      ),
                      child: const Icon(
                        Icons.search,
                        color: Colors.white,
                        size: 28,
                      ),
                    ),
                  ),
                ),
              ),
            ),

            // --- KANAN (2 Item) ---
            Expanded(
              child: Row(
                mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                children: [
                  // OLD: Phone icon for UM dialog (preserved but commented)
                  // _buildNavItem(
                  //     context,
                  //     icon: Icons.phone_outlined,
                  //     activeIcon: Icons.phone,
                  //     label: localizations.umLabel,
                  //     index: 3,
                  //     currentIndex: currentIndex,
                  //     activeColor: AppColors.primaryColor,
                  //     fontSize: fontSize,
                  //     iconSize: iconSize,
                  //     onTap: () => _onTap(context, 3, isLoggedIn)
                  // ),

                  // NEW: Customer Service Chat
                  _buildNavItem(
                      context,
                      icon: Icons.chat_bubble_outline,
                      activeIcon: Icons.chat_bubble,
                      label: localizations.csLabel,
                      index: 3,
                      currentIndex: currentIndex,
                      activeColor: AppColors.primaryColor,
                      fontSize: fontSize,
                      iconSize: iconSize,
                      onTap: () => _onTap(context, 3, isLoggedIn)
                  ),
                  _buildNavItem(
                      context,
                      icon: Icons.person_outline,
                      activeIcon: Icons.person,
                      label: isLoggedIn ? localizations.profileLabel : localizations.loginButton,
                      index: 4,
                      currentIndex: currentIndex,
                      activeColor: AppColors.primaryColor,
                      fontSize: fontSize,
                      iconSize: iconSize,
                      onTap: () => _onTap(context, 4, isLoggedIn)
                  ),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildNavItem(
      BuildContext context, {
        required IconData icon,
        required IconData activeIcon,
        required String label,
        required int index,
        required int currentIndex,
        required Color activeColor,
        required double fontSize, // Parameter baru
        required double iconSize, // Parameter baru
        required VoidCallback onTap,
      }) {
    final bool isSelected = index == currentIndex;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              isSelected ? activeIcon : icon,
              color: isSelected ? activeColor : Colors.grey.shade500,
              size: iconSize, // Pakai ukuran responsif
            ),
            const SizedBox(height: 3),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? activeColor : Colors.grey.shade500,
                fontSize: fontSize, // Pakai font size responsif (9.0 atau 8.0)
                fontWeight: isSelected ? FontWeight.w600 : FontWeight.w400,
                height: 1.0,
              ),
            ),
          ],
        ),
      ),
    );
  }
}