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
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 2),
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
                  _buildFlagImage(AppImage.indonesiaFlagurl, 'ID'),
                  const SizedBox(width: 8),
                  const Text('ID'),
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
                  const Text('EN'),
                ],
              ),
            ),
          ],
          icon: Icon(
            isDropdownOpen ? Icons.arrow_drop_up : Icons.arrow_drop_down,
            color: widget.iconColor,
          ),
          dropdownColor: Colors.white,
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