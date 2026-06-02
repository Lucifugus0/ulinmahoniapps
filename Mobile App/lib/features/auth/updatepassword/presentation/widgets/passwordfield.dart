import 'package:flutter/material.dart';
import '../../../../../core/constants/appcolor_constants.dart';

/// Dark-aware password input field.
/// Uses hintText only (no labelText) to avoid floating label overlap when focused.
Widget passwordField(
    BuildContext context,
    String hint,
    TextEditingController controller, {
      required bool obscure,
      required VoidCallback onToggleVisibility,
      String? Function(String?)? validator,
    }) {
  final isDark = Theme.of(context).brightness == Brightness.dark;

  return TextFormField(
    controller: controller,
    obscureText: obscure,
    // Dark-aware text color
    style: TextStyle(color: isDark ? Colors.white : Colors.black87),
    decoration: InputDecoration(
      // hintText only — no labelText, eliminates floating label overlap
      hintText: hint,
      hintStyle: TextStyle(
        color: isDark ? Colors.grey[400] : Colors.grey[500],
      ),
      // Dark-aware filled background
      filled: true,
      fillColor: isDark ? const Color(0xFF374151) : Colors.grey[200],
      contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 18),
      border: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide.none,
      ),
      enabledBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide.none,
      ),
      focusedBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: BorderSide(
          // Highlight border on focus with primary color
          color: AppColors.primaryAdaptive(context),
          width: 1.5,
        ),
      ),
      errorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Colors.red, width: 1.5),
      ),
      focusedErrorBorder: OutlineInputBorder(
        borderRadius: BorderRadius.circular(8),
        borderSide: const BorderSide(color: Colors.red, width: 1.5),
      ),
      suffixIcon: IconButton(
        icon: Icon(
          obscure ? Icons.visibility_off : Icons.visibility,
          // Dark-aware icon color
          color: isDark ? Colors.grey[400] : Colors.grey[600],
        ),
        onPressed: onToggleVisibility,
      ),
    ),
    validator: validator,
  );
}
