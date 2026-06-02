import 'package:flutter/material.dart';

/// Input field with dark mode support — single-layer fill via InputDecoration.
Widget inputField(
  String hint, {
  required TextEditingController controller,
  BuildContext? context,
}) {
  final isDark = context != null && Theme.of(context).brightness == Brightness.dark;
  // Single rounded border used for all states — no visible border line
  final border = OutlineInputBorder(
    borderRadius: BorderRadius.circular(8),
    borderSide: BorderSide.none,
  );
  return TextField(
    controller: controller,
    style: TextStyle(color: isDark ? Colors.white : Colors.black87),
    decoration: InputDecoration(
      // Single fill layer — no Container wrapper needed
      filled: true,
      fillColor: isDark ? const Color(0xFF374151) : Colors.grey[200],
      border: border,
      enabledBorder: border,
      focusedBorder: border,
      hintText: hint,
      hintStyle: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[500]),
      contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
    ),
  );
}