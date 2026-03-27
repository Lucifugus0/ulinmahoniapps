import 'package:flutter_dotenv/flutter_dotenv.dart';

class ApiConfig {
  static String get baseUrl =>dotenv.env['BASE_URL'] ?? 'https://default-url.com';
  static String get apiKey => dotenv.env['API_KEY'] ?? '';

  /// Storage base URL — origin of BASE_URL used to resolve relative /storage/... paths
  /// returned by the backend (e.g., BASE_URL=https://staging.ulinmahoni.com/api/v1
  /// → storageBaseUrl=https://staging.ulinmahoni.com).
  static String get storageBaseUrl {
    final uri = Uri.tryParse(baseUrl);
    if (uri == null) return '';
    /* Include port only if non-standard (not 80/443) */
    final port = uri.hasPort && uri.port != 80 && uri.port != 443 ? ':${uri.port}' : '';
    return '${uri.scheme}://${uri.host}$port';
  }
  static String get loginUrl => '$baseUrl/auth/login';
  static String get registerUrl => '$baseUrl/auth/register';
  static String get forgotPasswordUrl => '$baseUrl/forgot-password';
  static String get updatePasswordUrl =>'$baseUrl/profile/{userId}/update-password';
  static String get detailpropertyUrl => '$baseUrl/property/{propertyId}';
  static String get roomdetailUrl => '$baseUrl/rooms/propertyId/{propertyId}';
  static String get roomById => '$baseUrl/rooms/{roomId}';
  static String get bookingUrl => '$baseUrl/booking';
  static String get propertybytagUrl => '$baseUrl/property?tags={tags}';
  static String get propertyUrl => '$baseUrl/property';
  static String get propertybycitiesUrl => '$baseUrl/property?cities={cities}';
  static String get mybookingUrl => '$baseUrl/booking/userId/{userId}';
  static String get uploadimageUrl => '$baseUrl/booking/{idrec}/upload';
  static String get updateprofileUrl => '$baseUrl/profile/{userId}';
  static String get propertybyprovinceUrl =>'$baseUrl/property?province={province}';
  static String get propertybyidUrl => '$baseUrl/property/{propertyId}';
  static String get roombyidUrl => '$baseUrl/rooms/{roomId}';
  static String get checkavailabilityUrl =>'$baseUrl/booking/check-availability';
  // Multi-Tier Pricing: price preview endpoint for per-date breakdown
  static String roomPricePreview(String roomId) => '$baseUrl/rooms/$roomId/price-preview';
  /* Daily Multi Tier Pricing: search rooms endpoint with availability and per-date pricing */
  static String get searchRoomsUrl => '$baseUrl/search/rooms';
  static String get updateattachmentUrl =>'$baseUrl/booking/{idrec}/update-attachment';
  static String get mybookingidUrl => '$baseUrl/booking/{idrec}';
  static String get deactiveaccountUrl => '$baseUrl/users/{userId}/deactivate';
  static String get userById => "$baseUrl/users/{userId}";
  static String get allUsers => "$baseUrl/users/";
  static String get checkIn => "$baseUrl/booking/{orderid}/check-in";
  static String get uploadPicture => "$baseUrl/profile/{idrec}/profile-picture";
  static String get validateVoucher => "$baseUrl/voucher/validate";
  static String get applyVoucher => "$baseUrl/voucher/apply";
  static String get dokuGenerateVA => "$baseUrl/doku/test-generate-va";
  static String get dokuGenerateQRIS => "$baseUrl/doku/test-generate-qris";
  static String get dokuGenerateCC => "$baseUrl/doku/test-generate-cc";
  static String get updatePaymentMethod => "$baseUrl/booking/{idrec}/payment-method";

  // Chat endpoints
  static String get chatConversations => "$baseUrl/chat/conversations";
  static String chatConversationById(int conversationId) => "$baseUrl/chat/conversations/$conversationId";
  static String chatConversationMessages(int conversationId) => "$baseUrl/chat/conversations/$conversationId/messages";
  static String chatConversationRead(int conversationId) => "$baseUrl/chat/conversations/$conversationId/read";
  static String chatMessageById(int messageId) => "$baseUrl/chat/messages/$messageId";

  // Promo Banner endpoints - Public access
  static String get promoBanners => "$baseUrl/promo-banner";
  static String promoBannerById(int bannerId) => "$baseUrl/promo-banner?id=$bannerId";

  // Promo Banner endpoints - Admin only (for future admin panel)
  static String get promoBannerImages => "$baseUrl/promo-banner";
  static String promoBannerbyId(int bannerId) => "$baseUrl/promo-banner?id=$bannerId";

  /// Ticket endpoints — customer service ticketing system
  static String get ticketCategories => "$baseUrl/tickets/categories";
  static String get ticketEligibility => "$baseUrl/tickets/eligibility";
  static String get ticketEligibleBookings => "$baseUrl/tickets/eligible-bookings";
  static String get tickets => "$baseUrl/tickets";
  static String ticketById(int id) => "$baseUrl/tickets/$id";
  static String ticketMessages(int id) => "$baseUrl/tickets/$id/messages";
  static String ticketRead(int id) => "$baseUrl/tickets/$id/read";
  static String ticketClose(int id) => "$baseUrl/tickets/$id/close";
  static String ticketReopen(int id) => "$baseUrl/tickets/$id/reopen";
  /// Broadcast endpoints — one-way announcements
  static String get broadcasts => "$baseUrl/broadcasts";
  static String broadcastById(int id) => "$baseUrl/broadcasts/$id";

  // FCM (Firebase Cloud Messaging) endpoints
  // POST /device-token  → register/update device token (upsert by backend)
  // DELETE /device-token → remove device token on logout
  static String get fcmToken => "$baseUrl/device-token";
  static String get fcmTokenDelete => "$baseUrl/device-token";

  static const dokuUrl = "https://api-sandbox.doku.com";
  static const dokuPayment ="$dokuUrl/checkout/v1/payment"; // Ini boleh const krn dokuUrl juga const
  static const dokuPaymentPath = "/checkout/v1/payment";
  static const dokuClientId = "BRN-0205-1761118951136";
  static const dokuRequestId = "93626957-8ebe-4e0e-9778-3a1a623ea18b";
  static const dokuSecretKey = "SK-ZeP6ayrLAQopEpTotpiO";

  static Map<String, String> get defaultHeaders => {
    'x-api-key': apiKey,
    'Accept': 'application/json',
    'Cache-Control': 'no-cache',
  };
}
