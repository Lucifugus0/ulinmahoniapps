import 'package:flutter/material.dart';
import 'package:ulinmahoniapps/core/utils/imageconverter_utils.dart';
import 'package:ulinmahoniapps/core/constants/appfontweight_constants.dart';


class PropertyItem extends StatelessWidget {
  final String title;
  final String image; 
  final VoidCallback onTap;

  const PropertyItem({
    required this.title,
    required this.image,
    required this.onTap,
    Key? key,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    return GestureDetector(
      onTap: onTap,
      child: Padding(
        padding: const EdgeInsets.only(bottom: 16),
        child: Container(
          height: 200,
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(16),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.15),
                spreadRadius: 0,
                blurRadius: 10,
                offset: const Offset(0, 4),
              ),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(16),
            child: Stack(
              fit: StackFit.expand,
              children: [
                // Background Image
                ImageConverter.convertStringToImageWidget(
                  image,
                  width: double.infinity,
                  height: 200,
                  fit: BoxFit.cover,
                  alignment: Alignment.center,
                ),

                // Gradient Overlay
                Container(
                  decoration: BoxDecoration(
                    gradient: LinearGradient(
                      begin: Alignment.topCenter,
                      end: Alignment.bottomCenter,
                      colors: [
                        Colors.transparent,
                        Colors.black.withOpacity(0.3),
                        Colors.black.withOpacity(0.8),
                      ],
                      stops: const [0.4, 0.7, 1.0],
                    ),
                  ),
                ),

                // Title at bottom left
                Positioned(
                  left: 16,
                  bottom: 16,
                  child: Text(
                    title,
                    style: const TextStyle(
                      fontSize: 24,
                      fontWeight: AppFontWeight.bold,
                      color: Colors.white,
                      shadows: [
                        Shadow(
                          color: Colors.black26,
                          offset: Offset(0, 2),
                          blurRadius: 4,
                        ),
                      ],
                    ),
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