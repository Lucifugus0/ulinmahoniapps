import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import '../widgets/propertytypelist.dart';
import '../widgets/propertyitem.dart';
import '../../provider/propertytype_provider.dart';
import '../../../../core/layout/mainlayout.dart';
import '../../../../core/widgets/appbar.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import '../../../../core/utils/app_logger.dart';

class PropertyTypePage extends ConsumerWidget {
  const PropertyTypePage({Key? key}) : super(key: key);

  Future<void> _onRefresh(WidgetRef ref) async {
    ref.invalidate(representativePropertyTypesProvider);
    AppLogger.d('representativePropertyTypesProvider di-invalidate', 'PROPERTYTYPE');
  }

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final localizations = AppLocalizations.of(context)!;
    final isDark = Theme.of(context).brightness == Brightness.dark;
    final representativePropertiesAsync = ref.watch(representativePropertyTypesProvider);
    return MainLayout(
      currentIndex: 0,
      // Use theme-aware background color for dark/light mode
      backgroundColor: isDark ? const Color(0xFF111827) : AppColors.backgroundColor,
      showBottomNav: true,
      showNavBar: false,
      showContactBar: false,
      bottomcontactbar_pesansekarang: false,
      child: SafeArea(
        child: Column(
          children: [
            CustomAppBar(
              title: localizations.propertyTypePageTitle,
              showBackButton: true,
              // Dark mode: use dark header, Light mode: keep primary green
              backgroundColor: isDark ? const Color(0xFF1F2937) : AppColors.primaryColor,
              textColor: Colors.white,
              backButtonColor: Colors.white,
            ),
            Expanded(
              child: RefreshIndicator(
                onRefresh: () => _onRefresh(ref),
                color: const Color(0xFF005F21),
                child: representativePropertiesAsync.when(
                  data: (propertyList) {
                    final activeProperties = propertyList
                        .where((property) => property.status == 1)
                        .toList();

                    if (activeProperties.isEmpty) {
                      return SingleChildScrollView(
                        physics: const AlwaysScrollableScrollPhysics(),
                        child: Center(
                          child: Padding(
                            padding: const EdgeInsets.all(20.0),
                            child: Text(
                              localizations.propertyTypeNoActiveFound, 
                              style: const TextStyle(color: Colors.grey, fontSize: 16),
                              textAlign: TextAlign.center,
                            ),
                          ),
                        ),
                      );
                    }

                    final List<Map<String, String>> categoriesForList = activeProperties.map((property) {
                      return {
                        'title': property.tags,
                        'image': property.image,
                        'type': property.tags.toLowerCase(),
                      };
                    }).toList();

                    return PropertyTypeList(categories: categoriesForList);
                  },
                  loading: () => Skeletonizer(
                    enabled: true,
                    child: ListView.builder(
                      padding: const EdgeInsets.all(16),
                      itemCount: 5,
                      itemBuilder: (context, index) {
                        return PropertyItem(
                          title: 'Loading Property Type',
                          image: '',
                          onTap: () {},
                        );
                      },
                    ),
                  ),
                  error: (error, stack) {
                    AppLogger.e('Error loading property types', error, stack, 'PROPERTYTYPE');
                    return SingleChildScrollView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      child: Center(
                        child: Padding(
                          padding: const EdgeInsets.all(20.0),
                          child: Text(
                            '${localizations.propertyTypeFailedToLoad}: $error', 
                            style: const TextStyle(color: Colors.red, fontSize: 16),
                            textAlign: TextAlign.center,
                          ),
                        ),
                      ),
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}