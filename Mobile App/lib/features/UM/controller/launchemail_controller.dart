import 'package:url_launcher/url_launcher.dart';
import '../../../core/utils/app_logger.dart';

Future<void> launchEmail(String toEmail, {String subject = ""}) async {
  final Uri emailLaunchUri = Uri(
    scheme: 'mailto',
    path: toEmail,
    query: 'subject=${Uri.encodeComponent(subject)}',
  );

  try {
    if (await canLaunchUrl(emailLaunchUri)) {
      await launchUrl(emailLaunchUri);
    } else {
      AppLogger.w('Could not launch $emailLaunchUri', 'LAUNCH-EMAIL');
    }
  } catch (e) {
    AppLogger.e('Error launching email', e, StackTrace.current, 'LAUNCH-EMAIL');
  }
}