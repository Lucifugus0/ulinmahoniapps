import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:skeletonizer/skeletonizer.dart';
import '../../../../../core/layout/mainlayout.dart';
import '../../../../../core/widgets/appbar.dart';
import '../../provider/mybooking_provider.dart';
import '../../controller/mybooking_controller.dart';
import 'package:ulinmahoniapps/core/constants/appcolor_constants.dart';
import 'package:go_router/go_router.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';
import 'package:ulinmahoniapps/core/utils/app_logger.dart';

class MyBookingPage extends ConsumerStatefulWidget {
  const MyBookingPage({super.key});

  @override
  ConsumerState<MyBookingPage> createState() => _MyBookingPageState();
}

class _MyBookingPageState extends ConsumerState<MyBookingPage>
    with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    WidgetsBinding.instance.addPostFrameCallback((_) {
      ref.invalidate(userBookingsProvider);
    });
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  Future<void> _onRefresh() async {
    ref.invalidate(userBookingsProvider);
    await ref.read(userBookingsProvider.future).catchError((_) {});
    AppLogger.s('Data refreshed successfully', 'MYBOOKING');
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!; 
    if (!checkLoginAndRedirect(context, ref)) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }
    final bookingsAsync = ref.watch(userBookingsProvider);

    return MainLayout(
      showNavBar: false,
      showBottomNav: false,
      currentIndex: 1,
      child: Column(
        children: [
          CustomAppBar(title: localizations.myBookingTitle, showBackButton: false), 
          Builder(
            builder: (context) {
              final isDark = Theme.of(context).brightness == Brightness.dark;
              return Material(
                color: isDark ? AppColors.surfaceDark : Colors.white,
                child: TabBar(
                  controller: _tabController,
                  indicator: UnderlineTabIndicator(
                    borderSide: BorderSide(color: isDark ? Colors.white : Colors.black, width: 2.0),
                    insets: const EdgeInsets.symmetric(horizontal: 50.0),
                  ),
                  labelColor: isDark ? Colors.white : Colors.black,
                  unselectedLabelColor: Colors.grey,
                  labelStyle: const TextStyle(fontWeight: FontWeight.w500),
                  tabs: [
                    Tab(text: localizations.upcomingTab),
                    Tab(text: localizations.completedTab),
                  ],
                ),
              );
            },
          ),
          Expanded(
            child: RefreshIndicator(
              onRefresh: _onRefresh,
              child: bookingsAsync.when(
                data: (bookings) {
                  return TabBarView(
                    controller: _tabController,
                    children: [
                      RefreshIndicator(
                        onRefresh: _onRefresh,
                        child: ListView(
                          padding: const EdgeInsets.all(10),
                          physics: const AlwaysScrollableScrollPhysics(),
                          children: buildBookingList(bookings, 'all bookings'),
                        ),
                      ),
                      RefreshIndicator(
                        onRefresh: _onRefresh,
                        child: ListView(
                          padding: const EdgeInsets.all(10),
                          physics: const AlwaysScrollableScrollPhysics(),
                          children: buildBookingList(bookings, 'completed'),
                        ),
                      ),
                    ],
                  );
                },
                loading: () => Skeletonizer(
                  enabled: true,
                  child: TabBarView(
                    controller: _tabController,
                    children: [
                      ListView(
                        padding: const EdgeInsets.all(10),
                        children: List.generate(
                          5,
                          (index) => Card(
                            color: Colors.white,
                            margin: const EdgeInsets.symmetric(vertical: 8),
                            child: Padding(
                              padding: const EdgeInsets.all(10),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    height: 100,
                                    width: 100,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(8),
                                      color: Colors.grey[300],
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text(
                                          'Loading Property Name Here',
                                          style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        const Text(
                                          'Loading Room Name',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey,
                                          ),
                                        ),
                                        const SizedBox(height: 6),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: Colors.grey,
                                            borderRadius: BorderRadius.circular(6),
                                          ),
                                          child: const Text(
                                            'Loading Status',
                                            style: TextStyle(
                                              fontSize: 11,
                                              color: Colors.white,
                                              fontWeight: FontWeight.w600,
                                            ),
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        const Text(
                                          'Check-in: 01 Jan 2024',
                                          style: TextStyle(fontSize: 12),
                                        ),
                                        const Text(
                                          'Check-out: 02 Jan 2024',
                                          style: TextStyle(fontSize: 12),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                      ListView(
                        padding: const EdgeInsets.all(10),
                        children: List.generate(
                          3,
                          (index) => Card(
                            color: Colors.white,
                            margin: const EdgeInsets.symmetric(vertical: 8),
                            child: Padding(
                              padding: const EdgeInsets.all(10),
                              child: Row(
                                crossAxisAlignment: CrossAxisAlignment.start,
                                children: [
                                  Container(
                                    height: 100,
                                    width: 100,
                                    decoration: BoxDecoration(
                                      borderRadius: BorderRadius.circular(8),
                                      color: Colors.grey[300],
                                    ),
                                  ),
                                  const SizedBox(width: 12),
                                  Expanded(
                                    child: Column(
                                      crossAxisAlignment: CrossAxisAlignment.start,
                                      children: [
                                        const Text(
                                          'Loading Property Name Here',
                                          style: TextStyle(
                                            fontSize: 14,
                                            fontWeight: FontWeight.bold,
                                          ),
                                        ),
                                        const Text(
                                          'Loading Room Name',
                                          style: TextStyle(
                                            fontSize: 12,
                                            color: Colors.grey,
                                          ),
                                        ),
                                        const SizedBox(height: 6),
                                        Container(
                                          padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 4),
                                          decoration: BoxDecoration(
                                            color: Colors.grey,
                                            borderRadius: BorderRadius.circular(6),
                                          ),
                                          child: const Text(
                                            'Loading Status',
                                            style: TextStyle(
                                              fontSize: 11,
                                              color: Colors.white,
                                              fontWeight: FontWeight.w600,
                                            ),
                                          ),
                                        ),
                                        const SizedBox(height: 8),
                                        const Text(
                                          'Check-in: 01 Jan 2024',
                                          style: TextStyle(fontSize: 12),
                                        ),
                                        const Text(
                                          'Check-out: 02 Jan 2024',
                                          style: TextStyle(fontSize: 12),
                                        ),
                                      ],
                                    ),
                                  ),
                                ],
                              ),
                            ),
                          ),
                        ),
                      ),
                    ],
                  ),
                ),
                error: (e, stackTrace) {
                  // Log error for developers
                  AppLogger.e('Error fetching bookings', e, stackTrace, 'MYBOOKING');

                  // Show friendly empty state message
                  return RefreshIndicator(
                    onRefresh: _onRefresh,
                    child: ListView(
                      physics: const AlwaysScrollableScrollPhysics(),
                      children: [
                        SizedBox(height: MediaQuery.of(context).size.height * 0.2),
                        Center(
                          child: Column(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Icon(
                                Icons.inbox_outlined,
                                size: 80,
                                color: Colors.grey.shade400,
                              ),
                              const SizedBox(height: 16),
                              Text(
                                localizations.myBookingEmptyTitle,
                                style: TextStyle(
                                  fontSize: 18,
                                  fontWeight: FontWeight.w600,
                                  color: Colors.grey.shade700,
                                ),
                                textAlign: TextAlign.center,
                              ),
                              const SizedBox(height: 8),
                              Padding(
                                padding: const EdgeInsets.symmetric(horizontal: 32.0),
                                child: Text(
                                  localizations.myBookingEmptyMessage,
                                  style: TextStyle(
                                    fontSize: 14,
                                    color: Colors.grey.shade600,
                                  ),
                                  textAlign: TextAlign.center,
                                ),
                              ),
                              const SizedBox(height: 24),
                              ElevatedButton.icon(
                                onPressed: () => context.go('/home'),
                                icon: const Icon(Icons.search, color: Colors.white),
                                label: Text(localizations.myBookingBrowseProperties),
                                style: ElevatedButton.styleFrom(
                                  // Use primaryAdaptive for dark/light mode compatibility
                                  backgroundColor: AppColors.primaryAdaptive(context),
                                  foregroundColor: Colors.white,
                                  padding: const EdgeInsets.symmetric(horizontal: 24, vertical: 12),
                                  shape: RoundedRectangleBorder(
                                    borderRadius: BorderRadius.circular(8),
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                      ],
                    ),
                  );
                },
              ),
            ),
          ),
        ],
      ),
    );
  }
}