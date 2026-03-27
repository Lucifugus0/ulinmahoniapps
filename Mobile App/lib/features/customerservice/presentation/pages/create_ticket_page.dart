import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/widgets/appbar.dart';
import '../../../../core/utils/app_logger.dart';
import '../../../../features/auth/login/provider/auth_provider.dart';
import '../../model/ticket_category_model.dart';
import '../../model/eligible_booking_model.dart';
import '../../provider/ticket_provider.dart';
import '../../controller/ticket_controller.dart';

/// CreateTicketPage — Multi-step ticket creation wizard.
/// Step 1: Select ticket type (Booking / Complaint / Suggestion Box)
/// Step 2: If Booking/Complaint, select booking from eligible list. If Suggestion, skip.
/// Step 3: Select category within the chosen type.
/// Step 4: Enter subject + initial message and submit.
class CreateTicketPage extends ConsumerStatefulWidget {
  const CreateTicketPage({super.key});

  @override
  ConsumerState<CreateTicketPage> createState() => _CreateTicketPageState();
}

class _CreateTicketPageState extends ConsumerState<CreateTicketPage> {
  /// Current step index (0-based: 0=type, 1=booking, 2=category, 3=details)
  int _currentStep = 0;

  /// Selected ticket type from step 1
  String? _selectedType;

  /// Selected booking from step 2 (null for suggestion type)
  EligibleBookingModel? _selectedBooking;

  /// Selected category from step 3
  TicketCategoryModel? _selectedCategory;

  /// Text controllers for step 4 form fields
  final TextEditingController _subjectController = TextEditingController();
  final TextEditingController _messageController = TextEditingController();

  /// Form key for validating subject and message fields
  final GlobalKey<FormState> _formKey = GlobalKey<FormState>();

  @override
  void initState() {
    super.initState();
    /* Invalidate eligible bookings cache on every page open so fresh data is
       fetched — prevents showing stale empty results from a previous session. */
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.invalidate(eligibleBookingsProvider);
    });
  }

  @override
  void dispose() {
    _subjectController.dispose();
    _messageController.dispose();
    super.dispose();
  }

  /// Get total number of steps based on selected type.
  /// Suggestion type skips the booking selection step.
  int get _totalSteps {
    if (_selectedType == 'suggestion') return 3;
    return 4;
  }

  /// Navigate to the next step in the wizard
  void _nextStep() {
    setState(() {
      _currentStep++;
    });
  }

  /// Navigate to the previous step in the wizard
  void _previousStep() {
    if (_currentStep > 0) {
      setState(() {
        _currentStep--;
      });
    } else {
      /// Go back to ticket list when at the first step
      context.pop();
    }
  }

  /// Handle ticket type selection in step 1.
  /// Sets the type and advances to step 2.
  void _onTypeSelected(String type) {
    setState(() {
      _selectedType = type;
      _selectedBooking = null;
      _selectedCategory = null;
    });
    _nextStep();
  }

  /// Handle booking selection in step 2.
  /// Sets the booking and advances to step 3 (category selection).
  void _onBookingSelected(EligibleBookingModel booking) {
    setState(() {
      _selectedBooking = booking;
    });
    _nextStep();
  }

  /// Handle category selection in step 3.
  /// Sets the category and advances to step 4 (details form).
  void _onCategorySelected(TicketCategoryModel category) {
    setState(() {
      _selectedCategory = category;
    });
    _nextStep();
  }

  /// Submit the ticket creation form.
  /// Creates a new ticket via the controller and navigates to the chat page on success.
  Future<void> _submitTicket() async {
    if (!_formKey.currentState!.validate()) return;

    final user = ref.read(authProvider).user.value;
    if (user == null || _selectedCategory == null) return;

    final controller = ref.read(ticketControllerProvider.notifier);

    final ticketId = await controller.createTicket(
      userId: user.id,
      categoryId: _selectedCategory!.id,
      orderId: _selectedBooking?.orderId,
      subject: _subjectController.text.trim(),
      initialMessage: _messageController.text.trim().isNotEmpty
          ? _messageController.text.trim()
          : null,
    );

    if (!mounted) return;

    if (ticketId != null) {
      AppLogger.s('Ticket created with ID: $ticketId', 'CREATE-TICKET');

      /// Navigate to the newly created ticket's chat page
      context.go('/cs/ticket/$ticketId');
    } else {
      /// Show error snackbar on failure
      final ticketState = ref.read(ticketControllerProvider);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(
          content: Text(ticketState.error ?? 'Failed to create ticket'),
          backgroundColor: Colors.red.shade400,
          behavior: SnackBarBehavior.floating,
          margin: const EdgeInsets.all(16),
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(8),
          ),
        ),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final ticketState = ref.watch(ticketControllerProvider);

    /* Intercept back navigation: on step 0 allow normal pop (back to ticket list),
       on steps 1+ go to previous step instead of leaving the wizard. */
    return PopScope(
      canPop: _currentStep == 0,
      onPopInvokedWithResult: (didPop, _) {
        if (!didPop) _previousStep();
      },
      child: Scaffold(
      backgroundColor:
          isDark ? AppColors.backgroundDark : AppColors.backgroundLight,
      appBar: PreferredSize(
        preferredSize: const Size.fromHeight(kToolbarHeight),
        child: CustomAppBar(
          title: 'New Ticket',
          showBackButton: true,
        ),
      ),
      body: Column(
        children: [
          /// Step indicator showing progress through the wizard
          _buildStepIndicator(isDark),

          /// Main content area for the current step
          Expanded(
            child: _buildCurrentStep(isDark, ticketState),
          ),
        ],
      ),
    ), // Scaffold
    ); // PopScope
  }

  /// Build a horizontal step indicator at the top of the page.
  /// Shows numbered circles connected by lines, with active step highlighted.
  Widget _buildStepIndicator(bool isDark) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 16),
      color: isDark ? AppColors.surfaceDark : Colors.white,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: List.generate(_totalSteps, (index) {
          final isActive = index <= _currentStep;
          final isCurrent = index == _currentStep;

          return Row(
            children: [
              /// Step circle with number
              Container(
                width: 32,
                height: 32,
                decoration: BoxDecoration(
                  shape: BoxShape.circle,
                  color: isActive ? AppColors.primaryColor : Colors.grey[300],
                  border: isCurrent
                      ? Border.all(color: AppColors.primaryColor, width: 2)
                      : null,
                ),
                child: Center(
                  child: Text(
                    '${index + 1}',
                    style: TextStyle(
                      color: isActive ? Colors.white : Colors.grey[600],
                      fontWeight: FontWeight.bold,
                      fontSize: 14,
                    ),
                  ),
                ),
              ),

              /// Connector line between steps (not after last step)
              if (index < _totalSteps - 1)
                Container(
                  width: 40,
                  height: 2,
                  color: index < _currentStep
                      ? AppColors.primaryColor
                      : Colors.grey[300],
                ),
            ],
          );
        }),
      ),
    );
  }

  /// Build the content widget for the current wizard step
  Widget _buildCurrentStep(bool isDark, TicketControllerState ticketState) {
    /// For suggestion type, step mapping differs (no booking step)
    if (_selectedType == 'suggestion') {
      switch (_currentStep) {
        case 0:
          return _buildTypeSelectionStep(isDark);
        case 1:
          return _buildCategorySelectionStep(isDark);
        case 2:
          return _buildDetailsStep(isDark, ticketState);
        default:
          return _buildTypeSelectionStep(isDark);
      }
    }

    /// For booking/complaint type, all 4 steps apply
    switch (_currentStep) {
      case 0:
        return _buildTypeSelectionStep(isDark);
      case 1:
        return _buildBookingSelectionStep(isDark);
      case 2:
        return _buildCategorySelectionStep(isDark);
      case 3:
        return _buildDetailsStep(isDark, ticketState);
      default:
        return _buildTypeSelectionStep(isDark);
    }
  }

  /// Step 1: Select ticket type — shows 3 large cards for Booking, Complaint, Suggestion Box
  Widget _buildTypeSelectionStep(bool isDark) {
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    /* Use MediaQuery bottom padding so content clears the safe area (notch/home bar)
       on all devices — prevents the Suggestion Box card from being clipped. */
    final bottomPadding = MediaQuery.of(context).padding.bottom;

    return SingleChildScrollView(
      physics: const ClampingScrollPhysics(),
      padding: EdgeInsets.only(left: 24, right: 24, top: 24, bottom: 24 + bottomPadding),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Text(
            'What can we help you with?',
            style: TextStyle(
              fontSize: 20,
              fontWeight: FontWeight.bold,
              color: textColor,
            ),
          ),
          const SizedBox(height: 8),
          Text(
            'Select the type of issue you need help with.',
            style: TextStyle(
              fontSize: 14,
              color: isDark
                  ? AppColors.fontColorDarkMuted
                  : AppColors.fontColorLightMuted,
            ),
          ),
          const SizedBox(height: 24),

          /// Booking type card
          _buildTypeCard(
            type: 'booking',
            icon: Icons.hotel_outlined,
            title: 'Booking',
            description: 'Questions about your reservation, check-in, or payment.',
            color: AppColors.purple,
            isDark: isDark,
          ),
          const SizedBox(height: 16),

          /// Complaint type card
          _buildTypeCard(
            type: 'complaint',
            icon: Icons.report_problem_outlined,
            title: 'Complaint',
            description: 'Report an issue with your stay, facilities, or service.',
            color: AppColors.red,
            isDark: isDark,
          ),
          const SizedBox(height: 16),

          /// Suggestion Box type card
          _buildTypeCard(
            type: 'suggestion',
            icon: Icons.lightbulb_outline,
            title: 'Suggestion Box',
            description:
                'Share feedback, ideas, or general questions with us.',
            color: AppColors.teal,
            isDark: isDark,
          ),
        ],
      ),
    );
  }

  /// Build a large selectable card for ticket type selection in step 1
  Widget _buildTypeCard({
    required String type,
    required IconData icon,
    required String title,
    required String description,
    required Color color,
    required bool isDark,
  }) {
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;

    return GestureDetector(
      onTap: () => _onTypeSelected(type),
      child: Container(
        padding: const EdgeInsets.all(20),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(16),
          border: Border.all(
            color: color.withValues(alpha: 0.3),
            width: 1.5,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.05),
              blurRadius: 8,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Row(
          children: [
            /// Icon container with colored background
            Container(
              width: 56,
              height: 56,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(14),
              ),
              child: Icon(icon, size: 28, color: color),
            ),
            const SizedBox(width: 16),

            /// Title and description text
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    title,
                    style: TextStyle(
                      fontSize: 17,
                      fontWeight: FontWeight.w700,
                      color: textColor,
                    ),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    description,
                    style: TextStyle(
                      fontSize: 13,
                      color: isDark
                          ? AppColors.fontColorDarkMuted
                          : AppColors.fontColorLightMuted,
                      height: 1.3,
                    ),
                  ),
                ],
              ),
            ),
            Icon(Icons.chevron_right, color: color),
          ],
        ),
      ),
    );
  }

  /// Step 2: Select booking from eligible bookings list.
  /// Each card shows property name, room name, check-in/out dates, and order ID.
  Widget _buildBookingSelectionStep(bool isDark) {
    final user = ref.watch(authProvider).user.value;
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;

    if (user == null) {
      return const Center(child: CircularProgressIndicator());
    }

    final bookingsAsync = ref.watch(eligibleBookingsProvider(user.id));

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        /// Header with title and back button row
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Select Booking',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: textColor,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                'Choose the booking related to your issue.',
                style: TextStyle(
                  fontSize: 14,
                  color: isDark
                      ? AppColors.fontColorDarkMuted
                      : AppColors.fontColorLightMuted,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        /// Booking list or loading/empty states
        Expanded(
          child: bookingsAsync.when(
            data: (bookings) {
              if (bookings.isEmpty) {
                return Center(
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Icon(Icons.inbox_outlined,
                          size: 80, color: Colors.grey[300]),
                      const SizedBox(height: 16),
                      Text(
                        'No eligible bookings',
                        style: TextStyle(
                          fontSize: 16,
                          color: isDark ? Colors.grey[400] : Colors.grey[600],
                        ),
                      ),
                    ],
                  ),
                );
              }

              return ListView.builder(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                itemCount: bookings.length,
                itemBuilder: (context, index) {
                  return _buildBookingCard(bookings[index], isDark);
                },
              );
            },
            loading: () =>
                const Center(child: CircularProgressIndicator()),
            error: (error, stack) {
              AppLogger.e('Error loading eligible bookings', error, stack,
                  'CREATE-TICKET');
              return Center(
                child: Text(
                  'Failed to load bookings',
                  style: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[600]),
                ),
              );
            },
          ),
        ),

        /// Back button at the bottom
        _buildBottomBackButton(isDark),
      ],
    );
  }

  /// Build a selectable booking card for step 2.
  /// Displays property name, room name, check-in/out dates, and order ID.
  Widget _buildBookingCard(EligibleBookingModel booking, bool isDark) {
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final mutedColor =
        isDark ? AppColors.fontColorDarkMuted : AppColors.fontColorLightMuted;

    return GestureDetector(
      onTap: () => _onBookingSelected(booking),
      child: Container(
        margin: const EdgeInsets.only(bottom: 12),
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: cardBg,
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
            /// Booking icon
            Container(
              width: 48,
              height: 48,
              decoration: BoxDecoration(
                color: AppColors.primaryColor.withValues(alpha: 0.1),
                borderRadius: BorderRadius.circular(12),
              ),
              child: const Icon(Icons.hotel, color: AppColors.primaryColor),
            ),
            const SizedBox(width: 12),

            /// Booking details text
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text(
                    booking.propertyName ?? 'Unknown Property',
                    style: TextStyle(
                      fontSize: 15,
                      fontWeight: FontWeight.w600,
                      color: textColor,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                  const SizedBox(height: 2),
                  Text(
                    booking.roomName ?? '',
                    style: TextStyle(fontSize: 13, color: mutedColor),
                  ),
                  const SizedBox(height: 4),
                  Text(
                    '${booking.checkIn ?? '—'} to ${booking.checkOut ?? '—'}',
                    style: TextStyle(fontSize: 12, color: mutedColor),
                  ),
                  const SizedBox(height: 2),
                  Text(
                    booking.orderId,
                    style: TextStyle(
                      fontSize: 12,
                      color: AppColors.primaryColor,
                      fontWeight: FontWeight.w500,
                    ),
                  ),
                ],
              ),
            ),
            Icon(Icons.chevron_right, color: mutedColor),
          ],
        ),
      ),
    );
  }

  /// Step 3: Select category within the chosen ticket type.
  /// Displays a grid of category cards filtered by the selected type.
  Widget _buildCategorySelectionStep(bool isDark) {
    final categoriesAsync = ref.watch(ticketCategoriesProvider);
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final locale = Localizations.localeOf(context).languageCode;

    return Column(
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        /// Header text
        Padding(
          padding: const EdgeInsets.fromLTRB(24, 16, 24, 0),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(
                'Select Category',
                style: TextStyle(
                  fontSize: 20,
                  fontWeight: FontWeight.bold,
                  color: textColor,
                ),
              ),
              const SizedBox(height: 4),
              Text(
                'Choose the category that best describes your issue.',
                style: TextStyle(
                  fontSize: 14,
                  color: isDark
                      ? AppColors.fontColorDarkMuted
                      : AppColors.fontColorLightMuted,
                ),
              ),
            ],
          ),
        ),
        const SizedBox(height: 16),

        /// Category grid or loading/empty states
        Expanded(
          child: categoriesAsync.when(
            data: (categoriesMap) {
              /// Filter categories by the selected ticket type
              final categories = categoriesMap[_selectedType] ?? [];

              if (categories.isEmpty) {
                return Center(
                  child: Text(
                    'No categories available.',
                    style: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[600]),
                  ),
                );
              }

              return GridView.builder(
                padding: const EdgeInsets.symmetric(horizontal: 24),
                gridDelegate: const SliverGridDelegateWithFixedCrossAxisCount(
                  crossAxisCount: 2,
                  childAspectRatio: 1.3,
                  crossAxisSpacing: 12,
                  mainAxisSpacing: 12,
                ),
                itemCount: categories.length,
                itemBuilder: (context, index) {
                  return _buildCategoryCard(categories[index], isDark, locale);
                },
              );
            },
            loading: () =>
                const Center(child: CircularProgressIndicator()),
            error: (error, stack) {
              AppLogger.e('Error loading categories', error, stack,
                  'CREATE-TICKET');
              return Center(
                child: Text(
                  'Failed to load categories',
                  style: TextStyle(color: isDark ? Colors.grey[400] : Colors.grey[600]),
                ),
              );
            },
          ),
        ),

        /// Back button at the bottom
        _buildBottomBackButton(isDark),
      ],
    );
  }

  /// Build a selectable category card for step 3.
  /// Shows category icon and localized label.
  Widget _buildCategoryCard(
      TicketCategoryModel category, bool isDark, String locale) {
    final cardBg = isDark ? AppColors.surfaceDark : Colors.white;
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final color = _getCategoryIconColor(category.category);

    return GestureDetector(
      onTap: () => _onCategorySelected(category),
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: cardBg,
          borderRadius: BorderRadius.circular(14),
          border: Border.all(
            color: color.withValues(alpha: 0.3),
            width: 1,
          ),
          boxShadow: [
            BoxShadow(
              color: Colors.black.withValues(alpha: 0.04),
              blurRadius: 4,
              offset: const Offset(0, 2),
            ),
          ],
        ),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            /// Category icon
            Container(
              width: 44,
              height: 44,
              decoration: BoxDecoration(
                color: color.withValues(alpha: 0.15),
                borderRadius: BorderRadius.circular(12),
              ),
              child: Icon(
                _getCategoryIcon(category.category),
                color: color,
                size: 22,
              ),
            ),
            const SizedBox(height: 10),

            /// Category label (localized)
            Text(
              category.getLabel(locale),
              style: TextStyle(
                fontSize: 13,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
              textAlign: TextAlign.center,
              maxLines: 2,
              overflow: TextOverflow.ellipsis,
            ),
          ],
        ),
      ),
    );
  }

  /// Step 4: Enter subject and initial message, then submit.
  /// Includes a text field for subject, text area for message, and submit button.
  Widget _buildDetailsStep(bool isDark, TicketControllerState ticketState) {
    final textColor =
        isDark ? AppColors.fontColorDark : AppColors.fontColorLight;
    final inputBg = isDark ? AppColors.surfaceDarkElevated : Colors.grey[50];
    final inputBorder =
        isDark ? AppColors.glassBorderDark : Colors.grey[300]!;

    return SingleChildScrollView(
      padding: const EdgeInsets.all(24),
      child: Form(
        key: _formKey,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(
              'Describe Your Issue',
              style: TextStyle(
                fontSize: 20,
                fontWeight: FontWeight.bold,
                color: textColor,
              ),
            ),
            const SizedBox(height: 4),
            Text(
              'Provide a brief summary and details about your issue.',
              style: TextStyle(
                fontSize: 14,
                color: isDark
                    ? AppColors.fontColorDarkMuted
                    : AppColors.fontColorLightMuted,
              ),
            ),
            const SizedBox(height: 24),

            /// Subject text field
            Text(
              'Subject',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
            ),
            const SizedBox(height: 8),
            TextFormField(
              controller: _subjectController,
              style: TextStyle(color: textColor),
              decoration: InputDecoration(
                hintText: 'Brief summary of your issue',
                hintStyle: TextStyle(
                  color: isDark
                      ? AppColors.fontColorDarkMuted
                      : AppColors.fontColorLightMuted,
                ),
                filled: true,
                fillColor: inputBg,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: inputBorder),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: inputBorder),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide:
                      const BorderSide(color: AppColors.primaryColor, width: 2),
                ),
              ),
              validator: (value) {
                if (value == null || value.trim().isEmpty) {
                  return 'Subject is required';
                }
                return null;
              },
            ),
            const SizedBox(height: 20),

            /// Message text area
            Text(
              'Message',
              style: TextStyle(
                fontSize: 14,
                fontWeight: FontWeight.w600,
                color: textColor,
              ),
            ),
            const SizedBox(height: 8),
            TextFormField(
              controller: _messageController,
              style: TextStyle(color: textColor),
              maxLines: 5,
              decoration: InputDecoration(
                hintText: 'Describe your issue in detail (optional)',
                hintStyle: TextStyle(
                  color: isDark
                      ? AppColors.fontColorDarkMuted
                      : AppColors.fontColorLightMuted,
                ),
                filled: true,
                fillColor: inputBg,
                border: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: inputBorder),
                ),
                enabledBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide: BorderSide(color: inputBorder),
                ),
                focusedBorder: OutlineInputBorder(
                  borderRadius: BorderRadius.circular(12),
                  borderSide:
                      const BorderSide(color: AppColors.primaryColor, width: 2),
                ),
                alignLabelWithHint: true,
              ),
            ),
            const SizedBox(height: 32),

            /// Submit button with loading state
            SizedBox(
              width: double.infinity,
              height: 52,
              child: ElevatedButton(
                onPressed: ticketState.isLoading ? null : _submitTicket,
                style: ElevatedButton.styleFrom(
                  backgroundColor: AppColors.primaryColor,
                  foregroundColor: Colors.white,
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(14),
                  ),
                  elevation: 0,
                ),
                child: ticketState.isLoading
                    ? const SizedBox(
                        width: 24,
                        height: 24,
                        child: CircularProgressIndicator(
                          strokeWidth: 2,
                          color: Colors.white,
                        ),
                      )
                    : const Text(
                        'Submit Ticket',
                        style: TextStyle(
                          fontSize: 16,
                          fontWeight: FontWeight.w600,
                        ),
                      ),
              ),
            ),
            const SizedBox(height: 16),

            /// Back button to go to previous step
            Center(
              child: TextButton(
                onPressed: _previousStep,
                child: Text(
                  'Back',
                  style: TextStyle(
                    color: isDark
                        ? AppColors.fontColorDarkMuted
                        : AppColors.fontColorLightMuted,
                  ),
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  /// Build a back button pinned at the bottom of steps 2 and 3
  Widget _buildBottomBackButton(bool isDark) {
    return Padding(
      padding: const EdgeInsets.all(16),
      child: SizedBox(
        width: double.infinity,
        child: OutlinedButton(
          onPressed: _previousStep,
          style: OutlinedButton.styleFrom(
            side: BorderSide(
              color: isDark ? Colors.grey[600]! : Colors.grey[400]!,
            ),
            shape: RoundedRectangleBorder(
              borderRadius: BorderRadius.circular(12),
            ),
            padding: const EdgeInsets.symmetric(vertical: 14),
          ),
          child: Text(
            'Back',
            style: TextStyle(
              color: isDark
                  ? AppColors.fontColorDarkMuted
                  : AppColors.fontColorLightMuted,
            ),
          ),
        ),
      ),
    );
  }

  /// Get icon for a given category name
  IconData _getCategoryIcon(String category) {
    switch (category.toLowerCase()) {
      case 'check_in':
      case 'checkin':
        return Icons.login;
      case 'check_out':
      case 'checkout':
        return Icons.logout;
      case 'payment':
        return Icons.payment;
      case 'facility':
      case 'facilities':
        return Icons.apartment;
      case 'noise':
        return Icons.volume_up;
      case 'cleanliness':
        return Icons.cleaning_services;
      case 'security':
        return Icons.security;
      case 'maintenance':
        return Icons.build;
      case 'general':
        return Icons.help_outline;
      case 'feedback':
        return Icons.feedback_outlined;
      case 'feature_request':
        return Icons.lightbulb_outline;
      default:
        return Icons.category;
    }
  }

  /// Get color for a given category name
  Color _getCategoryIconColor(String category) {
    switch (category.toLowerCase()) {
      case 'check_in':
      case 'checkin':
        return AppColors.green;
      case 'check_out':
      case 'checkout':
        return AppColors.orange;
      case 'payment':
        return AppColors.blue;
      case 'facility':
      case 'facilities':
        return AppColors.purple;
      case 'noise':
        return AppColors.red;
      case 'cleanliness':
        return AppColors.teal;
      case 'security':
        return AppColors.indigo;
      case 'maintenance':
        return AppColors.amber;
      default:
        return AppColors.primaryColor;
    }
  }
}
