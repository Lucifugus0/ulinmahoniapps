// import 'package:flutter_riverpod/flutter_riverpod.dart';
// import 'package:http/http.dart' as http;
// import 'package:ulinmahoniapps/features/book/payment/data/paymentgateaway_services.dart';
// import 'package:ulinmahoniapps/features/book/payment/provider/payment_provider.dart';
//
// /// Provider untuk PaymentGatewayNotifier (khusus DOKU)
// final paymentGatewayNotifierProvider =
// StateNotifierProvider<PaymentGatewayNotifier, PaymentState>(
//       (ref) => PaymentGatewayNotifier(),
// );
//
// /// Notifier untuk mengelola proses pembayaran ke DOKU
// class PaymentGatewayNotifier extends StateNotifier<PaymentState> {
//   PaymentGatewayNotifier() : super(PaymentState.initial());
//
//   /// Kirim permintaan pembayaran ke DOKU API
//   Future<void> postBooking({
//     required int amount,
//     required Map<String, dynamic> customer,
//     required List<Map<String, dynamic>> lineItems,
//     String transactionType = 'checkout',
//   }) async {
//     state = state.copyWith(postBookingResult: const AsyncLoading());
//
//     try {
//       // ✅ Bentuk body JSON sesuai format DOKU API
//       final body = {
//         "order": {
//           "amount": amount,
//           "invoice_number": "INV-${DateTime.now().millisecondsSinceEpoch}",
//           "currency": "IDR",
//           "session_id": "session-${DateTime.now().millisecondsSinceEpoch}",
//           "callback_url": "https://doku.com/",
//           "line_items": lineItems,
//         },
//         "payment": {
//           "payment_due_date": 1440, // default 1 hari
//         },
//         "customer": customer,
//       };
//
//       print('➡️ PAYMENT BODY: $body');
//
//       // ✅ Kirim request ke PaymentGatewayService
//       final http.Response response =
//       await PaymentGatewayService.initiatePayment(paymentData: body);
//
//       print('✅ PAYMENT STATUS CODE: ${response.statusCode}');
//       print('✅ PAYMENT RESPONSE BODY: ${response.body}');
//
//       if (response.statusCode == 200 || response.statusCode == 201) {
//         state = state.copyWith(
//           postBookingResult:
//           AsyncData('Pembayaran berhasil: ${response.body}'),
//         );
//       } else {
//         throw Exception(
//             'Pembayaran gagal. Kode: ${response.statusCode}, Body: ${response.body}');
//       }
//     } catch (e, st) {
//       print('❌ PAYMENT ERROR: $e');
//       state = state.copyWith(postBookingResult: AsyncError(e, st));
//     }
//   }
// }
