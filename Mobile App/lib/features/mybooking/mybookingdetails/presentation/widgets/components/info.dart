import 'package:flutter/material.dart';

Widget info(BuildContext context, String label, String value, {bool isBold = false, Color? color}) {
  final isDark = Theme.of(context).brightness == Brightness.dark;
  final defaultTextColor = isDark ? Colors.grey[300]! : Colors.black;

  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        Expanded(
          flex: 3,
          child: Text(
            label,
            style: TextStyle(fontSize: 16, color: isDark ? Colors.grey[400] : Colors.black),
          ),
        ),
        Expanded(
          flex: 3,
          child: Text(
            value,
            textAlign: TextAlign.right,
            style: TextStyle(
              fontWeight: isBold ? FontWeight.bold : FontWeight.normal,
              fontSize: 16,
              color: color ?? defaultTextColor,
            ),
            softWrap: true,
            overflow: TextOverflow.ellipsis,
            maxLines: 2,
          ),
        ),
      ],
    ),
  );
}
