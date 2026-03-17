import 'dart:async';
import 'package:http/http.dart' as http;
// import '../utils/app_logger.dart';

/// Helper utility untuk melakukan ping ke DNS servers
/// Menggunakan HTTP HEAD request untuk simulate ping
class PingHelper {
  /// Perform ping ke single host
  /// Returns latency dalam milliseconds, atau null jika gagal
  static Future<int?> ping(String host) async {
    try {
      final stopwatch = Stopwatch()..start();

      // Gunakan HTTP HEAD request untuk simulate ping
      final response = await http.head(
        Uri.parse('http://$host'),
      ).timeout(
        const Duration(seconds: 5),
        onTimeout: () {
          throw TimeoutException('Ping timeout to $host');
        },
      );

      stopwatch.stop();

      // Check if response is successful
      if (response.statusCode >= 200 && response.statusCode < 300) {
        final latency = stopwatch.elapsedMilliseconds;
        // AppLogger.d('Ping to $host: ${latency}ms', 'PING');
        return latency;
      }

      // AppLogger.w('Ping to $host failed with status ${response.statusCode}', 'PING');
      return null;
    } on TimeoutException catch (e) {
      // AppLogger.w('Ping timeout to $host: $e', 'PING');
      return null;
    } catch (e) {
      // AppLogger.w('Ping error to $host: $e', 'PING');
      return null;
    }
  }

  /// Ping multiple hosts dan return average latency
  /// Returns average dalam milliseconds, atau null jika semua gagal
  static Future<int?> averagePing(List<String> hosts) async {
    final results = <int>[];

    for (final host in hosts) {
      final latency = await ping(host);
      if (latency != null) {
        results.add(latency);
      }
    }

    if (results.isEmpty) {
      // AppLogger.w('All pings failed', 'PING');
      return null;
    }

    final average = (results.reduce((a, b) => a + b) / results.length).round();
    // AppLogger.d('Average ping from ${results.length} successful hosts: ${average}ms', 'PING');
    return average;
  }
}
