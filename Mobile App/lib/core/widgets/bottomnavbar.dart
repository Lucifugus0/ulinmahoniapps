import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import '../../features/auth/login/provider/auth_provider.dart';
import '../../features/customerservice/provider/ticket_provider.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../features/home/presentation/widgets/section/searchfilter.dart';
import '../theme/glass_theme.dart';
// OLD UM DIALOG - Commented out but preserved
// import 'dialog/contactdialog.dart';

/// Glass pill-shaped bottom navigation bar with backdrop blur.
/// Theme-aware: adapts colors for dark and light modes.
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
    // --- Responsive sizing ---
    final screenWidth = MediaQuery.of(context).size.width;
    final isSmallScreen = screenWidth < 360;
    final double horizontalMargin = screenWidth * 0.05;
    final double fontSize = isSmallScreen ? 8.0 : 9.0;
    final double iconSize = isSmallScreen ? 20.0 : 24.0;

    final localizations = AppLocalizations.of(context)!;
    final authState = ref.watch(authProvider);
    final bool isLoggedIn = authState.isLoggedIn;
    final userId = authState.user.value?.id;
    // Unread chat count for CS tab badge
    final chatUnread = (isLoggedIn && userId != null)
        ? ref.watch(chatUnreadCountProvider(userId)).valueOrNull ?? 0
        : 0;
    // Detect dark/light mode
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return SafeArea(
      child: Container(
        margin: EdgeInsets.only(
            left: horizontalMargin,
            right: horizontalMargin,
            // Reduced bottom margin to position nav bar lower on screen
            bottom: 0,
        ),
        // Extra height to allow the search button to extend above the bar
        height: 85,
        child: Stack(
          clipBehavior: Clip.none,
          alignment: Alignment.bottomCenter,
          children: [
            // Glass pill bar background (65px tall, at the bottom)
            Positioned(
              left: 0,
              right: 0,
              bottom: 0,
              height: 65,
              child: ClipRRect(
                borderRadius: BorderRadius.circular(GlassTheme.radiusXLarge),
                child: BackdropFilter(
                  filter: GlassTheme.heavyBlur,
                  child: Container(
                    decoration: BoxDecoration(
                      // More transparent glass surface for liquid glass effect
                      color: isDark
                          ? const Color(0xFF1F2937).withValues(alpha: 0.35)
                          : Colors.white.withValues(alpha: 0.40),
                      borderRadius: BorderRadius.circular(GlassTheme.radiusXLarge),
                      border: Border.all(
                        color: isDark
                            ? Colors.white.withValues(alpha: 0.15)
                            : Colors.white.withValues(alpha: 0.50),
                        width: GlassTheme.borderWidth,
                      ),
                      boxShadow: [
                        BoxShadow(
                          color: Colors.black.withValues(alpha: isDark ? 0.25 : 0.08),
                          blurRadius: 20,
                          offset: const Offset(0, 4),
                        ),
                      ],
                    ),
                    child: Row(
                      crossAxisAlignment: CrossAxisAlignment.center,
                      children: [
                        // --- LEFT (2 Items) ---
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
                                  activeColor: isDark ? AppColors.primaryColorBright : AppColors.primaryColor,
                                  fontSize: fontSize,
                                  iconSize: iconSize,
                                  isDark: isDark,
                                  onTap: () => _onTap(context, 0, isLoggedIn)
                              ),
                              _buildNavItem(
                                  context,
                                  icon: Icons.calendar_today_outlined,
                                  activeIcon: Icons.calendar_today,
                                  label: localizations.myBookingLabel,
                                  index: 1,
                                  currentIndex: currentIndex,
                                  activeColor: isDark ? AppColors.primaryColorBright : AppColors.primaryColor,
                                  fontSize: fontSize,
                                  iconSize: iconSize,
                                  isDark: isDark,
                                  onTap: () => _onTap(context, 1, isLoggedIn)
                              ),
                            ],
                          ),
                        ),

                        // Spacer for center button
                        const SizedBox(width: 70),

                        // --- RIGHT (2 Items) ---
                        Expanded(
                          child: Row(
                            mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                            children: [
                              // OLD: Phone icon for UM dialog (preserved but commented)
                              // _buildNavItem(...)

                              // Customer Service Chat with unread badge
                              _buildNavItem(
                                  context,
                                  icon: Icons.chat_bubble_outline,
                                  activeIcon: Icons.chat_bubble,
                                  label: localizations.csLabel,
                                  index: 3,
                                  currentIndex: currentIndex,
                                  activeColor: isDark ? AppColors.primaryColorBright : AppColors.primaryColor,
                                  fontSize: fontSize,
                                  iconSize: iconSize,
                                  isDark: isDark,
                                  onTap: () => _onTap(context, 3, isLoggedIn),
                                  badgeCount: chatUnread,
                              ),
                              _buildNavItem(
                                  context,
                                  icon: Icons.person_outline,
                                  activeIcon: Icons.person,
                                  label: isLoggedIn ? localizations.profileLabel : localizations.loginButton,
                                  index: 4,
                                  currentIndex: currentIndex,
                                  activeColor: isDark ? AppColors.primaryColorBright : AppColors.primaryColor,
                                  fontSize: fontSize,
                                  iconSize: iconSize,
                                  isDark: isDark,
                                  onTap: () => _onTap(context, 4, isLoggedIn)
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
              ),
            ),

            // Center search button — sits ABOVE the glass bar, not clipped
            Positioned(
              top: 0,
              child: GestureDetector(
                onTap: () => _onTap(context, 2, isLoggedIn),
                child: Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    // Dark mode: brighter green; Light mode: original dark green
                    color: isDark ? AppColors.primaryColorBright : AppColors.primaryColor,
                    shape: BoxShape.circle,
                    boxShadow: [
                      BoxShadow(
                        color: (isDark ? AppColors.primaryColorBright : AppColors.primaryColor).withValues(alpha: isDark ? 0.5 : 0.4),
                        blurRadius: 12,
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
          ],
        ),
      ),
    );
  }

  /// Builds a single nav item with icon and label.
  /// Theme-aware: inactive color changes based on dark/light mode.
  Widget _buildNavItem(
      BuildContext context, {
        required IconData icon,
        required IconData activeIcon,
        required String label,
        required int index,
        required int currentIndex,
        required Color activeColor,
        required double fontSize,
        required double iconSize,
        required bool isDark,
        required VoidCallback onTap,
        int badgeCount = 0,
      }) {
    final bool isSelected = index == currentIndex;
    final Color inactiveColor = isDark
        ? Colors.grey.shade400
        : Colors.grey.shade500;

    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(20),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            // Icon with optional unread badge
            Stack(
              clipBehavior: Clip.none,
              children: [
                Icon(
                  isSelected ? activeIcon : icon,
                  color: isSelected ? activeColor : inactiveColor,
                  size: iconSize,
                ),
                if (badgeCount > 0)
                  Positioned(
                    right: -8,
                    top: -4,
                    child: Container(
                      padding: const EdgeInsets.symmetric(horizontal: 4, vertical: 1),
                      decoration: BoxDecoration(
                        color: Colors.red,
                        borderRadius: BorderRadius.circular(10),
                      ),
                      constraints: const BoxConstraints(minWidth: 16, minHeight: 14),
                      child: Text(
                        badgeCount > 99 ? '99+' : '$badgeCount',
                        style: const TextStyle(color: Colors.white, fontSize: 9, fontWeight: FontWeight.bold),
                        textAlign: TextAlign.center,
                      ),
                    ),
                  ),
              ],
            ),
            const SizedBox(height: 3),
            Text(
              label,
              style: TextStyle(
                color: isSelected ? activeColor : inactiveColor,
                fontSize: fontSize,
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
