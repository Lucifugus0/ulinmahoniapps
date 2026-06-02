import 'package:flutter/material.dart';

/// Fallback room facilities with bullet text (no icons).
/// Uses Column+Row instead of GridView to avoid excess vertical whitespace.
class RoomFacilitiesTextGrid extends StatelessWidget {
  final List<String> facilities;

  const RoomFacilitiesTextGrid({
    super.key,
    required this.facilities,
  });

  @override
  Widget build(BuildContext context) {
    if (facilities.isEmpty) {
      return const SizedBox.shrink();
    }

    final List<Widget> rows = [];
    for (int i = 0; i < facilities.length; i += 2) {
      rows.add(Row(
        children: [
          Expanded(child: _buildItem(context, facilities[i])),
          if (i + 1 < facilities.length)
            Expanded(child: _buildItem(context, facilities[i + 1]))
          else
            const Expanded(child: SizedBox.shrink()),
        ],
      ));
    }

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: rows,
    );
  }

  Widget _buildItem(BuildContext context, String name) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 6),
      child: Row(
        mainAxisSize: MainAxisSize.min,
        children: [
          Text(
            '• ',
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(fontSize: 15),
          ),
          Flexible(
            child: Text(
              name,
              style: Theme.of(context).textTheme.bodyMedium?.copyWith(fontSize: 15),
              overflow: TextOverflow.ellipsis,
              maxLines: 2,
            ),
          ),
        ],
      ),
    );
  }
}
