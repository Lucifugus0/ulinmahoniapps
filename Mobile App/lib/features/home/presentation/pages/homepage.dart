import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/features/home/presentation/widgets/section/populararea.dart';
import 'package:ulinmahoniapps/features/home/presentation/widgets/section/promotion.dart';
import '../widgets/section/videosearchbanner.dart';
import '../widgets/section/categories.dart';
import '../widgets/section/bestseller.dart';
import '../widgets/section/promobanner.dart';
import '../widgets/section/budget.dart';
import '../../provider/property_provider.dart';
import '../../../promo_banner/provider/promo_banner_provider.dart';
import '../widgets/section/filteredproperties.dart';
import '../../../../core/utils/app_logger.dart';
import '../../provider/content_provider.dart';
import '../../../auth/login/provider/auth_provider.dart';
import '../../../auth/login/presentation/widgets/email_verification_popup.dart';
import '../../../auth/login/data/repositories/auth_repository.dart';

class HomePage extends ConsumerStatefulWidget {
  const HomePage({super.key});

  @override
  ConsumerState<HomePage> createState() => _HomePageState();
}

class _HomePageState extends ConsumerState<HomePage> {
  // Default to "All" (empty string = no filter, show all properties)
  String _selectedFilterLabel = "";
  bool _verificationPopupShown = false;

  /// Show email verification popup with resend and change email options
  void _showVerificationPopup(BuildContext context, String email) {
    final l10n = AppLocalizations.of(context)!;

    EmailVerificationPopup.show(
      context: context,
      email: email,
      onResendEmail: () async {
        Navigator.of(context).pop();
        try {
          final repo = ref.read(authRepositoryProvider);
          final result = await repo.resendVerification(email);
          if (context.mounted) {
            EmailVerificationSentPopup.show(context: context, email: email);
          }
        } catch (e) {
          AppLogger.e('Failed to resend verification', e, null, 'HOME');
        }
      },
      onCancel: () => Navigator.of(context).pop(),
    );
  }

  Future<void> _onRefresh() async {
    ref.invalidate(propertiesProvider(_selectedFilterLabel));
    ref.invalidate(distinctCitiesProvider);
    ref.invalidate(distinctCityPropertiesProvider);
    ref.invalidate(availableNowPropertiesProvider);
    ref.invalidate(cheapestPropertiesProvider);
    ref.invalidate(activeBannersProvider);
    // Refresh dynamic tagline and hero video on pull-to-refresh
    ref.invalidate(taglineProvider);
    ref.invalidate(heroVideoUrlProvider);
    AppLogger.d("🔄 HomePage: Memuat ulang data properti...", 'HOME');
  }

  @override
  Widget build(BuildContext context) {
    // Show email verification popup once if user is logged in but unverified
    final authState = ref.watch(authProvider);
    if (!_verificationPopupShown && authState.isLoggedIn) {
      final user = authState.user.value;
      if (user != null && !user.isEmailVerified) {
        _verificationPopupShown = true;
        WidgetsBinding.instance.addPostFrameCallback((_) {
          _showVerificationPopup(context, user.email);
        });
      }
    }

    // SafeArea top is disabled so content scrolls behind the glass navbar
    return SafeArea(
      top: false,
      bottom: false,
      child: RefreshIndicator(
        onRefresh: _onRefresh,
        child: SingleChildScrollView(
          physics: const BouncingScrollPhysics(parent: AlwaysScrollableScrollPhysics()),
          child: Padding(
            padding: const EdgeInsets.only(bottom: 120.0),
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.center,
              children: [
                const VideoSearchBanner(),

                const SizedBox(height: 4),

                CategoriesSection(
                  selectedLabel: _selectedFilterLabel,
                  onCategorySelected: (label) {
                    setState(() {
                      _selectedFilterLabel = label.toLowerCase();
                    });
                  },
                ),

                const SizedBox(height: 6),

                FilteredPropertyListView(
                  selectedFilterLabel: _selectedFilterLabel,
                ),

                const SizedBox(height: 6),

                const BestSellerSection(),

                const SizedBox(height: 6),

                // Budget section before Promo (swapped per requirement)
                const BudgetSection(),

                const SizedBox(height: 6),

                const PromoBannerSection(),
              ],
            ),
          ),
        ),
      ),
    );
  }
}