import 'package:flutter/material.dart';



class CustomIconButton extends StatelessWidget {
  
  final String text;
  
  final IconData icon;
  
  final VoidCallback onPressed;
  
  final Color buttonColor;
  
  final Color textColor;
  
  final Color iconColor;
  
  final double? width;
  
  final double? height;
  
  final EdgeInsetsGeometry padding;
  
  final double borderRadius;
  
  final double? fontSize;
  
  final double? iconSize;

  const CustomIconButton({
    Key? key,
    required this.text,
    required this.icon,
    required this.onPressed,
    this.buttonColor = Colors.blue, 
    this.textColor = Colors.white,   
    this.iconColor = Colors.white,   
    this.width,
    this.height,
    this.padding = const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
    this.borderRadius = 8.0,
    this.fontSize,
    this.iconSize,
  }) : super(key: key);

  @override
  Widget build(BuildContext context) {
    
    return SizedBox(
      width: width,
      height: height,
      child: ElevatedButton.icon(
        onPressed: onPressed,
        style: ElevatedButton.styleFrom(
          backgroundColor: buttonColor, 
          foregroundColor: textColor,     
          padding: padding,              
          shape: RoundedRectangleBorder(
            borderRadius: BorderRadius.circular(borderRadius), 
          ),
        ),
        icon: Icon(
          icon,
          color: iconColor,
          size: iconSize,
        ),
        label: Text(
          text,
          style: TextStyle(
            color: textColor,
            fontSize: fontSize,
            fontWeight: FontWeight.bold,
          ),
          overflow: TextOverflow.ellipsis,
          maxLines: 2,
          textAlign: TextAlign.center,
        ),
      ),
    );
  }
}