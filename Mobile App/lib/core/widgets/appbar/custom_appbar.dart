import 'dart:ui';
import 'package:flutter/material.dart';
import 'package:go_router/go_router.dart';
import '../../theme/glass_theme.dart';

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
    // Dark mode detection — fallback defaults respect the current theme
    final isDark = Theme.of(context).brightness == Brightness.dark;
    return AppBar(
      // Transparent so the glass flexibleSpace shows through
      backgroundColor: Colors.transparent,
      elevation: 0,
      scrolledUnderElevation: 0,
      // Liquid glass backdrop blur behind the app bar
      flexibleSpace: ClipRect(
        child: BackdropFilter(
          filter: GlassTheme.standardBlur,
          child: Container(
            decoration: BoxDecoration(
              color: isDark
                  ? const Color(0xFF1F2937).withValues(alpha: 0.45)
                  : Colors.white.withValues(alpha: 0.45),
              border: Border(
                bottom: BorderSide(
                  color: isDark
                      ? GlassTheme.glassBorderDark
                      : GlassTheme.glassBorderLight,
                  width: GlassTheme.borderWidth,
                ),
              ),
            ),
          ),
        ),
      ),
      leading: IconButton(
        icon: Icon(
          Icons.arrow_back,
          color: iconColor ?? (isDark ? Colors.white : Colors.black),
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
              color: titleColor ?? (isDark ? Colors.white : Colors.black),
            ),
      ),
      titleSpacing: 0,
      actions: actions,
    );
  }

  @override
  Size get preferredSize => const Size.fromHeight(kToolbarHeight);
}
