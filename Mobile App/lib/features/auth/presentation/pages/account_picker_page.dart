import 'package:flutter/material.dart';
import '../../../../core/services/apple_multi_account_storage.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/utils/app_logger.dart';

class AccountPickerPage extends StatefulWidget {
  const AccountPickerPage({super.key});

  @override
  State<AccountPickerPage> createState() => _AccountPickerPageState();
}

class _AccountPickerPageState extends State<AccountPickerPage> {
  final _storage = AppleMultiAccountStorage();
  List<AppleAccountData> _accounts = [];
  AppleAccountData? _activeAccount;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _loadAccounts();
  }

  Future<void> _loadAccounts() async {
    setState(() => _isLoading = true);

    try {
      final accounts = await _storage.getAllAccounts();
      final active = await _storage.getActiveAccount();

      setState(() {
        _accounts = accounts;
        _activeAccount = active;
        _isLoading = false;
      });

      AppLogger.d('Loaded ${accounts.length} accounts', 'ACCOUNT-PICKER');
    } catch (e, stackTrace) {
      AppLogger.e('Failed to load accounts', e, stackTrace, 'ACCOUNT-PICKER');
      setState(() => _isLoading = false);
    }
  }

  Future<void> _switchAccount(AppleAccountData account) async {
    try {
      AppLogger.i('Switching to account: ${account.displayEmail}', 'ACCOUNT-PICKER');

      // Update last active timestamp
      final updated = account.copyWith(
        lastActiveTimestamp: DateTime.now().millisecondsSinceEpoch,
      );
      await _storage.saveAccount(updated);

      // Navigate back with selected account
      if (mounted) {
        Navigator.pop(context, account);
      }
    } catch (e, stackTrace) {
      AppLogger.e('Failed to switch account', e, stackTrace, 'ACCOUNT-PICKER');

      if (mounted) {
        ScaffoldMessenger.of(context).showSnackBar(
          const SnackBar(content: Text('Failed to switch account')),
        );
      }
    }
  }

  Future<void> _removeAccount(AppleAccountData account) async {
    final confirm = await showDialog<bool>(
      context: context,
      builder: (context) => AlertDialog(
        title: const Text('Remove Account'),
        content: Text('Remove ${account.displayEmail} from this device?'),
        actions: [
          TextButton(
            onPressed: () => Navigator.pop(context, false),
            child: const Text('Cancel'),
          ),
          TextButton(
            onPressed: () => Navigator.pop(context, true),
            style: TextButton.styleFrom(foregroundColor: Colors.red),
            child: const Text('Remove'),
          ),
        ],
      ),
    );

    if (confirm == true) {
      try {
        await _storage.removeAccount(account.userIdentifier);
        await _loadAccounts();

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Account removed')),
          );
        }
      } catch (e, stackTrace) {
        AppLogger.e('Failed to remove account', e, stackTrace, 'ACCOUNT-PICKER');

        if (mounted) {
          ScaffoldMessenger.of(context).showSnackBar(
            const SnackBar(content: Text('Failed to remove account')),
          );
        }
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Switch Account'),
        backgroundColor: AppColors.primaryColor,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : _accounts.isEmpty
              ? _buildEmptyState()
              : _buildAccountList(),
    );
  }

  Widget _buildEmptyState() {
    return Center(
      child: Padding(
        padding: const EdgeInsets.all(24.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(
              Icons.account_circle_outlined,
              size: 80,
              color: Colors.grey.shade400,
            ),
            const SizedBox(height: 16),
            Text(
              'No accounts found',
              style: TextStyle(
                fontSize: 18,
                fontWeight: FontWeight.w600,
                color: Colors.grey.shade700,
              ),
            ),
            const SizedBox(height: 8),
            Text(
              'Sign in with Apple to add an account',
              style: TextStyle(
                fontSize: 14,
                color: Colors.grey.shade600,
              ),
              textAlign: TextAlign.center,
            ),
            const SizedBox(height: 24),
            ElevatedButton.icon(
              onPressed: () => Navigator.pop(context, 'add_new'),
              icon: const Icon(Icons.add),
              label: const Text('Add Account'),
              style: ElevatedButton.styleFrom(
                backgroundColor: AppColors.primaryColor,
                foregroundColor: Colors.white,
                padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildAccountList() {
    return ListView.builder(
      itemCount: _accounts.length + 1,
      padding: const EdgeInsets.symmetric(vertical: 8),
      itemBuilder: (context, index) {
        if (index == _accounts.length) {
          // Add account button at the end
          return Padding(
            padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 8),
            child: ListTile(
              leading: CircleAvatar(
                backgroundColor: AppColors.primaryColor.withValues(alpha: 0.1),
                child: Icon(
                  Icons.add,
                  color: AppColors.primaryColor,
                ),
              ),
              title: const Text(
                'Add Another Account',
                style: TextStyle(
                  fontWeight: FontWeight.w500,
                  color: AppColors.primaryColor,
                ),
              ),
              onTap: () => Navigator.pop(context, 'add_new'),
            ),
          );
        }

        final account = _accounts[index];
        final isActive = _activeAccount?.userIdentifier == account.userIdentifier;

        return Card(
          margin: const EdgeInsets.symmetric(horizontal: 12, vertical: 4),
          elevation: isActive ? 2 : 0,
          color: isActive ? AppColors.primaryColor.withValues(alpha: 0.05) : null,
          child: ListTile(
            contentPadding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            leading: CircleAvatar(
              backgroundColor: AppColors.primaryColor,
              child: Text(
                account.fullName[0].toUpperCase(),
                style: const TextStyle(
                  color: Colors.white,
                  fontWeight: FontWeight.bold,
                ),
              ),
            ),
            title: Text(
              account.fullName,
              style: TextStyle(
                fontWeight: isActive ? FontWeight.w600 : FontWeight.w500,
              ),
            ),
            subtitle: Text(
              account.displayEmail,
              style: TextStyle(
                fontSize: 13,
                color: Colors.grey.shade600,
              ),
            ),
            trailing: isActive
                ? Row(
                    mainAxisSize: MainAxisSize.min,
                    children: [
                      const Icon(
                        Icons.check_circle,
                        color: Colors.green,
                        size: 20,
                      ),
                      const SizedBox(width: 4),
                      Text(
                        'Active',
                        style: TextStyle(
                          fontSize: 12,
                          color: Colors.grey.shade600,
                          fontWeight: FontWeight.w500,
                        ),
                      ),
                    ],
                  )
                : IconButton(
                    icon: const Icon(Icons.delete_outline),
                    color: Colors.red.shade400,
                    onPressed: () => _removeAccount(account),
                    tooltip: 'Remove account',
                  ),
            onTap: isActive ? null : () => _switchAccount(account),
          ),
        );
      },
    );
  }
}
