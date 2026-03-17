import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/widgets/dialog/notificationdialog.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';

class VoucherDialog extends StatefulWidget {
  final Function(String voucherCode) onVoucherApplied;

  const VoucherDialog({
    Key? key,
    required this.onVoucherApplied,
  }) : super(key: key);

  @override
  State<VoucherDialog> createState() => _VoucherDialogState();
}

class _VoucherDialogState extends State<VoucherDialog> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  final TextEditingController _redeemController = TextEditingController();

  // Mock voucher data - TODO: Replace with API call
  final List<Map<String, dynamic>> _availableVouchers = [
    {
      'code': 'WELCOME50',
      'title': 'Welcome Discount 50%',
      'description': 'Get 50% off on your first booking',
      'discount': '50%',
      'expiry': '31 Dec 2024',
      'minTransaction': 500000,
    },
    {
      'code': 'ROOM20',
      'title': 'Room Discount 20%',
      'description': 'Special discount for room booking',
      'discount': '20%',
      'expiry': '15 Dec 2024',
      'minTransaction': 300000,
    },
    {
      'code': 'FLAT100K',
      'title': 'Flat Rp 100.000',
      'description': 'Flat discount for all bookings',
      'discount': 'Rp 100.000',
      'expiry': '20 Dec 2024',
      'minTransaction': 200000,
    },
  ];

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    _redeemController.dispose();
    super.dispose();
  }

  void _applyVoucher(String voucherCode) {
    widget.onVoucherApplied(voucherCode);
    Navigator.of(context).pop();
  }

  void _redeemCode() {
    final localizations = AppLocalizations.of(context)!;
    final code = _redeemController.text.trim();

    if (code.isEmpty) {
      showNotificationDialog(
        context,
        localizations.paymentVoucherInvalid,
        defaultIcon: Icons.error_outline,
        iconColor: Colors.red,
      );
      return;
    }

    // TODO: Add API call to validate redeem code
    // For now, just apply the code
    _applyVoucher(code);
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;
    final textTheme = Theme.of(context).textTheme;

    return Dialog(
      shape: RoundedRectangleBorder(
        borderRadius: BorderRadius.circular(16),
      ),
      child: Container(
        width: MediaQuery.of(context).size.width * 0.9,
        constraints: BoxConstraints(
          maxHeight: MediaQuery.of(context).size.height * 0.8,
          minHeight: MediaQuery.of(context).size.height * 0.5,
        ),
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            // Header
            Row(
              mainAxisAlignment: MainAxisAlignment.spaceBetween,
              children: [
                Text(
                  localizations.paymentVoucherTitle,
                  style: textTheme.titleLarge?.copyWith(
                    fontWeight: FontWeight.bold,
                  ),
                ),
                IconButton(
                  icon: const Icon(Icons.close),
                  onPressed: () => Navigator.of(context).pop(),
                ),
              ],
            ),
            const SizedBox(height: 16),

            // Tab Bar
            Container(
              decoration: BoxDecoration(
                color: Colors.grey.shade200,
                borderRadius: BorderRadius.circular(8),
              ),
              child: TabBar(
                controller: _tabController,
                indicator: BoxDecoration(
                  color: AppColors.primaryColor,
                  borderRadius: BorderRadius.circular(8),
                ),
                labelColor: Colors.white,
                unselectedLabelColor: Colors.black54,
                labelStyle: const TextStyle(fontWeight: FontWeight.bold),
                tabs: [
                  Tab(text: localizations.paymentVoucherMyVouchers),
                  Tab(text: localizations.paymentVoucherRedeemCode),
                ],
              ),
            ),
            const SizedBox(height: 16),

            // Tab Bar View
            Expanded(
              child: TabBarView(
                controller: _tabController,
                children: [
                  // Tab 1: My Vouchers
                  _buildMyVouchersTab(localizations, textTheme),

                  // Tab 2: Redeem Code
                  _buildRedeemCodeTab(localizations, textTheme),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildMyVouchersTab(AppLocalizations localizations, TextTheme textTheme) {
    if (_availableVouchers.isEmpty) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.card_giftcard, size: 64, color: Colors.grey.shade400),
            const SizedBox(height: 16),
            Text(
              localizations.paymentVoucherNoVouchers,
              style: textTheme.bodyLarge?.copyWith(color: Colors.grey.shade600),
            ),
          ],
        ),
      );
    }

    return ListView.builder(
      itemCount: _availableVouchers.length,
      itemBuilder: (context, index) {
        final voucher = _availableVouchers[index];
        return _buildVoucherCard(voucher, localizations, textTheme);
      },
    );
  }

  Widget _buildVoucherCard(
    Map<String, dynamic> voucher,
    AppLocalizations localizations,
    TextTheme textTheme,
  ) {
    return Container(
      margin: const EdgeInsets.only(bottom: 12),
      decoration: BoxDecoration(
        border: Border.all(color: AppColors.primaryColor.withOpacity(0.3)),
        borderRadius: BorderRadius.circular(12),
        color: Colors.white,
      ),
      child: Padding(
        padding: const EdgeInsets.all(16),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  flex: 2,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Text(
                        voucher['title'],
                        style: textTheme.titleMedium?.copyWith(
                          fontWeight: FontWeight.bold,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                      const SizedBox(height: 4),
                      Text(
                        voucher['description'],
                        style: textTheme.bodySmall?.copyWith(
                          color: Colors.grey.shade600,
                        ),
                        maxLines: 2,
                        overflow: TextOverflow.ellipsis,
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                Container(
                  padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
                  decoration: BoxDecoration(
                    color: AppColors.primaryColor.withOpacity(0.1),
                    borderRadius: BorderRadius.circular(8),
                  ),
                  child: Text(
                    voucher['discount'],
                    style: textTheme.titleSmall?.copyWith(
                      color: AppColors.primaryColor,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
            const SizedBox(height: 12),
            Row(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Expanded(
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Row(
                        children: [
                          Icon(Icons.confirmation_number, size: 14, color: Colors.grey.shade600),
                          const SizedBox(width: 4),
                          Flexible(
                            child: Text(
                              voucher['code'],
                              style: textTheme.bodySmall?.copyWith(
                                fontWeight: FontWeight.bold,
                                color: AppColors.secondaryColor,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                      const SizedBox(height: 4),
                      Row(
                        children: [
                          Icon(Icons.calendar_today, size: 14, color: Colors.grey.shade600),
                          const SizedBox(width: 4),
                          Flexible(
                            child: Text(
                              '${localizations.paymentVoucherValidUntil} ${voucher['expiry']}',
                              style: textTheme.bodySmall?.copyWith(
                                color: Colors.grey.shade600,
                              ),
                              overflow: TextOverflow.ellipsis,
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
                const SizedBox(width: 8),
                ElevatedButton(
                  onPressed: () => _applyVoucher(voucher['code']),
                  style: ElevatedButton.styleFrom(
                    backgroundColor: AppColors.primaryColor,
                    foregroundColor: Colors.white,
                    padding: const EdgeInsets.symmetric(horizontal: 20, vertical: 10),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(8),
                    ),
                    minimumSize: const Size(0, 0),
                    tapTargetSize: MaterialTapTargetSize.shrinkWrap,
                  ),
                  child: Text(
                    localizations.paymentVoucherUseButton,
                    style: textTheme.labelMedium?.copyWith(
                      color: Colors.white,
                      fontWeight: FontWeight.bold,
                    ),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildRedeemCodeTab(AppLocalizations localizations, TextTheme textTheme) {
    return SingleChildScrollView(
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.stretch,
        children: [
          const SizedBox(height: 20),
          Icon(
            Icons.redeem,
            size: 80,
            color: AppColors.primaryColor,
          ),
          const SizedBox(height: 24),
          Text(
            localizations.paymentVoucherRedeemTitle,
            style: textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 8),
          Text(
            localizations.paymentVoucherRedeemDescription,
            style: textTheme.bodyMedium?.copyWith(
              color: Colors.grey.shade600,
            ),
            textAlign: TextAlign.center,
          ),
          const SizedBox(height: 32),
          TextField(
            controller: _redeemController,
            decoration: InputDecoration(
              labelText: localizations.paymentVoucherPlaceholder,
              hintText: localizations.paymentVoucherRedeemHint,
              border: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
              ),
              enabledBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide(color: Colors.grey.shade300),
              ),
              focusedBorder: OutlineInputBorder(
                borderRadius: BorderRadius.circular(12),
                borderSide: BorderSide(color: AppColors.primaryColor, width: 2),
              ),
              prefixIcon: Icon(Icons.local_offer, color: AppColors.primaryColor),
            ),
            textCapitalization: TextCapitalization.characters,
          ),
          const SizedBox(height: 24),
          ElevatedButton(
            onPressed: _redeemCode,
            style: ElevatedButton.styleFrom(
              backgroundColor: AppColors.primaryColor,
              foregroundColor: Colors.white,
              padding: const EdgeInsets.symmetric(vertical: 16),
              shape: RoundedRectangleBorder(
                borderRadius: BorderRadius.circular(12),
              ),
            ),
            child: Text(
              localizations.paymentVoucherApplyButton,
              style: textTheme.titleMedium?.copyWith(
                color: Colors.white,
                fontWeight: FontWeight.bold,
              ),
            ),
          ),
        ],
      ),
    );
  }
}
