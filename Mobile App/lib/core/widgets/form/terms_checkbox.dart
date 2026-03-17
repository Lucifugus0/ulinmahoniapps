import 'package:flutter/material.dart';

/// Reusable Terms and Conditions checkbox widget
class TermsCheckbox extends StatelessWidget {
  final bool value;
  final ValueChanged<bool?> onChanged;
  final String prefixText;
  final String linkText;
  final VoidCallback onLinkTap;
  final TextStyle? textStyle;
  final TextStyle? linkStyle;

  const TermsCheckbox({
    Key? key,
    required this.value,
    required this.onChanged,
    required this.prefixText,
    required this.linkText,
    required this.onLinkTap,
    this.textStyle,
    this.linkStyle,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final defaultTextStyle = TextStyle(fontSize: 14, color: Colors.grey[700]);
    final defaultLinkStyle = TextStyle(
      fontSize: 14,
      color: Colors.grey[800],
      fontWeight: FontWeight.bold,
      decoration: TextDecoration.underline,
    );

    return Row(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Checkbox(
          value: value,
          onChanged: onChanged,
          activeColor: const Color(0xFF124624),
          side: BorderSide(color: Colors.grey.shade400, width: 2.0),
        ),
        Expanded(
          child: Padding(
            padding: const EdgeInsets.only(top: 10.0),
            child: Wrap(
              crossAxisAlignment: WrapCrossAlignment.center,
              children: [
                GestureDetector(
                  onTap: () => onChanged(!value),
                  child: Text(prefixText, style: textStyle ?? defaultTextStyle),
                ),
                GestureDetector(
                  onTap: onLinkTap,
                  child: Text(linkText, style: linkStyle ?? defaultLinkStyle),
                ),
              ],
            ),
          ),
        ),
      ],
    );
  }
}

