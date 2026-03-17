import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:ulinmahoniapps/core/layout/mainlayout.dart';
import 'package:ulinmahoniapps/core/widgets/appbar.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/features/mybooking/mybooking/provider/mybooking_provider.dart';
import 'package:ulinmahoniapps/features/mybooking/mybooking/controller/mybooking_controller.dart';
import 'package:ulinmahoniapps/features/mybooking/mybooking/model/mybooking_model.dart';
import 'package:ulinmahoniapps/core/widgets/biometric_auth.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';
import 'package:ulinmahoniapps/features/auth/login/provider/auth_provider.dart';
import 'package:ulinmahoniapps/features/customerservice/provider/chat_provider.dart';
import '../widgets/booking_select_card.dart';
import '../widgets/recipient_selection_dialog.dart';

/// Customer Service landing page
/// User selects a booking to start a chat with FO or HO
class CustomerServicePage extends ConsumerStatefulWidget {
  const CustomerServicePage({super.key});

  @override
  ConsumerState<CustomerServicePage> createState() => _CustomerServicePageState();
}

class _CustomerServicePageState extends ConsumerState<CustomerServicePage> {
  final BiometricAuthService _biometricAuthService = BiometricAuthService();

  @override
  void initState() {
    super.initState();
    _authenticateBiometricsOnLoad();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.invalidate(userBookingsProvider);
    });
  }

  Future<void> _authenticateBiometricsOnLoad() async {
    if (!mounted) return;

    final didAuthenticate = await _biometricAuthService.authenticateOnLoad(context);
    if (!mounted) return;

    if (!didAuthenticate) {
      AppLogger.w('Biometric authentication failed, returning to home', 'BIOMETRIC');
      context.go('/home');
    } else {
      AppLogger.s('Biometric authentication successful', 'BIOMETRIC');
    }
  }

  Future<void> _onRefresh() async {
    ref.invalidate(userBookingsProvider);
    await ref.read(userBookingsProvider.future).catchError((_) {
      // Return empty list on error
      return <MyBookingModel>[];
    });
    AppLogger.s('Data refreshed successfully', 'CS');
  }

  void _onBookingSelected(MyBookingModel booking) {
    // Show recipient selection dialog
    showDialog(
      context: context,
      builder: (context) => RecipientSelectionDialog(
        booking: booking,
        onRecipientSelected: (recipientType) async {
          Navigator.of(context).pop();
          await _createConversationAndNavigate(booking, recipientType);
        },
      ),
    );
  }

  Future<void> _createConversationAndNavigate(
    MyBookingModel booking,
    String recipientType,
  ) async {
    final localizations = AppLocalizations.of(context)!;

    // Get user ID from auth state
    final authState = ref.read(authProvider);
    final user = authState.user.value;

    if (user == null) {
      _showErrorSnackbar(localizations.chatInvalidBooking);
      return;
    }

    // Show loading dialog
    if (!mounted) return;
    showDialog(
      context: context,
      barrierDismissible: false,
      builder: (context) => Center(
        child: Container(
          padding: const EdgeInsets.all(24),
          decoration: BoxDecoration(
            color: Colors.white,
            borderRadius: BorderRadius.circular(12),
          ),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              const CircularProgressIndicator(),
              const SizedBox(height: 16),
              Text(
                localizations.chatCreatingConversation,
                style: const TextStyle(fontSize: 14),
              ),
            ],
          ),
        ),
      ),
    );

    try {
      // Prepare conversation title
      final title = recipientType == 'fo'
          ? localizations.chatWithFrontOffice
          : localizations.chatWithHeadOffice;

      // Create conversation via chat controller
      final chatController = ref.read(chatControllerProvider.notifier);
      final conversationId = await chatController.createConversation(
        userId: user.id,
        orderId: booking.orderId,
        title: title,
        initialMessage: localizations.chatStartConversation,
      );

      // Close loading dialog using root navigator
      if (!mounted) return;
      Navigator.of(context, rootNavigator: true).pop();

      if (conversationId != null) {
        // Navigate to chat room
        if (!mounted) return;
        context.push(
          '/cs/chat/$conversationId',
          extra: {
            'orderId': booking.orderId,
            'recipientType': recipientType,
            'roomName': booking.roomName,
          },
        );

        AppLogger.s(
          'Conversation created: $conversationId for ${booking.orderId}',
          'CS',
        );
      } else {
        // Check for error message
        final chatState = ref.read(chatControllerProvider);
        _showErrorSnackbar(
          chatState.error ?? localizations.chatMessageFailed,
        );
      }
    } catch (e, stack) {
      AppLogger.e('Error creating conversation', e, stack, 'CS');

      // Close loading dialog safely using root navigator
      if (mounted) {
        Navigator.of(context, rootNavigator: true).pop();
      }

      // Show error snackbar
      if (mounted) {
        _showErrorSnackbar(localizations.chatUnknownError);
      }
    }
  }

  void _showErrorSnackbar(String message) {
    if (!mounted) return;
    ScaffoldMessenger.of(context).showSnackBar(
      SnackBar(
        content: Text(message),
        backgroundColor: Colors.red.shade400,
        behavior: SnackBarBehavior.floating,
        margin: const EdgeInsets.all(16),
        shape: RoundedRectangleBorder(
          borderRadius: BorderRadius.circular(8),
        ),
      ),
    );
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;

    if (!checkLoginAndRedirect(context, ref)) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    final bookingsAsync = ref.watch(userBookingsProvider);

    return MainLayout(
      showNavBar: false,
      showBottomNav: false,
      currentIndex: 3,
      child: Column(
        children: [
          CustomAppBar(
            title: localizations.csTitle,
            showBackButton: true,
          ),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _onRefresh,
              color: AppColors.primaryColor,
              child: bookingsAsync.when(
                data: (bookings) {
                  // Filter only active bookings
                  final activeBookings = bookings.where((b) {
                    final status = b.transactionStatus.toLowerCase();
                    return status == 'paid' || status == 'success';
                  }).toList();

                  if (activeBookings.isEmpty) {
                    return _buildEmptyState(localizations);
                  }

                  return ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: activeBookings.length,
                    itemBuilder: (context, index) {
                      final booking = activeBookings[index];
                      return BookingSelectCard(
                        booking: booking,
                        onTap: () => _onBookingSelected(booking),
                      );
                    },
                  );
                },
                loading: () => Skeletonizer(
                  enabled: true,
                  child: ListView.builder(
                    padding: const EdgeInsets.all(16),
                    itemCount: 5,
                    itemBuilder: (context, index) {
                      return Container(
                        margin: const EdgeInsets.only(bottom: 12),
                        padding: const EdgeInsets.all(16),
                        decoration: BoxDecoration(
                          color: Colors.white,
                          borderRadius: BorderRadius.circular(12),
                          boxShadow: [
                            BoxShadow(
                              color: Colors.black.withValues(alpha: 0.05),
                              blurRadius: 4,
                              offset: const Offset(0, 2),
                            ),
                          ],
                        ),
                        child: Row(
                          children: [
                            // Circle avatar placeholder
                            Container(
                              width: 50,
                              height: 50,
                              decoration: BoxDecoration(
                                color: Colors.grey.shade300,
                                shape: BoxShape.circle,
                              ),
                            ),
                            const SizedBox(width: 12),
                            // Text content
                            Expanded(
                              child: Column(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  // Booking code
                                  Container(
                                    width: double.infinity,
                                    height: 16,
                                    decoration: BoxDecoration(
                                      color: Colors.grey.shade300,
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  const SizedBox(height: 8),
                                  // Property name
                                  Container(
                                    width: 180,
                                    height: 14,
                                    decoration: BoxDecoration(
                                      color: Colors.grey.shade300,
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                  const SizedBox(height: 6),
                                  // Date
                                  Container(
                                    width: 120,
                                    height: 12,
                                    decoration: BoxDecoration(
                                      color: Colors.grey.shade300,
                                      borderRadius: BorderRadius.circular(4),
                                    ),
                                  ),
                                ],
                              ),
                            ),
                            // Arrow icon
                            Icon(
                              Icons.chevron_right,
                              color: Colors.grey.shade300,
                            ),
                          ],
                        ),
                      );
                    },
                  ),
                ),
                error: (error, stack) {
                  AppLogger.e('Error loading bookings', error, stack, 'CS');
                  return _buildErrorState(localizations, error);
                },
              ),
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildEmptyState(AppLocalizations localizations) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.inbox_outlined,
              size: 100,
              color: Colors.grey.shade300,
            ),
            const SizedBox(height: 24),
            Text(
              localizations.noActiveBookings,
              style: TextStyle(
                fontSize: 20,
                color: Colors.grey.shade700,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              localizations.noActiveBookingsMessage,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey.shade600,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 32),
            ElevatedButton.icon(
              onPressed: () => context.go('/home'),
              icon: const Icon(Icons.search),
              label: Text(localizations.browseProperties),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildErrorState(AppLocalizations localizations, Object error) {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.inbox_outlined,
              size: 100,
              color: Colors.grey.shade300,
            ),
            const SizedBox(height: 24),
            Text(
              localizations.noActiveBookings,
              style: TextStyle(
                fontSize: 20,
                color: Colors.grey.shade700,
                fontWeight: FontWeight.w600,
              ),
            ),
            const SizedBox(height: 12),
            Text(
              localizations.noActiveBookingsMessage,
              textAlign: TextAlign.center,
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey.shade600,
                height: 1.5,
              ),
            ),
            const SizedBox(height: 32),
            ElevatedButton.icon(
              onPressed: () => context.go('/home'),
              icon: const Icon(Icons.search),
              label: Text(localizations.browseProperties),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 32, vertical: 16),
                shape: RoundedRectangleBorder(
                  borderRadius: BorderRadius.circular(12),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}
