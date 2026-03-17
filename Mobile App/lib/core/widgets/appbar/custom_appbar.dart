import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';

class CustomAppBar extends StatelessWidget implements PreferredSizeWidget {
  final String title;
  final Color? backgroundColor;
  final Color? iconColor;
  final Color? titleColor;
  final double? elevation;
  final VoidCallback? onBackPressed;
  final List<Widget>? actions;

  const CustomAppBar({
    super.key,
    required this.title,
    this.backgroundColor,
    this.iconColor,
    this.titleColor,
    this.elevation,
    this.onBackPressed,
    this.actions,
  });

  @override
  Widget build(BuildContext context) {
    return AppBar(
      backgroundColor: backgroundColor ?? Colors.white,
      elevation: elevation ?? 2,
      shadowColor: Colors.black.withValues(alpha: 0.1),
      leading: IconButton(
        icon: Icon(
          Icons.arrow_back,
          color: iconColor ?? Colors.black,
        ),
        onPressed: onBackPressed ?? () {
          // Use GoRouter's pop if available, fallback to Navigator.pop
          if (context.canPop()) {
            context.pop();
          } else {
            Navigator.of(context).pop();
          }
        },
      ),
      title: Text(
        title,
        style: Theme.of(context).textTheme.titleLarge?.copyWith(
              fontWeight: FontWeight.bold,
              color: titleColor ?? Colors.black,
            ),
      ),
      titleSpacing: 0,
      actions: actions,
    );
  }

  @override
  Size get preferredSize => const Size.fromHeight(kToolbarHeight);
}
