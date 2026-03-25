import 'package:flutter/material.dart';

class PasswordField extends StatefulWidget {
  final String hint;
  final TextEditingController controller;

  const PasswordField({
    Key? key,
    required this.hint,
    required this.controller,
  }) : super(key: key);

  @override
  _PasswordFieldState createState() => _PasswordFieldState();
}

class _PasswordFieldState extends State<PasswordField> {
  bool _obscurePassword = true;

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    // Single rounded border used for all states — no visible border line
    final border = OutlineInputBorder(
      borderRadius: BorderRadius.circular(8),
      borderSide: BorderSide.none,
    );
    return TextField(
      controller: widget.controller,
      obscureText: _obscurePassword,
      style: TextStyle(color: isDark ? Colors.white : Colors.black87),
      decoration: InputDecoration(
        // Single fill layer — no Container wrapper needed
        filled: true,
        fillColor: isDark ? const Color(0xFF374151) : Colors.grey[200],
        border: border,
        enabledBorder: border,
        focusedBorder: border,
        hintText: widget.hint,
        hintStyle: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[500]),
        contentPadding: const EdgeInsets.symmetric(vertical: 14, horizontal: 16),
        suffixIcon: IconButton(
          icon: Icon(
            _obscurePassword ? Icons.visibility_off : Icons.visibility,
            color: isDark ? Colors.grey[400] : Colors.grey,
          ),
          onPressed: () {
            setState(() {
              _obscurePassword = !_obscurePassword;
            });
          },
        ),
      ),
    );
  }
}
