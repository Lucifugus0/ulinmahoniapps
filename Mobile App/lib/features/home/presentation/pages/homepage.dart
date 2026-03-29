import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
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

class HomePage extends ConsumerStatefulWidget {
  const HomePage({super.key});

  @override
  ConsumerState<HomePage> createState() => _HomePageState();
}

class _HomePageState extends ConsumerState<HomePage> {
  // Default to "All" (empty string = no filter, show all properties)
  String _selectedFilterLabel = "";

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
    return SafeArea(
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