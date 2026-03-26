import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/features/auth/forgotpassword/presentation/pages/forgotpassword_page.dart';
import 'package:ulinmahoniapps/features/auth/login/presentation/pages/login_page.dart';
import 'package:ulinmahoniapps/features/auth/register/presentation/pages/register_page.dart';
import 'package:ulinmahoniapps/features/auth/updatepassword/presentation/pages/updatepassword_page.dart';
import 'package:ulinmahoniapps/features/auth/welcoming/presentation/pages/welcoming_page.dart';
import 'package:ulinmahoniapps/features/book/detailproperty/model/detailproperty_model.dart';
import 'package:ulinmahoniapps/features/book/roomdetails/presentation/pages/roomdetails_page.dart';
import 'package:ulinmahoniapps/features/book/detailproperty/presentation/pages/detailproperty_page.dart';
import 'package:ulinmahoniapps/features/error/presentation/pages/errorpage.dart';
import 'package:ulinmahoniapps/features/help/presentation/pages/help_page.dart';
import 'package:ulinmahoniapps/features/searchresult/presentation/pages/searchresult_page.dart';
import 'package:ulinmahoniapps/features/comingsoon/presentation/pages/comingsoon.dart';
import 'package:ulinmahoniapps/features/mybooking/mybookingdetails/presentation/pages/mybookingdetails_page.dart';
import 'package:ulinmahoniapps/features/book/payment/presentation/pages/paymentpage.dart';
import 'package:ulinmahoniapps/features/profiles/updateprofile/presentation/pages/updateprofilepage.dart';
import 'package:ulinmahoniapps/features/home/presentation/pages/homepage.dart';
import 'package:ulinmahoniapps/features/mybooking/mybooking/presentation/pages/mybooking_page.dart';
import 'package:ulinmahoniapps/features/profiles/viewprofile/presentation/pages/profilepage.dart';
import 'package:ulinmahoniapps/features/propertytype/presentation/pages/propertytypepage.dart';
import 'package:ulinmahoniapps/features/customerservice/presentation/pages/chatroom_page.dart';
import 'package:ulinmahoniapps/features/customerservice/presentation/pages/ticket_list_page.dart';
import 'package:ulinmahoniapps/features/customerservice/presentation/pages/create_ticket_page.dart';
import 'package:ulinmahoniapps/features/customerservice/presentation/pages/ticket_chat_page.dart';
import 'package:ulinmahoniapps/features/customerservice/presentation/pages/broadcast_detail_page.dart';
import 'package:ulinmahoniapps/features/promo_banner/presentation/pages/promo_detail_page.dart';
import 'package:ulinmahoniapps/core/layout/mainlayout.dart';
import '../features/book/roomdetails/model/rooms_model.dart';
import 'route_constants.dart';
import '../core/utils/app_logger.dart';

final GoRouter appRouter = GoRouter(
  initialLocation: RoutePaths.welcome, // Start with welcoming page
  routes: [
    // ShellRoute untuk Bottom Navigation
    ShellRoute(
      builder: (context, state, child) {
        final path = state.uri.toString();
        final currentIndex = _getCurrentIndex(path);
        return MainLayout(currentIndex: currentIndex, child: child);
      },
      routes: [
        GoRoute(
          path: RoutePaths.home,
          name: RouteNames.home,
          builder: (context, state) => const HomePage(),
        ),
        GoRoute(
          path: RoutePaths.myBooking,
          name: RouteNames.myBooking,
          builder: (context, state) => const MyBookingPage(),
        ),
        GoRoute(
          path: RoutePaths.comingSoon,
          name: RouteNames.comingSoon,
          builder: (context, state) => const ComingSoonPage(),
        ),
        /// Changed to TicketListPage — now the main CS landing page with ticket/broadcast tabs
        GoRoute(
          path: RoutePaths.customerService,
          name: RouteNames.customerService,
          builder: (context, state) => const TicketListPage(),
        ),
        GoRoute(
          path: RoutePaths.profile,
          name: RouteNames.profile,
          builder: (context, state) => const ProfilePage(),
        ),
        // NOTE: Pindahkan rute ini keluar ShellRoute jika Anda ingin menyembunyikan bottom bar
        GoRoute(
          path: RoutePaths.myBookingDetails,
          name: RouteNames.myBookingDetails,
          builder: (context, GoRouterState state) {
            final bookingData = state.extra as Map<String, dynamic>;
            return MyBookingDetail(bookingData: bookingData);
          },
        ),
        GoRoute(
          path: RoutePaths.error,
          name: RouteNames.error,
          builder: (context, state) {
            final errorMessage = state.extra as String? ?? 'Unknown error';
            return ErrorPage(errorMessage: errorMessage);
          },
        ),
        GoRoute(
          path: RoutePaths.promoBannerDetail, // '/promo/:id'
          name: RouteNames.promoBannerDetail,
          builder: (context, state) {
            final bannerIdString = state.pathParameters['id'];
            final bannerId = int.tryParse(bannerIdString ?? '');
            if (bannerId == null) {
              return const Scaffold(
                body: Center(child: Text('Invalid promo ID')),
              );
            }
            return PromoDetailPage(bannerId: bannerId);
          },
        ),
      ],
    ),

    // Standard Routes
    GoRoute(
      path: RoutePaths.browseAll,
      name: RouteNames.browseAll,
      builder: (context, state) => const PropertyTypePage(),
    ),
    GoRoute(
      path: RoutePaths.roomDetails, // '/roomdetails/:id'
      name: RouteNames.roomDetails,
      builder: (context, state) {
        final extra = state.extra as Map<String, dynamic>?;

        if (extra == null) {
          return const Scaffold(
            body: Center(child: Text('No data provided')),
          );
        }

        final room = extra['room'] as RoomModel;
        final property = extra['property'] as DetailPropertyModel;

        return RoomDetailsPage(
          room: room,
          propertyData: property,
        );
      },
    ),
    GoRoute(
      path: RoutePaths.search,
      name: RouteNames.search,
      builder: (context, state) => SearchResult(),
    ),
    GoRoute(
      path: RoutePaths.detailProperty, // '/detailproperty/:id'
      name: RouteNames.detailProperty,
      builder: (context, state) {
        final idString = state.pathParameters['id'];
        final id = int.tryParse(idString ?? '');
        if (id == null) {
          return const Scaffold(body: Center(child: Text('Invalid ID')));
        }
        return DetailPropertyPage(id: id);
      },
    ),
    GoRoute(
      path: RoutePaths.payment,
      name: RouteNames.payment,
      builder: (context, state) {
        final extra = state.extra as Map<String, dynamic>;

        // Debug logging
        AppLogger.d('Payment route - extra keys: ${extra.keys.toList()}', 'ROUTER');
        AppLogger.d('Payment route - is_renewal value: ${extra['is_renewal']}', 'ROUTER');

        // Check if this is a renewal booking
        final isRenewal = extra['is_renewal'] as bool? ?? false;
        AppLogger.d('Payment route - isRenewal after cast: $isRenewal', 'ROUTER');

        if (isRenewal) {
          // For renewal, room and propertyData might be null
          // We'll handle this in PaymentPage
          return PaymentPage(
            room: extra['room'] as RoomModel?,
            propertyData: extra['propertyData'] as DetailPropertyModel?,
            rentType: extra['rentType'] as String?,
            duration: extra['duration'] as int?,
            checkInDate: extra['checkInDate'] as DateTime?,
            checkOutDate: extra['checkOutDate'] as DateTime?,
            isRenewal: isRenewal,
            originalOrderId: extra['original_order_id'] as String?,
            originalCheckIn: extra['original_check_in'] as String?,
            originalCheckOut: extra['original_check_out'] as String?,
            newCheckIn: extra['new_check_in'] as String?,
            newCheckOut: extra['new_check_out'] as String?,
            roomId: extra['room_id'] as int?,
            propertyId: extra['property_id'] as int?,
            propertyName: extra['property_name'] as String?,
            roomName: extra['room_name'] as String?,
            bookingType: extra['booking_type'] as String?,
            dailyPrice: extra['daily_price'] as double?,
            monthlyPrice: extra['monthly_price'] as double?,
          );
        }

        // Normal booking flow
        final room = extra['room'] as RoomModel?;
        final propertyData = extra['propertyData'] as DetailPropertyModel?;
        final rentType = extra['rentType'] as String?;
        final duration = extra['duration'] as int?;
        final checkInDate = extra['checkInDate'] as DateTime?;
        final checkOutDate = extra['checkOutDate'] as DateTime?;

        // If normal flow data is missing, something is wrong
        if (room == null || propertyData == null || rentType == null ||
            duration == null || checkInDate == null || checkOutDate == null) {
          throw Exception('Missing required payment data for normal booking flow');
        }

        return PaymentPage(
          room: room,
          propertyData: propertyData,
          rentType: rentType,
          duration: duration,
          checkInDate: checkInDate,
          checkOutDate: checkOutDate,
        );
      },
    ),
    GoRoute(
      path: RoutePaths.updateProfile,
      name: RouteNames.updateProfile,
      builder: (context, state) => UpdateProfile(),
    ),
    GoRoute(
      path: RoutePaths.welcome,
      name: RouteNames.welcome,
      builder: (context, state) => WelcomePage(),
    ),
    GoRoute(
      path: RoutePaths.register,
      name: RouteNames.register,
      builder: (context, state) => RegisterPage(),
    ),
    GoRoute(
      path: RoutePaths.login,
      name: RouteNames.login,
      builder: (context, state) => LoginPage(),
    ),
    GoRoute(
      path: RoutePaths.forgotPassword,
      name: RouteNames.forgotPassword,
      builder: (context, state) => ForgotPasswordPage(),
    ),
    GoRoute(
      path: RoutePaths.help,
      name: RouteNames.help,
      builder: (context, state) => HelpPage(),
    ),
    GoRoute(
      path: RoutePaths.updatePassword,
      name: RouteNames.updatePassword,
      builder: (context, state) => UpdatePasswordPage(),
    ),
    /// Existing chat route — kept for backward compatibility with old CS chat flow
    GoRoute(
      path: RoutePaths.chat, // '/cs/chat/:conversationId'
      name: RouteNames.chat,
      builder: (context, state) {
        final conversationIdString = state.pathParameters['conversationId'];
        final conversationId = int.tryParse(conversationIdString ?? '');

        if (conversationId == null) {
          return const Scaffold(
            body: Center(child: Text('Invalid conversation ID')),
          );
        }

        final extra = state.extra as Map<String, dynamic>?;

        return ChatRoomPage(
          conversationId: conversationId,
          orderId: extra?['orderId'] as String?,
          recipientType: extra?['recipientType'] as String?,
          roomName: extra?['roomName'] as String?,
        );
      },
    ),

    /// Create ticket page — multi-step wizard for submitting a new support ticket
    GoRoute(
      path: RoutePaths.createTicket, // '/cs/create'
      name: RouteNames.createTicket,
      builder: (context, state) => const CreateTicketPage(),
    ),

    /// Ticket chat page — displays messages and allows interaction for a specific ticket
    GoRoute(
      path: RoutePaths.ticketChat, // '/cs/ticket/:ticketId'
      name: RouteNames.ticketChat,
      builder: (context, state) {
        final ticketIdString = state.pathParameters['ticketId'];
        final ticketId = int.tryParse(ticketIdString ?? '');

        if (ticketId == null) {
          return const Scaffold(
            body: Center(child: Text('Invalid ticket ID')),
          );
        }

        return TicketChatPage(ticketId: ticketId);
      },
    ),

    /// Broadcast detail page — read-only view of a single broadcast announcement
    GoRoute(
      path: RoutePaths.broadcastDetail, // '/cs/broadcast/:broadcastId'
      name: RouteNames.broadcastDetail,
      builder: (context, state) {
        final broadcastIdString = state.pathParameters['broadcastId'];
        final broadcastId = int.tryParse(broadcastIdString ?? '');

        if (broadcastId == null) {
          return const Scaffold(
            body: Center(child: Text('Invalid broadcast ID')),
          );
        }

        return BroadcastDetailPage(broadcastId: broadcastId);
      },
    ),
  ],
);

int _getCurrentIndex(String path) {
  if (path.startsWith(RoutePaths.home)) return 0;
  if (path.startsWith(RoutePaths.myBooking)) return 1;
  if (path.startsWith(RoutePaths.customerService)) return 3;
  if (path.startsWith(RoutePaths.profile)) return 4;
  return 0;
}