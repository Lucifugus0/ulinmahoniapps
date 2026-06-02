import 'package:flutter/material.dart';

/// Fallback widget for property facilities using bullet text (no icons).
/// Uses Column+Row instead of GridView to avoid excess vertical whitespace.
class PropertyFacilitiesTextGrid extends StatelessWidget {
  final List<String>? general;
  final List<String>? security;
  final List<String>? amenities;

  const PropertyFacilitiesTextGrid({
    Key? key,
    this.general,
    this.security,
    this.amenities,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final List<String> allFacilities = [
      ...?general,
      ...?security,
      ...?amenities,
    ];

    if (allFacilities.isEmpty) {
      return const SizedBox.shrink();
    }

    final List<Widget> rows = [];
    for (int i = 0; i < allFacilities.length; i += 2) {
      rows.add(Row(
        children: [
          Expanded(child: _buildItem(context, allFacilities[i])),
          if (i + 1 < allFacilities.length)
            Expanded(child: _buildItem(context, allFacilities[i + 1]))
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
            style: Theme.of(context).textTheme.bodyMedium?.copyWith(
              fontSize: 15,
              fontWeight: FontWeight.bold,
            ),
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
