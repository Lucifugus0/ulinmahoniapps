import 'package:flutter/material.dart';
import 'package:flutter_widget_from_html_core/flutter_widget_from_html_core.dart';

/// Renders HTML description content from the rich text editor (Quill.js).
/// Falls back to plain Text widget if content has no HTML tags.
class HtmlDescription extends StatelessWidget {
  final String html;
  final TextStyle? textStyle;

  const HtmlDescription({
    super.key,
    required this.html,
    this.textStyle,
  });

  @override
  Widget build(BuildContext context) {
    if (html.isEmpty) return const SizedBox.shrink();

    // Check if content contains HTML tags
    final hasHtml = RegExp(r'<[^>]+>').hasMatch(html);

    if (!hasHtml) {
      // Plain text (legacy data) — render as Text widget
      return Text(
        html,
        style: textStyle ?? Theme.of(context).textTheme.bodyMedium,
        softWrap: true,
        overflow: TextOverflow.visible,
        textAlign: TextAlign.left,
      );
    }

    // HTML content from Quill editor — render with flutter_widget_from_html
    return HtmlWidget(
      html,
      textStyle: textStyle ?? Theme.of(context).textTheme.bodyMedium,
    );
  }
}
