import 'package:flutter/material.dart';
import 'package:flutter_localizations/flutter_localizations.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'core/i18n/app_strings.dart';
import 'core/design/remote_design.dart';
import 'core/theme/theme_controller.dart';
import 'core/widgets/global_top_controls.dart';
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
    final remote =
        ref.watch(remoteDesignProvider).asData?.value ?? RemoteDesign.fallback();

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
      theme: AppThemeFactory.build(
        settings.theme,
        settings.fontScale,
        remote.tokens,
      ),
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
    if (target.startsWith('page:')) {
      return DynamicPageScreen(slug: target.substring(5));
    }
    return const ExploreScreen();
  }

  @override
  Widget build(BuildContext context) {
    final remote =
        ref.watch(remoteDesignProvider).asData?.value ?? RemoteDesign.fallback();
    final t = AppStrings.of(context);
    final configured = remote.navigation
        .where(
          (e) =>
              e is Map &&
              e['location'] == 'bottom' &&
              (corePages.containsKey('${e['target']}') ||
                  '${e['target']}'.startsWith('page:')),
        )
        .map((e) {
          final item = Map<String, dynamic>.from(e as Map);
          final target = '${item['target']}';
          item['label'] = switch (target) {
            'home' => t('home'),
            'matches' => t('matches'),
            'explore' => t('explore'),
            'news' => t('news'),
            'more' => t('more'),
            'world' => t('world'),
            _ => item['label'] ?? target,
          };
          return item;
        })
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
    final pages = nav.map((n) => pageFor('${n['target']}')).toList();

    return LayoutBuilder(
      builder: (context, constraints) {
        final desktop = constraints.maxWidth >= 980;
        if (!desktop) {
          return Scaffold(
            body: Column(
              children: [
                SafeArea(
                  bottom: false,
                  child: Container(
                    height: 58,
                    padding: const EdgeInsets.symmetric(horizontal: 14),
                    decoration: BoxDecoration(
                      color: Theme.of(context).scaffoldBackgroundColor.withValues(alpha: .96),
                      border: Border(bottom: BorderSide(color: Theme.of(context).dividerColor)),
                    ),
                    child: Row(
                      children: [
                        Image.asset('assets/icons/scoretime_icon.png', width: 34, height: 34),
                        const SizedBox(width: 9),
                        const Expanded(
                          child: Text('ScoreTime', style: TextStyle(fontSize: 18, fontWeight: FontWeight.w900)),
                        ),
                        const GlobalTopControls(compact: true),
                      ],
                    ),
                  ),
                ),
                Expanded(child: IndexedStack(index: index, children: pages)),
              ],
            ),
            bottomNavigationBar: NavigationBar(
              selectedIndex: index,
              onDestinationSelected: (value) => setState(() => index = value),
              destinations: nav
                  .map(
                    (n) => NavigationDestination(
                      icon: Icon(iconFor('${n['icon']}')),
                      selectedIcon: Icon(
                        iconFor('${n['icon']}'),
                        color: Theme.of(context).colorScheme.secondary,
                      ),
                      label: '${n['label']}',
                    ),
                  )
                  .toList(),
            ),
          );
        }

        final extended = constraints.maxWidth >= 1320;
        return Scaffold(
          body: Row(
            children: [
              _DesktopRail(
                extended: extended,
                index: index,
                nav: nav,
                iconFor: iconFor,
                onSelect: (value) => setState(() => index = value),
              ),
              VerticalDivider(width: 1, color: Theme.of(context).dividerColor),
              Expanded(
                child: Column(
                  children: [
                    const _DesktopTopBar(),
                    Divider(height: 1, color: Theme.of(context).dividerColor),
                    Expanded(
                      child: Align(
                        alignment: Alignment.topCenter,
                        child: ConstrainedBox(
                          constraints: const BoxConstraints(maxWidth: 1580),
                          child: IndexedStack(index: index, children: pages),
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
    );
  }
}

class _DesktopRail extends StatelessWidget {
  final bool extended;
  final int index;
  final List<Map<String, dynamic>> nav;
  final IconData Function(String?) iconFor;
  final ValueChanged<int> onSelect;

  const _DesktopRail({
    required this.extended,
    required this.index,
    required this.nav,
    required this.iconFor,
    required this.onSelect,
  });

  @override
  Widget build(BuildContext context) {
    return SafeArea(
      child: NavigationRail(
        selectedIndex: index,
        onDestinationSelected: onSelect,
        extended: extended,
        minWidth: 76,
        minExtendedWidth: 238,
        leading: Padding(
          padding: const EdgeInsets.fromLTRB(12, 16, 12, 28),
          child: Row(
            mainAxisSize: MainAxisSize.min,
            children: [
              Container(
                padding: const EdgeInsets.all(3),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(16),
                  gradient: const LinearGradient(
                    colors: [ScoreTimeColors.blue, ScoreTimeColors.cyan],
                  ),
                ),
                child: ClipRRect(
                  borderRadius: BorderRadius.circular(13),
                  child: Image.asset(
                    'assets/icons/scoretime_icon.png',
                    width: 44,
                    height: 44,
                  ),
                ),
              ),
              if (extended) ...[
                const SizedBox(width: 11),
                const Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(
                      'ScoreTime',
                      style: TextStyle(fontSize: 20, fontWeight: FontWeight.w900),
                    ),
                    Text(
                      'EVERY MOMENT COUNTS',
                      style: TextStyle(
                        fontSize: 8.5,
                        letterSpacing: 1.15,
                        color: ScoreTimeColors.cyan,
                        fontWeight: FontWeight.w800,
                      ),
                    ),
                  ],
                ),
              ],
            ],
          ),
        ),
        destinations: nav
            .map(
              (n) => NavigationRailDestination(
                icon: Icon(iconFor('${n['icon']}')),
                selectedIcon: Icon(iconFor('${n['icon']}')),
                label: Text('${n['label']}'),
              ),
            )
            .toList(),
        trailing: Expanded(
          child: Align(
            alignment: Alignment.bottomCenter,
            child: Padding(
              padding: const EdgeInsets.only(bottom: 18),
              child: Tooltip(
                message: 'Secure & protected',
                child: Icon(
                  Icons.verified_user_rounded,
                  size: 20,
                  color: Theme.of(context).colorScheme.secondary,
                ),
              ),
            ),
          ),
        ),
      ),
    );
  }
}

class _DesktopTopBar extends ConsumerWidget {
  const _DesktopTopBar();

  @override
  Widget build(BuildContext context, WidgetRef ref) {
    final t = AppStrings.of(context);
    return SizedBox(
      height: 72,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 24),
        child: Row(
          children: [
            Expanded(
              child: ConstrainedBox(
                constraints: const BoxConstraints(maxWidth: 520),
                child: TextField(
                  decoration: InputDecoration(
                    isDense: true,
                    prefixIcon: const Icon(Icons.search_rounded),
                    hintText: t('global_search'),
                  ),
                ),
              ),
            ),
            const Spacer(),
            _TopAction(icon: Icons.live_tv_rounded, label: t('tv_guide')),
            const SizedBox(width: 8),
            _TopAction(icon: Icons.notifications_none_rounded, label: t('alerts')),
            const SizedBox(width: 8),
            const GlobalTopControls(),
            const SizedBox(width: 8),
            const CircleAvatar(
              radius: 20,
              backgroundColor: ScoreTimeColors.panel2,
              child: Icon(Icons.person_rounded, size: 20),
            ),
          ],
        ),
      ),
    );
  }
}

class _TopAction extends StatelessWidget {
  final IconData icon;
  final String label;
  const _TopAction({required this.icon, required this.label});

  @override
  Widget build(BuildContext context) {
    return Tooltip(
      message: label,
      child: IconButton.filledTonal(onPressed: () {}, icon: Icon(icon)),
    );
  }
}
