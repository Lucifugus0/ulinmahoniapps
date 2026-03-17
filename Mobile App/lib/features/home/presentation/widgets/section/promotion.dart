import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../../../../core/widgets/card/propertycard.dart'; 
import '../../../data/promotion_data.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class PromotionSection extends StatefulWidget {
  final Color? backgroundColor; 
  final String? sectionTitle; 

  const PromotionSection({
    Key? key,
    this.backgroundColor,
    this.sectionTitle, 
  }) : super(key: key);

  @override
  State<PromotionSection> createState() => _PromotionSectionState();
}

class _PromotionSectionState extends State<PromotionSection> {
  @override
  Widget build(BuildContext context) {
    final textTheme = Theme.of(context).textTheme; 
    final localizations = AppLocalizations.of(context)!;

    return Container(
      color: widget.backgroundColor, 
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
            const SizedBox(height: 20),
            Padding(
              padding: EdgeInsets.symmetric(horizontal: 16),
              child: Text(
                localizations.promotion, 
                style: TextStyle(fontSize: 23, fontWeight: FontWeight.bold),
              ),
            ),
            const SizedBox(height: 20),
          SizedBox(
            height: MediaQuery.of(context).size.height * 0.27,
            child: ListView.builder(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              itemCount: promotionData.length,
              itemBuilder: (context, index) {
                return Row(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    PropertyCard(
                      image: promotionData[index]['image']!,
                      title: promotionData[index]['title']!,
                      onTap: () {

                      },
                    ),
                    
                    if (index < promotionData.length - 1)
                      const SizedBox(width: 16), 
                  ],
                );
              },
            ),
          ),
        ],
      ),
    );
  }
}