import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:intl/intl.dart';
import '../../../../../core/constants/appcolor_constants.dart';
import '../../../../../core/constants/appfontweight_constants.dart';
import '../../../mybooking/model/mybooking_model.dart';
import '../../../../book/roomdetails/provider/rooms_provider.dart';
import '../../../../book/roomdetails/provider/checkavaibilty_provider.dart';
import '../../../../../l10n/app_localizations.dart';

class RenewBookingDialog extends ConsumerStatefulWidget {
  final MyBookingModel bookingData;

  const RenewBookingDialog({
    super.key,
    required this.bookingData,
  });

  @override
  ConsumerState<RenewBookingDialog> createState() => _RenewBookingDialogState();
}

class _RenewBookingDialogState extends ConsumerState<RenewBookingDialog> {
  DateTime? _checkInDate;
  String _rentType = 'daily';
  int _duration = 1;
  bool _periodeLoaded = false;
  bool _showPeriodeSelector = false;

  @override
  void initState() {
    super.initState();
    if (widget.bookingData.checkOut != null) {
      try {
        final parsed = DateTime.parse(widget.bookingData.checkOut!);
        // Set check-in to H+1 from old checkout (continuous booking)
        _checkInDate = DateTime(parsed.year, parsed.month, parsed.day).add(const Duration(days: 1));
      } catch (e) {
        final now = DateTime.now();
        _checkInDate = DateTime(now.year, now.month, now.day);
      }
    }
  }

  void _applyPeriode(int periodeDaily, int periodeMonthly) {
    if (_periodeLoaded) return;
    _periodeLoaded = true;

    if (periodeDaily == 1 && periodeMonthly == 1) {
      _showPeriodeSelector = true;
      _rentType = 'daily';
    } else if (periodeMonthly == 1) {
      _showPeriodeSelector = false;
      _rentType = 'monthly';
    } else {
      _showPeriodeSelector = false;
      _rentType = 'daily';
    }

    // Trigger check setelah periode loaded
    _triggerAvailabilityCheck();
  }

  DateTime _calcCheckOut() {
    if (_checkInDate == null) return DateTime.now();
    if (_rentType == 'monthly') {
      return _checkInDate!.copyWith(month: _checkInDate!.month + _duration);
    }
    return _checkInDate!.add(Duration(days: _duration));
  }

  void _triggerAvailabilityCheck() {
    if (_checkInDate == null) return;
    final checkOut = _calcCheckOut();
    ref.read(availabilityCheckProvider.notifier).checkRoomAvailability(
      propertyId: widget.bookingData.propertyId,
      roomId: widget.bookingData.roomId,
      checkInDate: DateFormat('yyyy-MM-dd').format(_checkInDate!),
      checkOutDate: DateFormat('yyyy-MM-dd').format(checkOut),
      isRenewal: true,
    );
  }

  String _formatDate(DateTime? date) {
    if (date == null) return '-';
    return DateFormat('dd MMM yyyy', 'id_ID').format(date);
  }

  void _handleContinue() {
    final checkOutDate = _calcCheckOut();
    Navigator.of(context).pop({
      'check_in': DateFormat('yyyy-MM-dd').format(_checkInDate!),
      'check_out': DateFormat('yyyy-MM-dd').format(checkOutDate),
      'rentType': _rentType,
    });
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final roomAsync = ref.watch(roomByIdProvider(widget.bookingData.roomId));
    final availabilityState = ref.watch(availabilityCheckProvider);
    final isAvailable = availabilityState.isRoomAvailable;

    // Terapkan periode dari room data saat tersedia
    // Wrap dengan Future.microtask untuk avoid modifying provider during build
    roomAsync.whenData((roomData) {
      Future.microtask(() {
        _applyPeriode(roomData.periode_daily ?? 0, roomData.periode_monthly ?? 0);
      });
    });

    final checkOutDate = _calcCheckOut();

    // Derive tombol state dari availability
    final bool isLoading = isAvailable.isLoading;
    final bool available = isAvailable.when(
      data: (val) => val ?? false,
      loading: () => false,
      error: (_, __) => false,
    );
    final bool canContinue = _checkInDate != null && _periodeLoaded && available && !isLoading;

    // Dark mode detection for dialog background and inner container colors
    final isDark = Theme.of(context).brightness == Brightness.dark;

    return Dialog(
      // Use dark-aware dialog background color
      backgroundColor: isDark ? AppColors.surfaceDark : Colors.white,
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
      elevation: 8,
      child: Container(
        constraints: const BoxConstraints(maxWidth: 500),
        child: SingleChildScrollView(
          child: Padding(
            padding: const EdgeInsets.all(24.0),
            child: Column(
              mainAxisSize: MainAxisSize.min,
              crossAxisAlignment: CrossAxisAlignment.stretch,
              children: [
                // Close button (top right)
                Row(
                  mainAxisAlignment: MainAxisAlignment.end,
                  children: [
                    IconButton(
                      onPressed: () => Navigator.pop(context),
                      icon: const Icon(Icons.close),
                      // Use dark-aware close button icon color
                      color: isDark ? Colors.grey[400] : Colors.grey[600],
                      padding: EdgeInsets.zero,
                      constraints: const BoxConstraints(),
                    ),
                  ],
                ),

                // Logo at top center
                Center(
                  child: Image.asset(
                    'assets/images/ulinmahonilogo.png',
                    height: 100,
                    fit: BoxFit.contain,
                  ),
                ),

                const SizedBox(height: 16),

                // Header Title
                Center(
                  child: Text(
                    localizations.renewBookingTitle,
                    style: TextStyle(
                      fontSize: 20,
                      fontWeight: AppFontWeight.bold,
                      // Dark-aware title color
                      color: isDark ? Colors.white : Colors.black87,
                    ),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),

                const SizedBox(height: 20),

                // Property & Room Info
                Container(
                  padding: const EdgeInsets.all(16),
                  decoration: BoxDecoration(
                    // Use dark-aware container background
                    color: isDark ? AppColors.surfaceDarkElevated : Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(
                      // Use dark-aware border color
                      color: isDark ? Colors.white24 : Colors.grey[300]!,
                      width: 1.5,
                    ),
                  ),
                  child: Row(
                    children: [
                      Container(
                        padding: const EdgeInsets.all(12),
                        decoration: BoxDecoration(
                          // Use primaryAdaptive for dark/light mode compatibility
                          color: AppColors.primaryAdaptive(context),
                          borderRadius: BorderRadius.circular(10),
                        ),
                        child: const Icon(Icons.home_outlined, color: Colors.white, size: 24),
                      ),
                      const SizedBox(width: 16),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(
                              widget.bookingData.propertyName,
                              // Dark-aware property name color
                              style: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black87),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                            const SizedBox(height: 4),
                            Text(
                              widget.bookingData.roomName ?? 'Room',
                              style: TextStyle(fontSize: 14, color: Colors.grey[600]),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 24),

                // --- Periode Selector ---
                roomAsync.when(
                  data: (roomData) {
                    if (_showPeriodeSelector) {
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            localizations.renewBookingPeriodLabel,
                            // Dark-aware period label color
                            style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            decoration: BoxDecoration(
                              // Use dark-aware dropdown container background
                              color: isDark ? AppColors.surfaceDarkElevated : Colors.white,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: isDark ? Colors.white24 : Colors.grey[300]!, width: 1.5),
                            ),
                            child: DropdownButtonHideUnderline(
                              child: DropdownButton<String>(
                                value: _rentType,
                                isExpanded: true,
                                padding: const EdgeInsets.symmetric(horizontal: 16),
                                // Use dark-aware dropdown arrow icon color
                                icon: Icon(Icons.arrow_drop_down, color: isDark ? Colors.grey[400] : Colors.grey[600]),
                                // Use dark-aware dropdown background
                                dropdownColor: isDark ? AppColors.surfaceDarkElevated : Colors.white,
                                onChanged: (value) {
                                  if (value != null) {
                                    setState(() {
                                      _rentType = value;
                                      _duration = 1;
                                    });
                                    _triggerAvailabilityCheck();
                                  }
                                },
                                items: [
                                  // Dark-aware dropdown item text color
                                  DropdownMenuItem(value: 'daily', child: Text(localizations.renewBookingPeriodDaily, style: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black87))),
                                  DropdownMenuItem(value: 'monthly', child: Text(localizations.renewBookingPeriodMonthly, style: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black87))),
                                ],
                              ),
                            ),
                          ),
                        ],
                      );
                    } else {
                      return Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Text(
                            localizations.renewBookingPeriodLabel,
                            // Dark-aware period label color (locked)
                            style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
                          ),
                          const SizedBox(height: 8),
                          Container(
                            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                            decoration: BoxDecoration(
                              // Use dark-aware locked field background
                              color: isDark ? AppColors.surfaceDarkElevated : Colors.grey[100],
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: isDark ? Colors.white24 : Colors.grey[300]!, width: 1.5),
                            ),
                            child: Row(
                              children: [
                                // Use dark-aware lock icon color
                                Icon(Icons.lock_outline, color: isDark ? Colors.grey[400] : Colors.grey[600], size: 20),
                                const SizedBox(width: 12),
                                Expanded(
                                  child: Text(
                                    _rentType == 'daily' ? localizations.renewBookingPeriodDaily : localizations.renewBookingPeriodMonthly,
                                    // Use dark-aware locked text color
                                    style: TextStyle(fontSize: 16, color: isDark ? Colors.grey[400] : Colors.grey[700]),
                                    maxLines: 1,
                                    overflow: TextOverflow.ellipsis,
                                  ),
                                ),
                              ],
                            ),
                          ),
                        ],
                      );
                    }
                  },
                  loading: () => const SizedBox(height: 48, child: Center(child: CircularProgressIndicator())),
                  error: (error, _) => Text('Gagal memuat data kamar', style: TextStyle(color: Colors.red[700])),
                ),

                const SizedBox(height: 20),

                // --- Durasi Input (+/-) ---
                Text(
                  localizations.renewBookingDurationLabel,
                  // Dark-aware duration label color
                  style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
                ),
                const SizedBox(height: 8),
                Container(
                  decoration: BoxDecoration(
                    // Use dark-aware duration stepper container background
                    color: isDark ? AppColors.surfaceDarkElevated : Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: isDark ? Colors.white24 : Colors.grey[300]!, width: 1.5),
                  ),
                  child: Row(
                    children: [
                      IconButton(
                        onPressed: _duration > 1 ? () {
                          setState(() => _duration--);
                          _triggerAvailabilityCheck();
                        } : null,
                        icon: const Icon(Icons.remove),
                        // Use primaryAdaptive for dark/light mode compatibility
                        color: _duration > 1 ? AppColors.primaryAdaptive(context) : Colors.grey[400],
                        padding: const EdgeInsets.all(12),
                      ),
                      Expanded(
                        child: Center(
                          child: Text(
                            '$_duration ${_rentType == "daily" ? localizations.renewBookingDurationDay : localizations.renewBookingDurationMonth}',
                            // Dark-aware duration counter text color
                            style: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black87),
                          ),
                        ),
                      ),
                      IconButton(
                        onPressed: () {
                          setState(() => _duration++);
                          _triggerAvailabilityCheck();
                        },
                        icon: const Icon(Icons.add),
                        // Use primaryAdaptive for dark/light mode compatibility
                        color: AppColors.primaryAdaptive(context),
                        padding: const EdgeInsets.all(12),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // --- Check-in (locked) ---
                Text(
                  localizations.renewBookingCheckInLabel,
                  // Dark-aware check-in label color
                  style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
                ),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  decoration: BoxDecoration(
                    // Use dark-aware locked field background
                    color: isDark ? AppColors.surfaceDarkElevated : Colors.grey[100],
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: isDark ? Colors.white24 : Colors.grey[300]!, width: 1.5),
                  ),
                  child: Row(
                    children: [
                      // Use dark-aware lock icon color
                      Icon(Icons.lock_outline, color: isDark ? Colors.grey[400] : Colors.grey[600], size: 20),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          _formatDate(_checkInDate),
                          // Use dark-aware locked text color
                          style: TextStyle(fontSize: 16, color: isDark ? Colors.grey[400] : Colors.grey[700]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // --- Check-out (auto-calculated) ---
                Text(
                  localizations.renewBookingCheckOutLabel,
                  // Dark-aware check-out label color
                  style: TextStyle(fontSize: 14, color: isDark ? Colors.white : Colors.black87),
                ),
                const SizedBox(height: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
                  decoration: BoxDecoration(
                    // Use dark-aware container background for check-out display
                    color: isDark ? AppColors.surfaceDarkElevated : Colors.white,
                    borderRadius: BorderRadius.circular(12),
                    border: Border.all(color: isDark ? Colors.white24 : Colors.grey[300]!, width: 1.5),
                  ),
                  child: Row(
                    children: [
                      // Use primaryAdaptive for dark/light mode compatibility
                      Icon(Icons.calendar_today_outlined, color: AppColors.primaryAdaptive(context), size: 20),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          _formatDate(checkOutDate),
                          // Dark-aware check-out date text color
                          style: TextStyle(fontSize: 16, color: isDark ? Colors.white : Colors.black87),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 20),

                // --- Availability Status ---
                isAvailable.when(
                  data: (val) {
                    if (val == null) return const SizedBox.shrink();
                    return Container(
                      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                      decoration: BoxDecoration(
                        color: val ? Colors.green.withValues(alpha: 0.1) : Colors.red.withValues(alpha: 0.1),
                        borderRadius: BorderRadius.circular(8),
                      ),
                      child: Row(
                        children: [
                          Icon(
                            val ? Icons.check_circle_outline : Icons.error_outline,
                            color: val ? Colors.green[700] : Colors.red[700],
                            size: 18,
                          ),
                          const SizedBox(width: 8),
                          Expanded(
                            child: Text(
                              val ? localizations.renewBookingRoomAvailable : localizations.renewBookingRoomNotAvailable,
                              style: TextStyle(
                                fontSize: 13,
                                color: val ? Colors.green[700] : Colors.red[700],
                              ),
                              maxLines: 2,
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    );
                  },
                  loading: () => Row(
                    children: [
                      const SizedBox(width: 16, height: 16, child: CircularProgressIndicator(strokeWidth: 2)),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Text(
                          localizations.renewBookingCheckingAvailability,
                          style: TextStyle(fontSize: 13, color: Colors.grey[600]),
                          maxLines: 1,
                          overflow: TextOverflow.ellipsis,
                        ),
                      ),
                    ],
                  ),
                  error: (error, __) => Container(
                    padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
                    decoration: BoxDecoration(
                      color: Colors.red.withValues(alpha: 0.1),
                      borderRadius: BorderRadius.circular(8),
                    ),
                    child: Row(
                      children: [
                        Icon(Icons.error_outline, color: Colors.red[700], size: 18),
                        const SizedBox(width: 8),
                        Expanded(
                          child: Text(
                            error.toString(),
                            style: TextStyle(fontSize: 13, color: Colors.red[700], fontWeight: FontWeight.w600),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),

                const SizedBox(height: 20),

                // Info Message
                Container(
                  padding: const EdgeInsets.all(12),
                  decoration: BoxDecoration(
                    color: Colors.blue.withValues(alpha: 0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Row(
                    children: [
                      Icon(Icons.info_outline, color: Colors.blue[700], size: 20),
                      const SizedBox(width: 12),
                      Expanded(
                        child: Text(
                          localizations.renewBookingInfoMessage,
                          // Dark-aware info text color
                          style: TextStyle(fontSize: 12, color: isDark ? Colors.blue[200] : Colors.blue[900]),
                        ),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 24),

                // Continue Button
                ElevatedButton(
                  onPressed: canContinue ? _handleContinue : null,
                  style: ElevatedButton.styleFrom(
                    // Use primaryAdaptive for dark/light mode compatibility
                    backgroundColor: AppColors.primaryAdaptive(context),
                    // Use dark-aware disabled button background color
                    disabledBackgroundColor: isDark ? Colors.grey[700] : Colors.grey[300],
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(vertical: 16),
                    shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(12)),
                    elevation: 2,
                  ),
                  child: Text(
                    localizations.renewBookingContinueButton,
                    style: TextStyle(fontSize: 16, fontWeight: AppFontWeight.semiBold),
                    maxLines: 1,
                    overflow: TextOverflow.ellipsis,
                  ),
                ),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
