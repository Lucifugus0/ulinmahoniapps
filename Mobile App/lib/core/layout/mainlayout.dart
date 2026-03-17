import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/widgets/navbar.dart';
import 'package:ulinmahoniapps/core/widgets/bottomnavbar.dart';
import '../widgets/bottomcontactbar.dart';
import '../constants/appcolor_constants.dart';

class MainLayout extends StatelessWidget {
  final int currentIndex;
  final Widget child;
  final bool showBottomNav;
  final bool showNavBar;
  final bool showContactBar;
  final bool bottomcontactbar_pesansekarang;
  final Color backgroundColor;
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
    this.backgroundColor = AppColors.backgroundColor,
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

    return Scaffold(
      backgroundColor: backgroundColor,

      // 1. WAJIB: Agar konten body bisa tembus ke belakang Navbar
      extendBody: true,

      appBar: showNavBar ? const Navbar(initialLanguage: 'ID') : null,

      body: child,

      // 2. WAJIB: Theme Wrapper untuk menghilangkan background putih bawaan Scaffold
      bottomNavigationBar: Theme(
        data: Theme.of(context).copyWith(
          canvasColor: Colors.transparent, // Memastikan area sisa benar-benar bening
        ),
        child: Column(
          mainAxisSize: MainAxisSize.min, // Tinggi menyesuaikan konten
          mainAxisAlignment: MainAxisAlignment.end, // Selalu menempel di bawah
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

            // Jarak pemisah kecil jika ContactBar dan Navbar muncul bersamaan
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