import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/features/home/data/filter_data.dart';

class Filter extends StatefulWidget {
  final int selectedIndex;
  final ValueChanged<int> onTypeSelected;
  final Color selectedColor;
  final Color unselectedColor;

  const Filter({
    Key? key,
    required this.selectedIndex,
    required this.onTypeSelected,
    this.selectedColor = const Color(0xFF004D40),
    this.unselectedColor = const Color(0xFF800000),
  }) : super(key: key);

  @override
  State<Filter> createState() => _FilterState();
}

class _FilterState extends State<Filter> {
  @override
  Widget build(BuildContext context) {
    return SizedBox(
      height: 70,
      width: double.infinity, 
      child: ListView.builder(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: filterdata.length,
        itemBuilder: (context, index) {
          final isSelected = widget.selectedIndex == index;
          final color = isSelected ? widget.selectedColor : widget.unselectedColor;
          final icon = filterdata[index]['icon'] as IconData;
          final label = filterdata[index]['label'] as String;

          return GestureDetector(
            onTap: () => widget.onTypeSelected(index),
            child: Container(
              margin: const EdgeInsets.only(right: 16),
              child: Column(
                mainAxisSize: MainAxisSize.min,
                children: [
                  Icon(icon, size: 20, color: color),
                  const SizedBox(height: 4),
                  Text(label, style: TextStyle(fontSize: 12, color: color)),
                  const SizedBox(height: 4),
                  if (isSelected)
                    Container(
                      height: 2,
                      width: 20,
                      color: color,
                    ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }
}
