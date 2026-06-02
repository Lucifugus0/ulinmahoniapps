import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../constants/app_asset_constants.dart';
import '../provider/language_provider.dart';

class LanguageDropdown extends ConsumerStatefulWidget {
  final Color iconColor;

  const LanguageDropdown({
    Key? key,
    this.iconColor = const Color(0xFF006400), // Default color hijau tua
  }) : super(key: key);

  @override
  ConsumerState<LanguageDropdown> createState() => _LanguageDropdownState();
}

class _LanguageDropdownState extends ConsumerState<LanguageDropdown> {
  bool isDropdownOpen = false;

  @override
  Widget build(BuildContext context) {
    final currentLocale = ref.watch(localeProvider);
    final isDark = Theme.of(context).brightness == Brightness.dark;
    // Dropdown panel background and text color adapt to dark/light mode
    final dropdownBg = isDark ? const Color(0xFF1F2937) : Colors.white;
    final textColor = isDark ? Colors.white : Colors.black87;
    final arrowColor = isDark ? Colors.white70 : widget.iconColor;

    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 2),
      child: DropdownButtonHideUnderline(
        child: DropdownButton<String>(
          value: currentLocale.languageCode.toUpperCase(),
          onChanged: (String? newValue) {
            if (newValue != null) {
              // Map language code to Locale — supports ID, EN, ZH
              final newLocale = newValue == 'ID'
                  ? const Locale('id')
                  : newValue == 'ZH'
                      ? const Locale('zh')
                      : const Locale('en');
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
                  _buildFlagImage(AppImage.indonesiaFlagurl, 'ID'),
                  const SizedBox(width: 8),
                  Text('ID', style: TextStyle(color: textColor)),
                ],
              ),
            ),
            DropdownMenuItem(
              value: 'EN',
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  _buildFlagImage(AppImage.usaFlagurl, 'EN'),
                  const SizedBox(width: 8),
                  Text('EN', style: TextStyle(color: textColor)),
                ],
              ),
            ),
            // Simplified Chinese language option
            DropdownMenuItem(
              value: 'ZH',
              child: Row(
                mainAxisSize: MainAxisSize.min,
                children: [
                  _buildFlagImage(AppImage.chinaFlagurl, 'ZH'),
                  const SizedBox(width: 8),
                  Text('ZH', style: TextStyle(color: textColor)),
                ],
              ),
            ),
          ],
          icon: Icon(
            isDropdownOpen ? Icons.arrow_drop_up : Icons.arrow_drop_down,
            color: arrowColor,
          ),
          // Dropdown panel uses dark surface in dark mode
          dropdownColor: dropdownBg,
          elevation: 2,
        ),
      ),
    );
  }

  // Helper widget agar kode lebih rapi saat load gambar bendera
  Widget _buildFlagImage(String url, String altText) {
    return Image.network(
      url,
      width: 20,
      height: 20,
      loadingBuilder: (context, child, loadingProgress) {
        if (loadingProgress == null) return child;
        return const SizedBox(
          width: 20,
          height: 20,
          child: Center(child: CircularProgressIndicator(strokeWidth: 2)),
        );
      },
      errorBuilder: (context, error, stackTrace) => Text(altText),
    );
  }
}