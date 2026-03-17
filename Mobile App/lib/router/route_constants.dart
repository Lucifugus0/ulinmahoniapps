class RouteNames {
  // Auth Routes
  static const String splash = 'SPLASH';
  static const String login = 'LOGIN';
  static const String register = 'REGISTER';
  static const String welcome = 'WELCOME';
  static const String forgotPassword = 'FORGOT_PASSWORD';
  static const String updatePassword = 'UPDATE_PASSWORD';

  // Main (Shell) Routes
  static const String home = 'HOME';
  static const String myBooking = 'MY_BOOKING';
  static const String comingSoon = 'COMING_SOON';
  static const String customerService = 'CUSTOMER_SERVICE';
  static const String profile = 'PROFILE';

  // Sub-pages inside Shell (or outside depending on logic)
  static const String myBookingDetails = 'MY_BOOKING_DETAILS';
  static const String chatRecipientSelection = 'CHAT_RECIPIENT_SELECTION';
  static const String chat = 'CHAT';
  static const String error = 'ERROR';

  // Standalone Routes
  static const String browseAll = 'BROWSE_ALL';
  static const String search = 'SEARCH';
  static const String help = 'HELP';

  // Profile Related
  static const String updateProfile = 'UPDATE_PROFILE';

  // Transaction & Details
  static const String roomDetails = 'ROOM_DETAILS';
  static const String detailProperty = 'DETAIL_PROPERTY';
  static const String payment = 'PAYMENT';

  // Promo Banner
  static const String promoBannerDetail = 'PROMO_BANNER_DETAIL';
}

class RoutePaths {
  // Root
  static const String root = '/';

  // Auth
  static const String splash = '/splash';
  static const String login = '/login';
  static const String register = '/register';
  static const String welcome = '/welcome';
  static const String forgotPassword = '/forgetpassword';
  static const String updatePassword = '/updatepassword';

  // Main
  static const String home = '/home';
  static const String myBooking = '/mybooking';
  static const String comingSoon = '/comingsoon';
  static const String customerService = '/cs';
  static const String profile = '/profile';

  // Details
  static const String myBookingDetails = '/mybookingdetails';
  static const String chatRecipientSelection = '/cs/recipient';
  static const String chat = '/cs/chat/:conversationId';
  static const String error = '/error';
  static const String browseAll = '/browse-all';
  static const String search = '/search';
  static const String help = '/help';
  static const String updateProfile = '/updateprofile';
  static const String payment = '/payment';

  // Parameterized Paths
  // Note: GoRouter expects ":id" in the definition, but when pushing you replace it.
  static const String roomDetails = '/roomdetails/:id';
  static const String detailProperty = '/detailproperty/:id';
  static const String promoBannerDetail = '/promo/:id';
}