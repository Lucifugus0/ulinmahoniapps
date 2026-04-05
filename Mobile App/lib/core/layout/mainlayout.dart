import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/widgets/navbar.dart';
import 'package:ulinmahoniapps/core/widgets/bottomnavbar.dart';
import '../widgets/bottomcontactbar.dart';
import '../widgets/leafy_background.dart';

/// Main scaffold wrapper for all pages.
/// Uses Theme.of(context) for background color so it responds to dark/light mode.
class MainLayout extends StatelessWidget {
  final int currentIndex;
  final Widget child;
  final bool showBottomNav;
  final bool showNavBar;
  final bool showContactBar;
  final bool bottomcontactbar_pesansekarang;
  final Color? backgroundColor;
  final Map<String, dynamic>? bottomcontactbar_data;
  final bool bottomcontactbar_buttonenabled;
  final VoidCallback? bottomcontactbar_buttonpressed;
  final String bottomcontactbar_price;
  final String? bottomcontactbar_renttype;
  final String? bottomcontactbar_warningtext;
  final String? bottomcontactbar_pricelabel;

  const MainLayout({
    Key? key,
    required this.currentIndex,
    required this.child,
    this.showBottomNav = true,
    this.showNavBar = true,
    this.showContactBar = false,
    this.bottomcontactbar_pesansekarang = false,
    this.backgroundColor,
    this.bottomcontactbar_data,
    this.bottomcontactbar_buttonenabled = true,
    this.bottomcontactbar_buttonpressed,
    this.bottomcontactbar_price = "100.000",
    this.bottomcontactbar_renttype = "Hari",
    this.bottomcontactbar_warningtext,
    this.bottomcontactbar_pricelabel,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    // Use provided backgroundColor, or fall back to scaffold background from theme
    final bgColor = backgroundColor ?? Theme.of(context).scaffoldBackgroundColor;

    return Scaffold(
      backgroundColor: bgColor,

      // Allow body to extend behind navbar for glass blur effect
      extendBody: true,

      appBar: showNavBar ? const Navbar(initialLanguage: 'ID') : null,

      body: Stack(
        children: [
          // Leafy background overlay — decorative, non-interactive
          const LeafyBackground(),
          // Actual page content
          child,
        ],
      ),

      // Theme wrapper to remove default white canvas behind bottom nav
      bottomNavigationBar: Theme(
        data: Theme.of(context).copyWith(
          canvasColor: Colors.transparent,
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min,
          mainAxisAlignment: MainAxisAlignment.end,
          children: [
            if (showContactBar)
              BottomContactBar(
                showBookingButton: bottomcontactbar_pesansekarang,
                isButtonEnabled: bottomcontactbar_buttonenabled,
                onButtonPressed: bottomcontactbar_buttonpressed,
                roomData: bottomcontactbar_data,
                priceText: bottomcontactbar_price,
                durationType: bottomcontactbar_renttype,
                warningText: bottomcontactbar_warningtext,
                priceLabel: bottomcontactbar_pricelabel,
              ),

            // Small gap if both ContactBar and BottomNav are visible
            if (showContactBar && showBottomNav)
              const SizedBox(height: 10),

            if (showBottomNav)
              BottomNavBar(currentIndex: currentIndex),
          ],
        ),
      ),
    );
  }
}
