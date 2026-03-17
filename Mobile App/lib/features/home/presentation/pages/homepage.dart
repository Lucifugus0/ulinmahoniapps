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

class HomePage extends ConsumerStatefulWidget {
  const HomePage({super.key});

  @override
  ConsumerState<HomePage> createState() => _HomePageState();
}

class _HomePageState extends ConsumerState<HomePage> {
  String _selectedFilterLabel = "kos";

  Future<void> _onRefresh() async {
    ref.invalidate(propertiesProvider(_selectedFilterLabel));
    ref.invalidate(distinctCitiesProvider);
    ref.invalidate(distinctCityPropertiesProvider);
    ref.invalidate(bestSellerPropertiesProvider);
    ref.invalidate(cheapestPropertiesProvider);
    ref.invalidate(activeBannersProvider);
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

                const PromoBannerSection(),

                // const SizedBox(height: 24),

                // const PromotionSection(),

                // const SizedBox(height: 24),

                // const AreaPopularSection(),

                const SizedBox(height: 6),

                const BudgetSection(),
              ],
            ),
          ),
        ),
      ),
    );
  }
}