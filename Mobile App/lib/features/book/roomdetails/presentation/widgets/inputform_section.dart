import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class RoomInputSection extends StatelessWidget {
  final String? rentType;
  final int? duration;
  final DateTime? checkInDate;
  final DateTime? checkOutDate;
  final TextEditingController checkInDateController;
  final TextEditingController checkOutDateController;
  final Function(String?) onRentTypeChanged;
  final Function(int?) onDurationChanged;
  final VoidCallback onSelectCheckInDate;
  final List<String> availableRentTypes;

  const RoomInputSection({
    Key? key,
    required this.rentType,
    required this.duration,
    required this.checkInDate,
    required this.checkOutDate,
    required this.checkInDateController,
    required this.checkOutDateController,
    required this.onRentTypeChanged,
    required this.onDurationChanged,
    required this.onSelectCheckInDate,
    required this.availableRentTypes,
  }) : super(key: key);

  // --- Helper Logic (Tetap Sama) ---
  String _mapRentTypeValueToDisplay(AppLocalizations localizations, String value) {
    switch (value.toLowerCase()) {
      case 'daily':
        return localizations.dailyRentType;
      case 'monthly':
        return localizations.monthlyRentType;
      default:
        return value;
    }
  }

  String? _mapDisplayValueToRentType(AppLocalizations localizations, String displayValue) {
    if (displayValue == localizations.dailyRentType) {
      return 'daily';
    } else if (displayValue == localizations.monthlyRentType) {
      return 'monthly';
    }
    return null;
  }

  // --- Helper Styles (Desain Baru) ---

  // 1. Style Label di atas input
  Widget _buildLabel(String text, bool isDark) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 8.0),
      child: Text(
        text,
        style: TextStyle(
          fontSize: 14,
          fontWeight: FontWeight.w600, // Semi-bold agar terbaca jelas
          color: isDark ? Colors.white : Colors.black87,
        ),
      ),
    );
  }

  // 2. Style Dekorasi Input (Clean, Border Tipis)
  InputDecoration _buildInputDecoration({required String hintText, IconData? suffixIcon, required bool isDark}) {
    return InputDecoration(
      hintText: hintText,
      hintStyle: TextStyle(color: Colors.grey.shade400, fontSize: 14),
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
      // Ikon diletakkan di kanan (suffix) dengan warna halus
      suffixIcon: suffixIcon != null
          ? Icon(suffixIcon, color: Colors.grey.shade400, size: 20)
          : null,
      fillColor: isDark ? const Color(0xFF374151) : Colors.white,
      filled: true,
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide(color: isDark ? Colors.grey.shade600 : Colors.grey.shade300, width: 1),
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: AppColors.primaryColor, width: 1.5),
      ),
      disabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide(color: isDark ? Colors.grey.shade700 : Colors.grey.shade200, width: 1),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Colors.red, width: 1),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    // Dark mode detection — passed to helper methods that don't have BuildContext
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      // Margin kiri-kanan 16px (Standar Mobile)
      margin: const EdgeInsets.symmetric(vertical: 16, horizontal: 4),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          // --- HEADER SECTION ---
          // const Text(
          //   "Booking Information",
          //   style: TextStyle(
          //     fontSize: 18,
          //     fontWeight: FontWeight.bold,
          //     color: Colors.black87,
          //   ),
          // ),
          // const SizedBox(height: 4),
          // Text(
          //   "Please confirm your stay details below.",
          //   style: TextStyle(
          //     fontSize: 13,
          //     color: Colors.grey.shade600,
          //   ),
          // ),
          // const SizedBox(height: 24), // Jarak agak jauh ke form
          // ----------------------

          // 1. RENT TYPE
          buildRentTypeInput(localizations, isDark),
          const SizedBox(height: 20), // Jarak antar field lebih lega

          // 2. CHECK-IN DATE
          GestureDetector(
            onTap: onSelectCheckInDate,
            child: AbsorbPointer(
              child: buildDateInput(
                localizations.checkInDateLabel,
                "Select Date",
                checkInDateController,
                isDark,
              ),
            ),
          ),
          const SizedBox(height: 20),

          // 3. DURATION
          buildDurationInput(localizations, isDark),
          const SizedBox(height: 20),

          // 4. CHECK-OUT DATE
          buildCheckoutDateInput(
            localizations.checkOutDateLabel,
            localizations.autoFilledHint,
            checkOutDateController,
            isDark,
          ),
        ],
      ),
    );
  }

  Widget buildRentTypeInput(AppLocalizations localizations, bool isDark) {
    final List<String> displayOptions = availableRentTypes
        .map((value) => _mapRentTypeValueToDisplay(localizations, value))
        .toList();

    String? currentDisplayValue;
    if (rentType != null) {
      currentDisplayValue = _mapRentTypeValueToDisplay(localizations, rentType!);
    }

    // Validasi Safety Crash
    if (currentDisplayValue != null && !displayOptions.contains(currentDisplayValue)) {
      currentDisplayValue = null;
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildLabel(localizations.rentTypeLabel, isDark),
        DropdownButtonFormField<String>(
          value: currentDisplayValue,
          items: displayOptions.map((item) => DropdownMenuItem(
            value: item,
            child: Text(item, style: TextStyle(color: isDark ? Colors.white : Colors.black87, fontSize: 14)),
          )).toList(),
          icon: const SizedBox.shrink(), // Sembunyikan ikon default dropdown (kita pakai decoration)
          decoration: _buildInputDecoration(
            hintText: "Select Type",
            suffixIcon: Icons.keyboard_arrow_down_rounded, // Custom arrow icon
            isDark: isDark,
          ),
          onChanged: (displayValue) {
            if (displayValue != null) {
              final rentTypeValue = _mapDisplayValueToRentType(localizations, displayValue);
              onRentTypeChanged(rentTypeValue);
            }
          },
        ),
      ],
    );
  }

  Widget buildDurationInput(AppLocalizations localizations, bool isDark) {
    int maxDuration = 1;
    String labelText = localizations.durationLabel;

    final safeRentType = rentType?.toLowerCase();

    if (safeRentType == 'daily') {
      maxDuration = 31;
      labelText = localizations.dailyDurationLabel;
    } else if (safeRentType == 'monthly') {
      maxDuration = 12;
      labelText = localizations.monthlyDurationLabel;
    }

    final int currentDuration = duration ?? 1;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildLabel(labelText, isDark),
        Container(
          decoration: BoxDecoration(
            color: isDark ? const Color(0xFF374151) : Colors.white,
            borderRadius: BorderRadius.circular(8),
            border: Border.all(color: isDark ? Colors.grey.shade600 : Colors.grey.shade300, width: 1),
          ),
          child: Row(
            children: [
              // Duration Display
              Expanded(
                child: Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 14),
                  child: Text(
                    currentDuration.toString(),
                    textAlign: TextAlign.left,
                    style: TextStyle(
                      fontSize: 14,
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                  ),
                ),
              ),

              // Buttons Container
              Row(
                children: [
                  // Minus Button
                  IconButton(
                    onPressed: rentType == null || currentDuration <= 1 ? null : () {
                      onDurationChanged(currentDuration - 1);
                    },
                    icon: const Icon(Icons.remove),
                    color: AppColors.primaryColor,
                    iconSize: 20,
                    disabledColor: Colors.grey.shade300,
                  ),
                  Container(
                    width: 1,
                    height: 24,
                    color: Colors.grey.shade300,
                  ),
                  // Plus Button
                  IconButton(
                    onPressed: rentType == null || currentDuration >= maxDuration ? null : () {
                      onDurationChanged(currentDuration + 1);
                    },
                    icon: const Icon(Icons.add),
                    color: AppColors.primaryColor,
                    iconSize: 20,
                    disabledColor: Colors.grey.shade300,
                  ),
                ],
              ),
            ],
          ),
        ),
      ],
    );
  }

  Widget buildDateInput(String label, String hint, TextEditingController controller, bool isDark) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildLabel(label, isDark),
        TextField(
          controller: controller,
          readOnly: true,
          style: TextStyle(color: isDark ? Colors.white : Colors.black87, fontSize: 14),
          decoration: _buildInputDecoration(
            hintText: hint,
            suffixIcon: Icons.calendar_today_rounded, // Ikon kalender halus
            isDark: isDark,
          ),
        ),
      ],
    );
  }

  Widget buildCheckoutDateInput(String label, String hint, TextEditingController controller, bool isDark) {
    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        _buildLabel(label, isDark),
        TextField(
          controller: controller,
          enabled: false,
          style: TextStyle(color: Colors.grey.shade600, fontSize: 14),
          decoration: _buildInputDecoration(
            hintText: hint,
            suffixIcon: Icons.event_busy_rounded, // Ikon berbeda untuk disabled (opsional)
            isDark: isDark,
          ).copyWith(
            fillColor: isDark ? const Color(0xFF2D3748) : Colors.grey.shade100,
          ),
        ),
      ],
    );
  }
}