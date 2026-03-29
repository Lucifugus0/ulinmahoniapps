import 'package:flutter/material.dart';
import 'package:flutter_dotenv/flutter_dotenv.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'router/router.dart';
import 'features/error/presentation/pages/errorpage.dart';
import 'package:camera/camera.dart';
import 'package:flutter/services.dart';
import 'l10n/app_localizations.dart';
import 'core/provider/language_provider.dart';
import 'core/theme/app_theme.dart';
import 'core/theme/theme_provider.dart';
import 'core/utils/network.dart';
import 'core/utils/app_logger.dart';
import 'core/services/ping_monitor_service.dart';
import 'core/services/local_notification_service.dart';
// TEMPORARY DISABLED: BGTask causing crash - See: feature_guide/BGTASK_TEMPORARY_DISABLE.md
// import 'core/services/chat_background_service.dart';
import 'package:firebase_core/firebase_core.dart';
import 'package:firebase_messaging/firebase_messaging.dart';
import 'core/services/fcm_service.dart';

List<CameraDescription> cameras = [];

Future<void> fetchEarlyData() async {
  AppLogger.d('--- fetchEarlyData: Fetching some initial data... ---', 'MAIN');
  AppLogger.d('--- fetchEarlyData: Initial data fetching complete. ---', 'MAIN');
}

void main() async {
  AppLogger.i('#### ULIN MAHONI APP STARTING UP ####', 'MAIN');
  WidgetsFlutterBinding.ensureInitialized();

  // Initialize Firebase FIRST with retry logic (before any Firebase service)
  bool firebaseInitialized = false;
  int retryCount = 0;
  const maxRetries = 3;

  while (!firebaseInitialized && retryCount < maxRetries) {
    try {
      await Firebase.initializeApp();
      firebaseInitialized = true;
      AppLogger.s('Firebase initialized successfully', 'MAIN');
    } catch (e, stackTrace) {
      retryCount++;
      AppLogger.e(
        'Firebase initialization failed (attempt $retryCount/$maxRetries)',
        e,
        stackTrace,
        'MAIN'
      );

      if (retryCount < maxRetries) {
        // Exponential backoff: 1s, 2s, 3s
        await Future.delayed(Duration(seconds: retryCount));
      } else {
        // After max retries, log critical error but don't crash
        AppLogger.e(
          'CRITICAL: Firebase initialization failed after $maxRetries attempts. '
          'FCM and Firebase features will be disabled.',
          e,
          stackTrace,
          'MAIN'
        );
      }
    }
  }

  // NetworkManager disabled — connectivity dialog causes false positives
  // await NetworkManager().initialize();
  await dotenv.load(fileName: ".env");

  // Only initialize Firebase-dependent services if Firebase is ready
  if (firebaseInitialized) {
    // Register background message handler (must be top-level function)
    FirebaseMessaging.onBackgroundMessage(firebaseMessagingBackgroundHandler);
    AppLogger.d('FCM background handler registered', 'MAIN');

    // Initialize notification services
    await LocalNotificationService().initialize();

    // TEMPORARY DISABLED: BGTask causing SIGABRT crash
    // TODO: Re-enable after fixing Bundle ID mismatch in Info.plist
    // See: feature_guide/BGTASK_TEMPORARY_DISABLE.md
    // await ChatBackgroundService().initialize();
    AppLogger.w('ChatBackgroundService temporarily disabled (BGTask crash fix)', 'MAIN');

    AppLogger.d('--- Notification services initialized ---', 'MAIN');

    // Initialize FCM Service
    try {
      await FCMService().initialize();
      AppLogger.s('FCM Service initialized', 'MAIN');
    } catch (e, stackTrace) {
      AppLogger.e('FCM Service initialization failed', e, stackTrace, 'MAIN');
    }
  } else {
    // Firebase failed - initialize only non-Firebase services
    AppLogger.w('Running in limited mode without Firebase features', 'MAIN');

    // Initialize services that don't require Firebase
    await LocalNotificationService().initialize();
    AppLogger.d('--- Local notification service initialized (limited mode) ---', 'MAIN');
  }

  SystemChrome.setPreferredOrientations([
    DeviceOrientation.portraitUp,
    DeviceOrientation.portraitDown,
  ]);
  AppLogger.d('--- SystemChrome.setPreferredOrientations() called ---', 'MAIN');

  await fetchEarlyData();

  ErrorWidget.builder = (FlutterErrorDetails details) {
    debugPrint('--- Global Flutter Error Caught ---');
    debugPrint('Exception: ${details.exception.toString()}');
    debugPrint('Stack: ${details.stack.toString()}');
    debugPrint('----------------------------------');
    return ErrorPage(
      errorMessage: details.exception.toString(),
      errorStack: details.stack,
    );
  };

  try {
    cameras = await availableCameras();
    AppLogger.d('--- availableCameras() called. Found ${cameras.length} cameras. ---', 'MAIN');
  } on CameraException catch (e) {
    AppLogger.e('--- CameraException occurred: ${e.code} - ${e.description} ---', e, StackTrace.current, 'MAIN');
  } catch (e) {
    AppLogger.e('--- An unexpected error occurred during camera initialization', e, StackTrace.current, 'MAIN');
  }

  runApp(const ProviderScope(
    child: MyApp(),
  ));
}

/// Root app widget. Wires light/dark themes and locale from Riverpod providers.
class MyApp extends ConsumerWidget {
  const MyApp({super.key});

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final currentLocale = ref.watch(localeProvider);
    // Watch theme mode from the theme provider (persisted, default: dark)
    final themeMode = ref.watch(themeProvider);

    return MaterialApp.router(
      title: 'Ulin Mahoni',
      // Light theme from central AppTheme
      theme: AppTheme.lightTheme,
      // Dark theme from central AppTheme
      darkTheme: AppTheme.darkTheme,
      // Theme mode from provider (dark/light/system)
      themeMode: themeMode,
      debugShowCheckedModeBanner: false,
      routerConfig: appRouter,
      locale: currentLocale,
      localizationsDelegates: AppLocalizations.localizationsDelegates,
      supportedLocales: AppLocalizations.supportedLocales,
      builder: (context, child) {
        // Initialize ping monitoring after first frame
        WidgetsBinding.instance.addPostFrameCallback((_) {
          PingMonitorService().initialize(context);
        });

        // NetworkConnectivityMonitor disabled — causes false positives on some devices.
        // Connectivity issues are handled by API error responses and FCM push.
        return child ?? const SizedBox.shrink();
      },
    );
  }
}
