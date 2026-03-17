

import 'package:flutter/material.dart';

Widget inputField(
    String hint,
    TextEditingController controller,
    ) {
  return Container(
    decoration: BoxDecoration(
      color: Colors.grey[200],
      borderRadius: BorderRadius.circular(8),
    ),
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16.0),
      child: TextField(
        controller: controller,
        decoration: InputDecoration(
          border: InputBorder.none,
          hintText: hint,
          isCollapsed: true, 
          contentPadding: EdgeInsets.symmetric(vertical: 14), 
        ),
      ),
    ),
  );
}