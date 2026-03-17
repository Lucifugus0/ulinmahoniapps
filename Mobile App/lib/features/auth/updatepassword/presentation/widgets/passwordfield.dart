import 'package:flutter/material.dart';

Widget passwordField(
    String hint,
    TextEditingController controller, {
      required bool obscure,
      required VoidCallback onToggleVisibility,
      String? Function(String?)? validator,
    }) {
  return Container(
    decoration: BoxDecoration(
      color: Colors.grey[200],
      borderRadius: BorderRadius.circular(8),
    ),
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0),
      child: TextFormField( 
        controller: controller,
        obscureText: obscure,
        decoration: InputDecoration(
          border: InputBorder.none,
          hintText: hint,
          labelText: hint,
          suffixIcon: IconButton(
            icon: Icon(
              obscure ? Icons.visibility_off : Icons.visibility,
              color: Colors.grey,
            ),
            onPressed: onToggleVisibility,
          ),
        ),
        validator: validator,
      ),
    ),
  );
}