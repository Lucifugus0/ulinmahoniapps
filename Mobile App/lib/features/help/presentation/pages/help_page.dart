import 'package:flutter/material.dart';
import 'package:flutter/rendering.dart';
import 'package:ulinmahoniapps/core/widgets/appbar.dart';
import '../../../../core/layout/mainlayout.dart';
import '../widgets/contacus.dart';
import '../widgets/faq.dart';
import 'package:ulinmahoniapps/l10n/app_localizations.dart';

class HelpPage extends StatefulWidget {
  const HelpPage({Key? key}) : super(key: key);

  @override
  State<HelpPage> createState() => _HelpPageState();
}

class _HelpPageState extends State<HelpPage> with SingleTickerProviderStateMixin {
  late TabController _tabController;

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
  }

  @override
  void dispose() {
    _tabController.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final localizations = AppLocalizations.of(context)!;

    return MainLayout(
      currentIndex: 3,
      child: Scaffold(
        backgroundColor: Colors.white,
        appBar: CustomAppBar(title: localizations.helpCenter),
        body: Column(
          children: [
            TabBar(
              controller: _tabController,
              indicatorColor: const Color(0xFF134E3A),
              labelColor: const Color(0xFF134E3A),
              unselectedLabelColor: Colors.grey,
              labelStyle: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold),
              tabs: [
                Tab(text: localizations.faqTabLabel),
                Tab(text: localizations.contactUsTabLabel),
              ],
            ),
            Expanded( 
              child: TabBarView(
                controller: _tabController,
                children: [
                  faq(),
                  help(context),
                ],
              ),
            ),
          ],
        ),
      ),
    );
  }
}
