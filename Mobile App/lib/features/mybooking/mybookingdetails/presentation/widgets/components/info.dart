import 'package:flutter/material.dart';

Widget info(String label, String value, {bool isBold = false, Color? color}) {
  return Padding(
    padding: const EdgeInsets.symmetric(vertical: 4),
    child: Row(
      mainAxisAlignment: MainAxisAlignment.spaceBetween,
      children: [
        
        Expanded(
          flex: 3,
          child: Text(
            label,
            style: const TextStyle(fontSize: 16, color: Colors.black),
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
              color: color ?? Colors.black,
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