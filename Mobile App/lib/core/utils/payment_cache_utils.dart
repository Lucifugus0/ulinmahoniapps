import 'package:shared_preferences/shared_preferences.dart';
import 'app_logger.dart';

/// Utility class for managing payment-related cache
///
/// Handles QR content and Credit Card payment links for single device usage
class PaymentCacheUtils {
  // Keys for QR Content
  static String _qrContentKey(String bookingId) => 'payment_qr_content_$bookingId';
  static String _qrOrderIdKey(String bookingId) => 'payment_qr_order_id_$bookingId';
  static String _qrUserIdKey(String bookingId) => 'payment_qr_user_id_$bookingId';
  static String _qrExpiredAtKey(String bookingId) => 'payment_qr_expired_at_$bookingId';

  // Keys for CC Link
  static String _ccLinkKey(String bookingId) => 'payment_cc_link_$bookingId';
  static String _ccInvoiceKey(String bookingId) => 'payment_cc_invoice_$bookingId';
  static String _ccUserIdKey(String bookingId) => 'payment_cc_user_id_$bookingId';
  static String _ccExpiredAtKey(String bookingId) => 'payment_cc_expired_at_$bookingId';

  /// Save QR content to SharedPreferences
  static Future<void> saveQRContent({
    required String bookingId,
    required String qrContent,
    required String orderId,
    required String userId,
    required DateTime expiredAt,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      await prefs.setString(_qrContentKey(bookingId), qrContent);
      await prefs.setString(_qrOrderIdKey(bookingId), orderId);
      await prefs.setString(_qrUserIdKey(bookingId), userId);
      await prefs.setString(_qrExpiredAtKey(bookingId), expiredAt.toIso8601String());

      AppLogger.d(
        'QR content saved for booking $bookingId (order: $orderId, user: $userId, expires: $expiredAt)',
        'PAYMENT-CACHE',
      );
    } catch (e) {
      AppLogger.e('Failed to save QR content', e, StackTrace.current, 'PAYMENT-CACHE');
    }
  }

  /// Get QR content with validation
  /// Returns null if validation fails
  static Future<Map<String, String>?> getQRContent({
    required String bookingId,
    required String currentOrderId,
    required String currentUserId,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      final qrContent = prefs.getString(_qrContentKey(bookingId));
      final savedOrderId = prefs.getString(_qrOrderIdKey(bookingId));
      final savedUserId = prefs.getString(_qrUserIdKey(bookingId));
      final expiredAtString = prefs.getString(_qrExpiredAtKey(bookingId));

      // Check if data exists
      if (qrContent == null || savedOrderId == null || savedUserId == null || expiredAtString == null) {
        AppLogger.d('QR content not found for booking $bookingId', 'PAYMENT-CACHE');
        return null;
      }

      // VALIDATION 1: Order ID match
      if (savedOrderId != currentOrderId) {
        AppLogger.w(
          'QR order ID mismatch (saved: $savedOrderId, current: $currentOrderId)',
          'PAYMENT-CACHE',
        );
        await clearQRContent(bookingId);
        return null;
      }

      // VALIDATION 2: User ID match
      if (savedUserId != currentUserId) {
        AppLogger.w(
          'QR user ID mismatch (saved: $savedUserId, current: $currentUserId)',
          'PAYMENT-CACHE',
        );
        await clearQRContent(bookingId);
        return null;
      }

      // VALIDATION 3: Check expiry (15 minutes from created_at)
      final expiredAt = DateTime.parse(expiredAtString);
      if (DateTime.now().isAfter(expiredAt)) {
        AppLogger.w(
          'QR content expired for booking $bookingId (expired at: $expiredAt)',
          'PAYMENT-CACHE',
        );
        await clearQRContent(bookingId);
        return null;
      }

      AppLogger.d(
        'QR content retrieved for booking $bookingId (valid)',
        'PAYMENT-CACHE',
      );

      return {
        'qrContent': qrContent,
        'orderId': savedOrderId,
        'userId': savedUserId,
        'expiredAt': expiredAtString,
      };
    } catch (e) {
      AppLogger.e('Failed to get QR content', e, StackTrace.current, 'PAYMENT-CACHE');
      return null;
    }
  }

  /// Clear QR content for a booking
  static Future<void> clearQRContent(String bookingId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_qrContentKey(bookingId));
      await prefs.remove(_qrOrderIdKey(bookingId));
      await prefs.remove(_qrUserIdKey(bookingId));
      await prefs.remove(_qrExpiredAtKey(bookingId));

      AppLogger.d('QR content cleared for booking $bookingId', 'PAYMENT-CACHE');
    } catch (e) {
      AppLogger.e('Failed to clear QR content', e, StackTrace.current, 'PAYMENT-CACHE');
    }
  }

  /// Save Credit Card payment link to SharedPreferences
  static Future<void> saveCCLink({
    required String bookingId,
    required String paymentUrl,
    required String invoiceNumber,
    required String userId,
    required DateTime expiredAt,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      await prefs.setString(_ccLinkKey(bookingId), paymentUrl);
      await prefs.setString(_ccInvoiceKey(bookingId), invoiceNumber);
      await prefs.setString(_ccUserIdKey(bookingId), userId);
      await prefs.setString(_ccExpiredAtKey(bookingId), expiredAt.toIso8601String());

      AppLogger.d(
        'CC link saved for booking $bookingId (invoice: $invoiceNumber, user: $userId, expires: $expiredAt)',
        'PAYMENT-CACHE',
      );
    } catch (e) {
      AppLogger.e('Failed to save CC link', e, StackTrace.current, 'PAYMENT-CACHE');
    }
  }

  /// Get Credit Card payment link with validation
  /// Returns null if validation fails
  static Future<Map<String, String>?> getCCLink({
    required String bookingId,
    required String currentUserId,
  }) async {
    try {
      final prefs = await SharedPreferences.getInstance();

      final ccLink = prefs.getString(_ccLinkKey(bookingId));
      final savedInvoiceNumber = prefs.getString(_ccInvoiceKey(bookingId));
      final savedUserId = prefs.getString(_ccUserIdKey(bookingId));
      final expiredAtString = prefs.getString(_ccExpiredAtKey(bookingId));

      // Check if data exists
      if (ccLink == null || savedInvoiceNumber == null || savedUserId == null || expiredAtString == null) {
        AppLogger.d('CC link not found for booking $bookingId', 'PAYMENT-CACHE');
        return null;
      }

      // VALIDATION 1: User ID match
      if (savedUserId != currentUserId) {
        AppLogger.w(
          'CC link user ID mismatch (saved: $savedUserId, current: $currentUserId)',
          'PAYMENT-CACHE',
        );
        await clearCCLink(bookingId);
        return null;
      }

      // VALIDATION 2: Check expiry (15 minutes from created_at)
      final expiredAt = DateTime.parse(expiredAtString);
      if (DateTime.now().isAfter(expiredAt)) {
        AppLogger.w(
          'CC link expired for booking $bookingId (expired at: $expiredAt)',
          'PAYMENT-CACHE',
        );
        await clearCCLink(bookingId);
        return null;
      }

      AppLogger.d(
        'CC link retrieved for booking $bookingId (valid)',
        'PAYMENT-CACHE',
      );

      return {
        'paymentUrl': ccLink,
        'invoiceNumber': savedInvoiceNumber,
        'userId': savedUserId,
        'expiredAt': expiredAtString,
      };
    } catch (e) {
      AppLogger.e('Failed to get CC link', e, StackTrace.current, 'PAYMENT-CACHE');
      return null;
    }
  }

  /// Clear Credit Card link for a booking
  static Future<void> clearCCLink(String bookingId) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      await prefs.remove(_ccLinkKey(bookingId));
      await prefs.remove(_ccInvoiceKey(bookingId));
      await prefs.remove(_ccUserIdKey(bookingId));
      await prefs.remove(_ccExpiredAtKey(bookingId));

      AppLogger.d('CC link cleared for booking $bookingId', 'PAYMENT-CACHE');
    } catch (e) {
      AppLogger.e('Failed to clear CC link', e, StackTrace.current, 'PAYMENT-CACHE');
    }
  }

  /// Clear all payment cache data
  static Future<void> clearAllPaymentCache() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final keys = prefs.getKeys();

      int count = 0;
      for (final key in keys) {
        if (key.startsWith('payment_')) {
          await prefs.remove(key);
          count++;
        }
      }

      AppLogger.d('All payment cache cleared ($count keys removed)', 'PAYMENT-CACHE');
    } catch (e) {
      AppLogger.e('Failed to clear all payment cache', e, StackTrace.current, 'PAYMENT-CACHE');
    }
  }
}
