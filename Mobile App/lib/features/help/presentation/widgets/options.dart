import 'package:flutter/material.dart';

class OptionsSelector extends StatefulWidget {
  final List<String> options;
  final void Function(String)? onSelected;

  const OptionsSelector({
    Key? key,
    required this.options,
    this.onSelected,
  }) : super(key: key);

  @override
  State<OptionsSelector> createState() => _OptionsSelectorState();
}

class _OptionsSelectorState extends State<OptionsSelector> {
  String selected = "Umum";

  @override
  Widget build(BuildContext context) {
    return Row(
      children: widget.options.map((option) {
        final isSelected = option == selected;
        return Padding(
          padding: const EdgeInsets.symmetric(horizontal: 4),
          child: OutlinedButton(
            onPressed: () {
              setState(() {
                selected = option;
              });
              if (widget.onSelected != null) {
                widget.onSelected!(option);
              }
            },
            style: OutlinedButton.styleFrom(
              backgroundColor: isSelected ? const Color(0xFF134E3A) : Colors.white,
              side: const BorderSide(color: Color(0xFF134E3A)),
              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(24)),
              foregroundColor: isSelected ? Colors.white : Colors.black,
              elevation: 2,
              shadowColor: Colors.black26,
            ),
            child: Text(option),
          ),
        );
      }).toList(),
    );
  }
}
