import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/design/remote_design.dart';
import '../../../core/config/app_config.dart';
import '../../../core/network/demo_data.dart';
import '../../../core/i18n/app_strings.dart';
import '../../../core/network/api_client.dart';
import '../../transfers/presentation/transfer_intelligence_screen.dart';
import '../../world/presentation/global_football_screen.dart';
import '../../worldclass/presentation/world_class_screen.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});
  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  bool loading = true;
  List<dynamic> matches = [];
  List<dynamic> news = [];
  String? error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    if (AppConfig.webDemoMode) {
      if (!mounted) return;
      setState(() {
        matches = List<dynamic>.from(DemoData.matches);
        news = List<dynamic>.from(DemoData.news);
        loading = false;
        error = null;
      });
      return;
    }

    try {
      final api = ApiClient();
      final matchData = await api.getMatches();
      final newsResponse = await api.dio.get('/news');
      if (!mounted) return;
      setState(() {
        matches = matchData;
        news = List<dynamic>.from(newsResponse.data['data'] ?? []);
        loading = false;
        error = null;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        loading = false;
        error = 'Unable to refresh ScoreTime. Pull down to try again.';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final remote = ref.watch(remoteDesignProvider).asData?.value ?? RemoteDesign.fallback();
    final t = AppStrings.of(context);
    final live = matches.where((m) => ['live', 'halftime'].contains('${m['status']}'.toLowerCase())).toList();
    return Scaffold(
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: _load,
          child: CustomScrollView(
            slivers: [
              SliverToBoxAdapter(child: _ScoreTimeHeader(remote: remote)),
              SliverToBoxAdapter(child: _HeroCard(liveCount: live.length, t: t)),
              SliverToBoxAdapter(child: _QuickAccess(t: t)),
              SliverToBoxAdapter(child: _SectionTitle(kicker: 'LIVE & UPCOMING', title: t('matches'))),
              if (loading)
                const SliverToBoxAdapter(child: SizedBox(height: 180, child: Center(child: CircularProgressIndicator())))
              else
                SliverToBoxAdapter(child: _MatchRail(items: live.isNotEmpty ? live : matches.take(8).toList())),
              SliverToBoxAdapter(child: _SectionTitle(kicker: 'SCORETIME EDITORIAL', title: t('latest'))),
              SliverToBoxAdapter(child: _NewsFeed(items: news)),
              if (error != null)
                SliverToBoxAdapter(child: Padding(padding: const EdgeInsets.all(18), child: Text(error!, textAlign: TextAlign.center, style: TextStyle(color: Theme.of(context).colorScheme.error)))),
              const SliverToBoxAdapter(child: SizedBox(height: 28)),
            ],
          ),
        ),
      ),
    );
  }
}

class _ScoreTimeHeader extends StatelessWidget {
  final RemoteDesign remote;
  const _ScoreTimeHeader({required this.remote});
  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.fromLTRB(18, 12, 12, 8),
        child: Row(children: [
          ClipRRect(borderRadius: BorderRadius.circular(15), child: Image.asset('assets/icons/scoretime_icon.png', width: 46, height: 46, fit: BoxFit.cover)),
          const SizedBox(width: 11),
          Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
            RichText(text: TextSpan(style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900), children: [const TextSpan(text: 'Score'), TextSpan(text: 'Time', style: TextStyle(color: Theme.of(context).colorScheme.secondary))])),
            Text(remote.tagline.isEmpty ? 'Every moment counts.' : remote.tagline, maxLines: 1, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.bodySmall),
          ])),
          IconButton(onPressed: () {}, icon: const Icon(Icons.notifications_none_rounded)),
        ]),
      );
}

class _HeroCard extends StatelessWidget {
  final int liveCount;
  final String Function(String) t;
  const _HeroCard({required this.liveCount, required this.t});
  @override
  Widget build(BuildContext context) {
    final scheme = Theme.of(context).colorScheme;
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.fromLTRB(22, 24, 20, 22),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(30),
        gradient: const LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [Color(0xFF0A234B), Color(0xFF061329), Color(0xFF07101F)]),
        border: Border.all(color: Colors.white.withValues(alpha: .08)),
        boxShadow: [BoxShadow(color: scheme.primary.withValues(alpha: .12), blurRadius: 45, offset: const Offset(0, 18))],
      ),
      child: Stack(children: [
        Positioned(right: -22, bottom: -34, child: Opacity(opacity: .16, child: Image.asset('assets/icons/scoretime_icon.png', width: 170))),
        Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
          Row(children: [Container(width: 8, height: 8, decoration: const BoxDecoration(color: Color(0xFFFF4D6D), shape: BoxShape.circle)), const SizedBox(width: 8), Text('$liveCount ${t('live').toUpperCase()}', style: const TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.4, fontSize: 12, color: Color(0xFFFF8398)))]),
          const SizedBox(height: 14),
          const Text('Football moves fast.\nScoreTime moves faster.', style: TextStyle(fontSize: 31, height: 1.02, fontWeight: FontWeight.w900, letterSpacing: -1.2)),
          const SizedBox(height: 12),
          Text('Live scores • Match intelligence • News • Transfers', style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.white70)),
          const SizedBox(height: 20),
          FilledButton.icon(onPressed: () {}, icon: const Icon(Icons.sports_soccer_rounded), label: Text(t('matches'))),
        ]),
      ]),
    );
  }
}

class _QuickAccess extends StatelessWidget {
  final String Function(String) t;
  const _QuickAccess({required this.t});
  @override
  Widget build(BuildContext context) {
    final items = [
      (Icons.public_rounded, t('world'), () => Navigator.push(context, MaterialPageRoute(builder: (_) => const GlobalFootballScreen()))),
      (Icons.swap_horiz_rounded, t('transfers'), () => Navigator.push(context, MaterialPageRoute(builder: (_) => const TransferIntelligenceScreen()))),
      (Icons.insights_rounded, 'Intelligence', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WorldClassScreen()))),
      (Icons.emoji_events_rounded, 'Fan Hub', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WorldClassScreen()))),
    ];
    return SizedBox(
      height: 96,
      child: ListView.separated(
        padding: const EdgeInsets.symmetric(horizontal: 16),
        scrollDirection: Axis.horizontal,
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(width: 10),
        itemBuilder: (context, i) => InkWell(
          borderRadius: BorderRadius.circular(20),
          onTap: items[i].$3,
          child: Container(width: 132, padding: const EdgeInsets.all(14), decoration: BoxDecoration(borderRadius: BorderRadius.circular(20), color: Theme.of(context).colorScheme.surface, border: Border.all(color: Theme.of(context).colorScheme.outlineVariant.withValues(alpha: .22))), child: Column(crossAxisAlignment: CrossAxisAlignment.start, mainAxisAlignment: MainAxisAlignment.center, children: [Icon(items[i].$1, color: Theme.of(context).colorScheme.secondary), const SizedBox(height: 8), Text(items[i].$2, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w800))])),
        ),
      ),
    );
  }
}

class _SectionTitle extends StatelessWidget {
  final String kicker, title;
  const _SectionTitle({required this.kicker, required this.title});
  @override
  Widget build(BuildContext context) => Padding(padding: const EdgeInsets.fromLTRB(18, 28, 18, 12), child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(kicker, style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.3)), const SizedBox(height: 4), Text(title, style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900))]));
}

class _MatchRail extends StatelessWidget {
  final List<dynamic> items;
  const _MatchRail({required this.items});
  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) return const Padding(padding: EdgeInsets.all(22), child: Text('No matches loaded yet.'));
    return SizedBox(
      height: 184,
      child: ListView.separated(
        scrollDirection: Axis.horizontal,
        padding: const EdgeInsets.symmetric(horizontal: 16),
        itemCount: items.length,
        separatorBuilder: (_, __) => const SizedBox(width: 11),
        itemBuilder: (context, i) {
          final m = Map<String, dynamic>.from(items[i]);
          final h = Map<String, dynamic>.from(m['home_team'] ?? m['homeTeam'] ?? {});
          final a = Map<String, dynamic>.from(m['away_team'] ?? m['awayTeam'] ?? {});
          final live = ['live', 'halftime'].contains('${m['status']}'.toLowerCase());
          return Container(
            width: 286,
            padding: const EdgeInsets.all(17),
            decoration: BoxDecoration(borderRadius: BorderRadius.circular(24), color: Theme.of(context).colorScheme.surface, border: Border.all(color: live ? const Color(0x44FF4D6D) : Theme.of(context).colorScheme.outlineVariant.withValues(alpha: .22))),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Row(children: [Expanded(child: Text('${m['competition']?['name_en'] ?? m['competition']?['name_ar'] ?? 'Football'}', maxLines: 1, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.labelSmall)), if (live) const Text('● LIVE', style: TextStyle(color: Color(0xFFFF6B84), fontWeight: FontWeight.w900, fontSize: 11))]),
              const Spacer(),
              Row(children: [Expanded(child: Text('${h['name_en'] ?? h['name_ar'] ?? 'Home'}', maxLines: 2, style: const TextStyle(fontWeight: FontWeight.w800))), Text('${m['home_score'] ?? 0}  —  ${m['away_score'] ?? 0}', style: const TextStyle(fontSize: 22, fontWeight: FontWeight.w900)), Expanded(child: Text('${a['name_en'] ?? a['name_ar'] ?? 'Away'}', textAlign: TextAlign.end, maxLines: 2, style: const TextStyle(fontWeight: FontWeight.w800)))]),
              const Spacer(),
              Text('${m['venue'] ?? ''}  •  ${m['minute'] ?? m['status'] ?? ''}', maxLines: 1, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.bodySmall),
            ]),
          );
        },
      ),
    );
  }
}

class _NewsFeed extends StatelessWidget {
  final List<dynamic> items;
  const _NewsFeed({required this.items});
  @override
  Widget build(BuildContext context) {
    if (items.isEmpty) return const Padding(padding: EdgeInsets.all(22), child: Text('No published stories yet.'));
    return Padding(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      child: Column(children: items.take(7).map((raw) {
        final n = Map<String, dynamic>.from(raw);
        return Container(
          margin: const EdgeInsets.only(bottom: 10),
          padding: const EdgeInsets.all(16),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(22), color: Theme.of(context).colorScheme.surface, border: Border.all(color: Theme.of(context).colorScheme.outlineVariant.withValues(alpha: .22))),
          child: Row(crossAxisAlignment: CrossAxisAlignment.start, children: [
            Container(width: 54, height: 54, decoration: BoxDecoration(borderRadius: BorderRadius.circular(17), gradient: const LinearGradient(colors: [Color(0xFF0B8CFF), Color(0xFF18D7FF)])), child: Icon(n['is_breaking'] == true ? Icons.bolt_rounded : Icons.article_rounded, color: Colors.white)),
            const SizedBox(width: 13),
            Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text('${n['category'] ?? 'Football'}', style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontWeight: FontWeight.w800, fontSize: 11)), const SizedBox(height: 4), Text('${n['title'] ?? ''}', maxLines: 3, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w900, height: 1.2))])),
            const Icon(Icons.chevron_right_rounded),
          ]),
        );
      }).toList()),
    );
  }
}
