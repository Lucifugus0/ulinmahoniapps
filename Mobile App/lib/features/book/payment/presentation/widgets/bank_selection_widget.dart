import 'package:flutter/material.dart';
import '../../../../../core/constants/appcolor_constants.dart';

/// Bank item model
class BankItem {
  final String code;
  final String name;
  final String? iconAsset;  // Local asset path

  const BankItem({
    required this.code,
    required this.name,
    this.iconAsset,
  });
}

/// Available banks for VA generation
/// TODO: Isi iconUrl dengan URL SVG yang sebenarnya
const List<BankItem> availableBanks = [
  BankItem(code: 'BTN', name: 'BTN', iconAsset: 'assets/images/payment/btn.png'),
  BankItem(code: 'CIMB', name: 'CIMB', iconAsset: 'assets/images/payment/cimb.png'),
  BankItem(code: 'DANAMON', name: 'DANAMON', iconAsset: 'assets/images/payment/danamon.png'),
  BankItem(code: 'BNC', name: 'BNC', iconAsset: 'assets/images/payment/bnc.png'),
  BankItem(code: 'BSI', name: 'BSI', iconAsset: 'assets/images/payment/bsi.png'),
  BankItem(code: 'BNI', name: 'BNI', iconAsset: 'assets/images/payment/bni.png'),
  BankItem(code: 'BRI', name: 'BRI', iconAsset: 'assets/images/payment/bri.png'),
  BankItem(code: 'MANDIRI', name: 'MANDIRI', iconAsset: 'assets/images/payment/mandiri.png'),
  BankItem(code: 'PERMATA', name: 'PERMATA', iconAsset: 'assets/images/payment/permata.png'),
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
    // Dark mode detection for bank name text color
    final isDark = Theme.of(context).brightness == Brightness.dark;
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
                // Use primaryAdaptive for the radio button icon color when selected
                Icon(
                  isSelected ? Icons.check_circle : Icons.radio_button_unchecked,
                  color: isSelected ? AppColors.primaryAdaptive(context) : Colors.grey,
                ),
                const SizedBox(width: 8),
                // Bank icon (60x60) - white background in dark mode for visibility
                Container(
                  width: 60,
                  height: 60,
                  decoration: BoxDecoration(
                    color: isDark ? Colors.white : Colors.transparent,
                    borderRadius: BorderRadius.circular(8),
                  ),
                  padding: isDark ? const EdgeInsets.all(4) : EdgeInsets.zero,
                  child: bank.iconAsset != null
                      ? Image.asset(
                          bank.iconAsset!,
                          width: 60,
                          height: 60,
                          fit: BoxFit.contain,
                          errorBuilder: (context, error, stackTrace) => Icon(
                            Icons.account_balance,
                            size: 32,
                            color: isSelected ? AppColors.primaryAdaptive(context) : Colors.grey.shade600,
                          ),
                        )
                      : Icon(
                          Icons.account_balance,
                          size: 32,
                          color: isSelected ? AppColors.primaryAdaptive(context) : Colors.grey.shade600,
                        ),
                ),
                const SizedBox(width: 12),
                // Bank name
                Expanded(
                  child: Text(
                    bank.name,
                    style: TextStyle(
                      fontWeight: isSelected ? FontWeight.bold : FontWeight.normal,
                      // Use primaryAdaptive for the bank name text color when selected
                      color: isSelected ? AppColors.primaryAdaptive(context) : (isDark ? Colors.white : Colors.black87),
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
