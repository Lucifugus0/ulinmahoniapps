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
    return Container(
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(8),
      ),
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 16.0),
        child: TextField(
          controller: widget.controller,
          obscureText: _obscurePassword,
          decoration: InputDecoration(
            border: InputBorder.none,
            hintText: widget.hint,
            isCollapsed: true,
            contentPadding: EdgeInsets.symmetric(vertical: 14),
            suffixIcon: IconButton(
              icon: Icon(
                _obscurePassword ? Icons.visibility_off : Icons.visibility,
                color: Colors.grey,
              ),
              onPressed: () {
                setState(() {
                  _obscurePassword = !_obscurePassword;
                });
              },
            ),
          ),
        ),
      ),
    );
  }
}
