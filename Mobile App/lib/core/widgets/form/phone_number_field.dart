import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:intl_phone_field/intl_phone_field.dart';
import 'package:intl_phone_field/phone_number.dart';

class PhoneNumberField extends StatelessWidget {
  final TextEditingController controller;
  final String labelText;
  final String hintText;
  final String initialCountryCode;
  final ValueChanged<String>? onChanged;
  final ValueChanged<String>? onCountryChanged;
  final String? Function(PhoneNumber?)? validator;

  const PhoneNumberField({
    Key? key,
    required this.controller,
    required this.labelText,
    required this.hintText,
    this.initialCountryCode = 'ID',
    this.onChanged,
    this.onCountryChanged,
    this.validator,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return Container(
      decoration: BoxDecoration(
        color: Colors.grey[200],
        borderRadius: BorderRadius.circular(8),
      ),
      child: IntlPhoneField(
        key: ValueKey(initialCountryCode),
        controller: controller,
        inputFormatters: [FilteringTextInputFormatter.digitsOnly],
        decoration: InputDecoration(
          labelText: labelText,
          hintText: hintText,
          border: OutlineInputBorder(
            borderRadius: BorderRadius.circular(8),
            borderSide: BorderSide.none,
          ),
          filled: false,
          contentPadding: const EdgeInsets.symmetric(
            vertical: 14.0,
            horizontal: 16.0,
          ),
          counterText: "",
        ),
        initialCountryCode: initialCountryCode,
        languageCode: "id",
        dropdownIcon: const Icon(Icons.arrow_drop_down),
        dropdownIconPosition: IconPosition.trailing,
        dropdownTextStyle: const TextStyle(fontSize: 16, color: Colors.black),
        cursorColor: const Color(0xFF124624),
        onChanged: (phone) {
          onChanged?.call(phone.completeNumber);
        },
        onCountryChanged: (country) {
          onCountryChanged?.call(country.code);
        },
        validator: validator,
      ),
    );
  }
}
