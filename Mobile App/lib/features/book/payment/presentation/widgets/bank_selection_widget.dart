import 'package:flutter/material.dart';
import '../../../../../core/constants/appcolor_constants.dart';

/// Bank item model
class BankItem {
  final String code;
  final String name;
  final String? iconUrl;

  const BankItem({
    required this.code,
    required this.name,
    this.iconUrl,
  });
}

/// Available banks for VA generation
/// TODO: Isi iconUrl dengan URL SVG yang sebenarnya
const List<BankItem> availableBanks = [
  BankItem(code: 'BTN', name: 'BTN', iconUrl: 'https://upload.wikimedia.org/wikipedia/commons/c/ca/BTN_2024.svg'), // Ganti dengan URL asli
  BankItem(code: 'CIMB', name: 'CIMB', iconUrl: 'https://upload.wikimedia.org/wikipedia/commons/3/38/CIMB_Niaga_logo.svg'), // Ganti dengan URL asli
  BankItem(code: 'DANAMON', name: 'DANAMON', iconUrl: 'https://upload.wikimedia.org/wikipedia/commons/7/7b/Danamon.svg'), // Ganti dengan URL asli
  BankItem(code: 'BNC', name: 'BNC', iconUrl: 'https://example.com/bnc.svg'), // Ganti dengan URL asli
  BankItem(code: 'BSI', name: 'BSI', iconUrl: 'https://upload.wikimedia.org/wikipedia/commons/a/a0/Bank_Syariah_Indonesia.svg'), // Ganti dengan URL asli
  BankItem(code: 'BNI', name: 'BNI', iconUrl: 'https://logotyp.us/file/bni.svg'), // Ganti dengan URL asli
  BankItem(code: 'BRI', name: 'BRI', iconUrl: 'https://logotyp.us/file/bri.svg'), // Ganti dengan URL asli
  BankItem(code: 'MANDIRI', name: 'MANDIRI', iconUrl: 'https://logotyp.us/file/mandiri.svg'), // Ganti dengan URL asli
  BankItem(code: 'PERMATA', name: 'PERMATA', iconUrl: 'https://upload.wikimedia.org/wikipedia/commons/f/ff/Permata_Bank_%282024%29.svg'), // Ganti dengan URL asli
];

/// Bank Selection Widget - List style with radio buttons like PaymentMethodItem
class BankSelectionWidget extends StatelessWidget {
  final String? selectedBank;
  final ValueChanged<String> onBankSelected;

  const BankSelectionWidget({
    Key? key,
    required this.selectedBank,
    required this.onBankSelected,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Column(
      children: availableBanks.map((bank) {
        final isSelected = selectedBank == bank.code;

        return GestureDetector(
          onTap: () => onBankSelected(bank.code),
          child: Container(
            padding: const EdgeInsets.symmetric(vertical: 12.0, horizontal: 8.0),
            margin: const EdgeInsets.symmetric(vertical: 4.0),
            child: Row(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                // Radio button icon (MOVED TO LEFT)
                Icon(
                  isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
                  color: isSelected ? AppColors.primaryColor : Colors.grey,
                ),
                const SizedBox(width: 8),
                // Bank icon (60x60) - initial letter
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: isSelected ? AppColors.primaryColor.withValues(alpha: 0.1) : Colors.grey.shade100,
                    borderRadius: BorderRadius.circular(8),
                    border: Border.all(
                      color: isSelected ? AppColors.primaryColor : Colors.grey.shade300,
                    ),
                  ),
                  alignment: Alignment.center,
                  child: Text(
                    bank.code.length > 3 ? bank.code.substring(0, 3) : bank.code,
                    style: TextStyle(
                      fontSize: 11,
                      fontWeight: FontWeight.bold,
                      color: isSelected ? AppColors.primaryColor : Colors.grey.shade700,
                    ),
                  ),
                ),
                const SizedBox(width: 12),
                // Bank name
                Expanded(
                  child: Text(
                    bank.name,
                    style: TextStyle(
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      color: isSelected ? AppColors.primaryColor : Colors.black87,
                      fontSize: 14,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
        );
      }).toList(),
    );
  }
}
