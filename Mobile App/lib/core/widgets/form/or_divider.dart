import 'package:flutter/material.dart';

/// Reusable "OR" divider widget
class OrDivider extends StatelessWidget {
  final String text;

  const OrDivider({
    Key? key,
    this.text = 'ATAU',
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Row(
      children: [
        Expanded(child: Divider(color: Colors.grey.shade400)),
        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 12.0),
          child: Text(
            text,
            style: const TextStyle(color: Colors.grey),
          ),
        ),
        Expanded(child: Divider(color: Colors.grey.shade400)),
      ],
    );
  }
}

