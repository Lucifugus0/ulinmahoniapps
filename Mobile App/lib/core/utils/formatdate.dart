import 'package:intl/intl.dart';
import 'app_logger.dart';

String? formatDate(String? dateString) {
  if (dateString == null) return null;
  try {
    DateTime? dateTime;

    // Try to parse as ISO 8601 first
    try {
      // Parse as UTC then convert to local date only (ignore time)
      final parsedUtc = DateTime.parse(dateString);
      // Create local date with year/month/day only (no timezone conversion)
      dateTime = DateTime(parsedUtc.year, parsedUtc.month, parsedUtc.day);

      AppLogger.d('Parsed date: "$dateString" → UTC: $parsedUtc → Local date-only: $dateTime', 'FORMAT-DATE');
    } catch (_) {
      // If that fails, try common formats
      final formats = [
        DateFormat('dd-MM-yyyy HH:mm'),
        DateFormat('dd-MM-yyyy'),
        DateFormat('yyyy-MM-dd'),
        DateFormat('yyyy-MM-dd HH:mm:ss'),
      ];

      for (var format in formats) {
        try {
          dateTime = format.parse(dateString);
          // Convert to date-only (no time component)
          dateTime = DateTime(dateTime.year, dateTime.month, dateTime.day);
          break;
        } catch (_) {
          continue;
        }
      }

      if (dateTime == null) {
        throw FormatException('Unable to parse date: $dateString');
      }
    }

    DateFormat outputFormat = DateFormat("EEE, dd MMM yyyy");
    return outputFormat.format(dateTime);
  } catch (e) {
    AppLogger.e("Error formatting date", e, StackTrace.current, 'FORMAT-DATE');
    return dateString;
  }
}