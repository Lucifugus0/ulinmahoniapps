import 'package:flutter/material.dart';

/// Helper class to map facility icon strings to Flutter IconData
/// Supports both Iconify-style naming and custom mappings
class FacilityIconMapper {
  /// Map icon string from API to Flutter IconData
  /// Supports format: "prefix:icon-name" (e.g., "material-symbols:wifi")
  static IconData getIcon(String iconString) {
    // Remove any whitespace
    iconString = iconString.trim();

    // Check if it's an Iconify-style string (e.g., "material-symbols:wifi")
    if (iconString.contains(':')) {
      final parts = iconString.split(':');
      if (parts.length == 2) {
        final iconName = parts[1].toLowerCase().replaceAll('-', '_');
        return _getIconByName(iconName);
      }
    }

    // Direct icon name lookup
    return _getIconByName(iconString.toLowerCase().replaceAll('-', '_'));
  }

  /// Get IconData by icon name (fuzzy matching)
  static IconData _getIconByName(String name) {
    // AC / Air Conditioner
    if (name.contains('ac') || name.contains('air') && name.contains('condition')) {
      return Icons.ac_unit;
    }

    // WiFi
    if (name.contains('wifi') || name.contains('wi_fi')) {
      return Icons.wifi;
    }

    // Desk / Table / Furniture
    if (name.contains('desk') || name.contains('table') || name.contains('meja')) {
      return Icons.desk;
    }

    // Chair / Kursi
    if (name.contains('chair') || name.contains('kursi')) {
      return Icons.chair;
    }

    // Bed / Kasur
    if (name.contains('bed') || name.contains('kasur')) {
      return Icons.bed;
    }

    // Bathroom / Toilet / Kamar Mandi
    if (name.contains('bath') || name.contains('toilet') || name.contains('shower')) {
      return Icons.bathroom;
    }

    // Kitchen / Dapur
    if (name.contains('kitchen') || name.contains('dapur')) {
      return Icons.kitchen;
    }

    // TV / Television
    if (name.contains('tv') || name.contains('television')) {
      return Icons.tv;
    }

    // Parking / Parkir
    if (name.contains('park') || name.contains('garage')) {
      return Icons.local_parking;
    }

    // Laundry / Washing Machine
    if (name.contains('laundry') || name.contains('wash') || name.contains('cuci')) {
      return Icons.local_laundry_service;
    }

    // Security / CCTV / Camera
    if (name.contains('security') || name.contains('cctv') || name.contains('camera')) {
      return Icons.security;
    }

    // Elevator / Lift
    if (name.contains('elevator') || name.contains('lift')) {
      return Icons.elevator;
    }

    // Pool / Swimming / Kolam
    if (name.contains('pool') || name.contains('swim') || name.contains('kolam')) {
      return Icons.pool;
    }

    // Gym / Fitness
    if (name.contains('gym') || name.contains('fitness')) {
      return Icons.fitness_center;
    }

    // Restaurant / Dining / Cafe
    if (name.contains('restaurant') || name.contains('dining') || name.contains('cafe') || name.contains('food')) {
      return Icons.restaurant;
    }

    // Hospital / Medical / Health
    if (name.contains('hospital') || name.contains('medical') || name.contains('health')) {
      return Icons.local_hospital;
    }

    // School / Education
    if (name.contains('school') || name.contains('education')) {
      return Icons.school;
    }

    // Shop / Shopping / Store
    if (name.contains('shop') || name.contains('store') || name.contains('mall')) {
      return Icons.shopping_bag;
    }

    // Transport / Bus / Train
    if (name.contains('transport') || name.contains('bus') || name.contains('train')) {
      return Icons.directions_bus;
    }

    // Balcony / Balkon
    if (name.contains('balcony') || name.contains('balkon')) {
      return Icons.balcony;
    }

    // Closet / Wardrobe / Lemari
    if (name.contains('closet') || name.contains('wardrobe') || name.contains('lemari')) {
      return Icons.bedroom_parent;
    }

    // Fan / Kipas
    if (name.contains('fan') || name.contains('kipas')) {
      return Icons.air;
    }

    // Light / Lamp / Lampu
    if (name.contains('light') || name.contains('lamp') || name.contains('lampu')) {
      return Icons.lightbulb;
    }

    // Window / Jendela
    if (name.contains('window') || name.contains('jendela')) {
      return Icons.window;
    }

    // Door / Pintu
    if (name.contains('door') || name.contains('pintu')) {
      return Icons.door_sliding;
    }

    // Heater / Pemanas
    if (name.contains('heat') || name.contains('panas')) {
      return Icons.heat_pump;
    }

    // Refrigerator / Kulkas
    if (name.contains('fridge') || name.contains('refrigerator') || name.contains('kulkas')) {
      return Icons.kitchen;
    }

    // Microwave
    if (name.contains('microwave')) {
      return Icons.microwave;
    }

    // Iron / Setrika
    if (name.contains('iron') || name.contains('setrika')) {
      return Icons.iron;
    }

    // Default icon if no match found
    return Icons.check_circle_outline;
  }

  /// Get color for icon based on name (optional feature)
  /// @deprecated All icons now use consistent dark green color (0xFF134E3A)
  /// This method is kept for backward compatibility but returns null
  static Color? getIconColor(String iconString) {
    return null; // All icons now use dark green (0xFF134E3A)
  }
}
