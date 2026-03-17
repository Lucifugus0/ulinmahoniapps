
import 'package:flutter/material.dart';
import 'app_logger.dart';


class GoRouterObserver extends NavigatorObserver {

  @override
  void didPush(Route<dynamic> route, Route<dynamic>? previousRoute) {
    AppLogger.d('GO_ROUTER_OBSERVER: PUSHED - New route: ${route.settings.name ?? route.settings.arguments ?? 'Unnamed Route'} | Previous route: ${previousRoute?.settings.name ?? previousRoute?.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }


  @override
  void didPop(Route<dynamic> route, Route<dynamic>? previousRoute) {
    AppLogger.d('GO_ROUTER_OBSERVER: POPPED - Route: ${route.settings.name ?? route.settings.arguments ?? 'Unnamed Route'} | To route: ${previousRoute?.settings.name ?? previousRoute?.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }


  @override
  void didReplace({Route<dynamic>? newRoute, Route<dynamic>? oldRoute}) {

    AppLogger.d('GO_ROUTER_OBSERVER: REPLACED - Old route: ${oldRoute?.settings.name ?? oldRoute?.settings.arguments ?? 'Unnamed Route'} | New route: ${newRoute?.settings.name ?? newRoute?.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }

  @override
  void didRemove(Route<dynamic> route, Route<dynamic>? previousRoute) {

    AppLogger.d('GO_ROUTER_OBSERVER: REMOVED - Route: ${route.settings.name ?? route.settings.arguments ?? 'Unnamed Route'} | Previous route: ${previousRoute?.settings.name ?? previousRoute?.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }


  @override
  void didDeactivate(Route<dynamic> route) {
    AppLogger.d('GO_ROUTER_OBSERVER: DEACTIVATED - Route: ${route.settings.name ?? route.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }

  @override
  void didActivate(Route<dynamic> route) {
    AppLogger.d('GO_ROUTER_OBSERVER: ACTIVATED - Route: ${route.settings.name ?? route.settings.arguments ?? 'Unnamed Route'}', 'ROUTER');
  }
}