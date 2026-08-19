import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/i18n/app_strings.dart';
import 'core/design/remote_design.dart';
import 'core/theme/theme_controller.dart';
import 'features/home/presentation/home_screen.dart';
import 'features/matches/presentation/matches_screen.dart';
import 'features/news/presentation/news_screen.dart';
import 'features/explore/presentation/explore_screen.dart';
import 'features/settings/presentation/settings_screen.dart';
import 'features/custom_pages/presentation/dynamic_page_screen.dart';
import 'features/world/presentation/global_football_screen.dart';

void main() => runApp(const ProviderScope(child: ScoreTimeApp()));

class ScoreTimeApp extends ConsumerWidget {
  const ScoreTimeApp({super.key});
  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final settings = ref.watch(themeControllerProvider);
    final remote = ref.watch(remoteDesignProvider).asData?.value ?? RemoteDesign.fallback();
    return MaterialApp(
      debugShowCheckedModeBanner: false,
      title: 'ScoreTime',
      locale: Locale(settings.locale),
      supportedLocales: AppStrings.supported.map(Locale.new),
      localizationsDelegates: const [
        GlobalMaterialLocalizations.delegate,
        GlobalWidgetsLocalizations.delegate,
        GlobalCupertinoLocalizations.delegate,
      ],
      theme: AppThemeFactory.build(settings.theme, settings.fontScale, remote.tokens),
      home: const AppShell(),
    );
  }
}

class AppShell extends ConsumerStatefulWidget {
  const AppShell({super.key});
  @override
  ConsumerState<AppShell> createState() => _AppShellState();
}

class _AppShellState extends ConsumerState<AppShell> {
  int index = 0;
  static const corePages = <String, Widget>{
    'home': HomeScreen(),
    'matches': MatchesScreen(),
    'explore': ExploreScreen(),
    'news': NewsScreen(),
    'more': SettingsScreen(),
    'world': GlobalFootballScreen(),
  };

  IconData iconFor(String? name) => switch (name) {
        'home' => Icons.home_rounded,
        'sports_soccer' => Icons.sports_soccer_rounded,
        'newspaper' => Icons.newspaper_rounded,
        'search' => Icons.travel_explore_rounded,
        'tune' => Icons.grid_view_rounded,
        'star' => Icons.star_rounded,
        'calendar' => Icons.calendar_month_rounded,
        'public' => Icons.public_rounded,
        'language' => Icons.language_rounded,
        _ => Icons.circle_outlined,
      };

  Widget pageFor(String target) {
    if (corePages.containsKey(target)) return corePages[target]!;
    if (target.startsWith('page:')) return DynamicPageScreen(slug: target.substring(5));
    return const ExploreScreen();
  }

  @override
  Widget build(BuildContext context) {
    final remote = ref.watch(remoteDesignProvider).asData?.value ?? RemoteDesign.fallback();
    final t = AppStrings.of(context);
    final configured = remote.navigation
        .where((e) => e is Map && e['location'] == 'bottom' && (corePages.containsKey('${e['target']}') || '${e['target']}'.startsWith('page:')))
        .map((e) => Map<String, dynamic>.from(e as Map))
        .take(5)
        .toList();
    final nav = configured.isNotEmpty
        ? configured
        : <Map<String, dynamic>>[
            {'target': 'home', 'label': t('home'), 'icon': 'home'},
            {'target': 'matches', 'label': t('matches'), 'icon': 'sports_soccer'},
            {'target': 'explore', 'label': t('explore'), 'icon': 'search'},
            {'target': 'news', 'label': t('news'), 'icon': 'newspaper'},
            {'target': 'more', 'label': t('more'), 'icon': 'tune'},
          ];
    if (index >= nav.length) index = 0;
    return Scaffold(
      body: IndexedStack(index: index, children: nav.map((n) => pageFor('${n['target']}')).toList()),
      bottomNavigationBar: NavigationBar(
        selectedIndex: index,
        onDestinationSelected: (value) => setState(() => index = value),
        destinations: nav
            .map((n) => NavigationDestination(
                  icon: Icon(iconFor('${n['icon']}')),
                  selectedIcon: Icon(iconFor('${n['icon']}'), color: Theme.of(context).colorScheme.primary),
                  label: '${n['label']}',
                ))
            .toList(),
      ),
    );
  }
}
