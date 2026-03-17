import '../widgets/propertyitem.dart';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../searchresult/provider/searchresult_provider.dart';
import '../../../../core/constants/app_asset_constants.dart';
import '../../../../core/constants/appcolor_constants.dart';
import '../../../../core/widgets/dialog/notificationdialog.dart';
import '../../../../core/utils/app_logger.dart';

class PropertyTypeList extends ConsumerWidget { 
  const PropertyTypeList({Key? key, required this.categories})
      : super(key: key);
  final List<Map<String, String>> categories;

  @override
  Widget build(BuildContext context, WidgetRef ref) { 
    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: categories.length,
      itemBuilder: (context, index) {
        final category = categories[index];

        final String title = category['title'] ?? 'Nama Kategori Tidak Ada';
        final String image = category['image'] ?? AppImage.defaultPropertyImage;
        final String type = category['type'] ?? ''; 

        return PropertyItem(
          title: title,
          image: image,
          onTap: () {
            if (type.isNotEmpty) {
              
              
              
              ref.read(searchFilterProvider.notifier).updateFilterPartial(category: type);

              
              final route = '/search';
              context.push(route);
              AppLogger.d('Navigating to $route after setting category filter: $type', 'PROPERTYTYPE');
            } else {
              AppLogger.w('Error: Tipe properti tidak ditemukan untuk kategori: $title', 'PROPERTYTYPE');
              showNotificationDialog(
                context,
                'Tipe properti tidak valid. Tidak dapat menavigasi.',
                defaultIcon: Icons.error_outline,
                iconColor: AppColors.secondaryColor,
              );
            }
          },
        );
      },
    );
  }
}