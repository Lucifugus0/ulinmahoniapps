import 'package:intl/intl.dart';

String formatCurrency(dynamic amount) {

  num value;
  if (amount is String) {
    value = double.tryParse(amount) ?? 0;
  } else if (amount is num) {
    value = amount;
  } else {
    value = 0;
  }

  final formatCurrency = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
  return formatCurrency.format(value);
}

