import 'package:flutter/material.dart';

/// Reusable glass-style form field widget for auth pages.
/// Adapts background and text color to dark/light mode via Theme.of(context).
class AuthFormField extends StatelessWidget {
  final String hint;
  final TextEditingController controller;
  final TextInputType? keyboardType;
  final bool obscureText;
  final Widget? suffixIcon;
  final String? Function(String?)? validator;

  const AuthFormField({
    Key? key,
    required this.hint,
    required this.controller,
    this.keyboardType,
    this.obscureText = false,
    this.suffixIcon,
    this.validator,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Container(
      decoration: BoxDecoration(
        // Glass-style semi-transparent background
        color: isDark
            ? Colors.white.withValues(alpha: 0.08)
            : Colors.grey[200],
        borderRadius: BorderRadius.circular(12),
        border: Border.all(
          color: isDark
              ? Colors.white.withValues(alpha: 0.12)
              : Colors.transparent,
          width: 0.5,
        ),
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16.0),
        child: TextFormField(
          controller: controller,
          keyboardType: keyboardType,
          obscureText: obscureText,
          style: TextStyle(
            color: isDark ? Colors.white : Colors.black,
          ),
          decoration: InputDecoration(
            border: InputBorder.none,
            hintText: hint,
            labelText: hint,
            labelStyle: TextStyle(
              color: isDark ? Colors.grey.shade500 : Colors.grey.shade600,
            ),
            hintStyle: TextStyle(
              color: isDark ? Colors.grey.shade600 : Colors.grey.shade500,
            ),
            suffixIcon: suffixIcon,
            isCollapsed: true,
            contentPadding: const EdgeInsets.symmetric(vertical: 14),
          ),
          validator: validator,
        ),
      ),
    );
  }
}
