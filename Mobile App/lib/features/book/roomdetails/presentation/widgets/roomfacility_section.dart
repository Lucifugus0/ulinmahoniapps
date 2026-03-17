import 'package:flutter/material.dart';

class RoomFacilitiesTextGrid extends StatelessWidget {
  
  final List<String> facilities;

  const RoomFacilitiesTextGrid({
    super.key,
    required this.facilities,
  });

  @override
  Widget build(BuildContext context) {
    
    if (facilities.isEmpty) {
      return const SizedBox.shrink();
    }


    const int crossAxisCount = 2;
    final double mainAxisSpacing = 16.0;
    final double crossAxisSpacing = 16.0;
    const double childAspectRatio = 4.5; // Increased height for better visibility 

    return GridView.builder(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(), 
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: crossAxisCount,
        crossAxisSpacing: crossAxisSpacing,
        mainAxisSpacing: mainAxisSpacing,
        childAspectRatio: childAspectRatio,
      ),
      itemCount: facilities.length, 
      itemBuilder: (context, index) {
        final featureName = facilities[index]; 

        
        return Align(
          alignment: Alignment.centerLeft, 
          child: Row(
            mainAxisSize: MainAxisSize.min, 
            crossAxisAlignment: CrossAxisAlignment.start, 
            children: [
              Text(
                '• ',
                style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                  fontSize: 15,
                ),
              ),
              Flexible(
                child: Text(
                  featureName,
                  style: Theme.of(context).textTheme.bodyMedium?.copyWith(
                    fontSize: 15, // Explicitly set font size to 15
                  ),
                  softWrap: true,
                  overflow: TextOverflow.ellipsis,
                  maxLines: 2,
                ),
              ),
            ],
          ),
        );
      },
    );
  }
}

