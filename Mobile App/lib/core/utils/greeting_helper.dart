import 'package:ulinmahoniapps/l10n/app_localizations.dart';

String getTimeBasedGreeting(AppLocalizations localizations) {
  final hour = DateTime.now().hour;

  if (hour >= 5 && hour < 12) {
    // Morning: 5:00 AM - 11:59 AM
    return localizations.greetingMorning;
  } else if (hour >= 12 && hour < 15) {
    // Afternoon: 12:00 PM - 2:59 PM
    return localizations.greetingAfternoon;
  } else if (hour >= 15 && hour < 18) {
    // Evening: 3:00 PM - 5:59 PM
    return localizations.greetingEvening;
  } else {
    // Night: 6:00 PM - 4:59 AM
    return localizations.greetingNight;
  }
}
