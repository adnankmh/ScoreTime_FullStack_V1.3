import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import '../../../core/i18n/app_strings.dart';
import '../../../core/network/football_repository.dart';
import '../../../core/theme/theme_controller.dart';
import '../../world/presentation/global_football_screen.dart';
import '../../worldclass/presentation/world_class_screen.dart';
import '../../matches/presentation/matches_screen.dart';
import '../../settings/presentation/settings_screen.dart';

class HomeScreen extends ConsumerStatefulWidget {
  const HomeScreen({super.key});

  @override
  ConsumerState<HomeScreen> createState() => _HomeScreenState();
}

class _HomeScreenState extends ConsumerState<HomeScreen> {
  bool loading = true;
  String? error;
  List<dynamic> matches = [];
  List<dynamic> news = [];
  List<dynamic> players = [];
  List<dynamic> competitions = [];
  List<dynamic> transfers = [];
  Map<String, dynamic> summary = {};

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    setState(() {
      loading = true;
      error = null;
    });
    try {
      final repo = FootballRepository();
      final result = await Future.wait<dynamic>([
        repo.matches(),
        repo.personalizedNews(),
        repo.worldPlayers(),
        repo.worldCompetitions(),
        repo.transfers(),
        repo.worldSummary(),
      ]);
      if (!mounted) return;
      setState(() {
        matches = List<dynamic>.from(result[0] as List);
        news = List<dynamic>.from(result[1] as List);
        players = List<dynamic>.from(result[2] as List);
        competitions = List<dynamic>.from(result[3] as List);
        transfers = List<dynamic>.from(result[4] as List);
        summary = Map<String, dynamic>.from(result[5] as Map);
        loading = false;
      });
    } catch (_) {
      if (!mounted) return;
      setState(() {
        loading = false;
        error = 'live_data_unavailable';
      });
    }
  }

  @override
  Widget build(BuildContext context) {
    final live = matches
        .where((m) => ['live', 'halftime'].contains('${m['status']}'.toLowerCase()))
        .toList();
    final featured = live.isNotEmpty
        ? Map<String, dynamic>.from(live.first)
        : matches.isNotEmpty
            ? Map<String, dynamic>.from(matches.first)
            : <String, dynamic>{};
    final t = AppStrings.of(context);

    return Scaffold(
      backgroundColor: Colors.transparent,
      body: SafeArea(
        top: MediaQuery.sizeOf(context).width < 980,
        child: RefreshIndicator(
          onRefresh: _load,
          child: CustomScrollView(
            physics: const AlwaysScrollableScrollPhysics(),
            slivers: [
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 16, 16, 0),
                  child: _MobileHeader(liveCount: live.length),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 14, 16, 0),
                  child: _HeroCommandCenter(
                    featured: featured,
                    liveCount: live.length,
                    summary: summary,
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: _CompetitionRail(items: competitions),
              ),
              if (error != null)
                SliverToBoxAdapter(
                  child: Padding(
                    padding: const EdgeInsets.fromLTRB(16, 8, 16, 0),
                    child: _StatusStrip(message: t(error!)),
                  ),
                ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 18, 16, 0),
                  child: LayoutBuilder(
                    builder: (context, constraints) {
                      final wide = constraints.maxWidth >= 1180;
                      final medium = constraints.maxWidth >= 760;
                      if (wide) {
                        return Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Expanded(
                              flex: 12,
                              child: _LiveDesk(
                                loading: loading,
                                matches: live.isNotEmpty ? live : matches,
                              ),
                            ),
                            const SizedBox(width: 14),
                            Expanded(flex: 11, child: _NewsDesk(news: news)),
                            const SizedBox(width: 14),
                            Expanded(
                              flex: 8,
                              child: _PlayerDesk(players: players),
                            ),
                          ],
                        );
                      }
                      if (medium) {
                        return Column(
                          children: [
                            Row(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Expanded(
                                  child: _LiveDesk(
                                    loading: loading,
                                    matches: live.isNotEmpty ? live : matches,
                                  ),
                                ),
                                const SizedBox(width: 14),
                                Expanded(child: _NewsDesk(news: news)),
                              ],
                            ),
                            const SizedBox(height: 14),
                            _PlayerDesk(players: players),
                          ],
                        );
                      }
                      return Column(
                        children: [
                          _LiveDesk(
                            loading: loading,
                            matches: live.isNotEmpty ? live : matches,
                          ),
                          const SizedBox(height: 14),
                          _NewsDesk(news: news),
                          const SizedBox(height: 14),
                          _PlayerDesk(players: players),
                        ],
                      );
                    },
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
                  child: _SectionHeading(
                    kicker: 'SCORETIME INTELLIGENCE',
                    title: 'Read the match, not just the score',
                    action: 'Explore analytics',
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
                  child: LayoutBuilder(
                    builder: (context, constraints) {
                      if (constraints.maxWidth >= 960) {
                        return const Row(
                          crossAxisAlignment: CrossAxisAlignment.stretch,
                          children: [
                            Expanded(flex: 10, child: _TacticalPitchCard()),
                            SizedBox(width: 14),
                            Expanded(flex: 8, child: _MomentumCard()),
                            SizedBox(width: 14),
                            Expanded(flex: 7, child: _RadarCard()),
                          ],
                        );
                      }
                      return const Column(
                        children: [
                          _TacticalPitchCard(),
                          SizedBox(height: 14),
                          _MomentumCard(),
                          SizedBox(height: 14),
                          _RadarCard(),
                        ],
                      );
                    },
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 20, 16, 0),
                  child: _SectionHeading(
                    kicker: 'YOUR FOOTBALL',
                    title: 'Everything that matters to you',
                    action: 'Customize',
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 12, 16, 0),
                  child: LayoutBuilder(
                    builder: (context, constraints) {
                      final cards = <Widget>[
                        const _FollowCard(),
                        const _TVGuideCard(),
                        _TransferPulseCard(items: transfers),
                        const _FanZoneCard(),
                      ];
                      if (constraints.maxWidth >= 1040) {
                        return Row(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            for (var i = 0; i < cards.length; i++) ...[
                              Expanded(child: cards[i]),
                              if (i != cards.length - 1) const SizedBox(width: 12),
                            ],
                          ],
                        );
                      }
                      return Wrap(
                        spacing: 12,
                        runSpacing: 12,
                        children: cards
                            .map(
                              (card) => SizedBox(
                                width: constraints.maxWidth >= 650
                                    ? (constraints.maxWidth - 12) / 2
                                    : constraints.maxWidth,
                                child: card,
                              ),
                            )
                            .toList(),
                      );
                    },
                  ),
                ),
              ),
              SliverToBoxAdapter(
                child: Padding(
                  padding: const EdgeInsets.fromLTRB(16, 20, 16, 24),
                  child: _FeatureRibbon(summary: summary),
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}

class _MobileHeader extends StatelessWidget {
  final int liveCount;
  const _MobileHeader({required this.liveCount});

  @override
  Widget build(BuildContext context) {
    if (MediaQuery.sizeOf(context).width >= 980) return const SizedBox.shrink();
    return Row(
      children: [
        ClipRRect(
          borderRadius: BorderRadius.circular(14),
          child: Image.asset('assets/icons/scoretime_icon.png', width: 42, height: 42),
        ),
        const SizedBox(width: 10),
        const Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text('ScoreTime', style: TextStyle(fontSize: 19, fontWeight: FontWeight.w900)),
              Text(
                'EVERY MOMENT COUNTS',
                style: TextStyle(
                  fontSize: 8.5,
                  color: ScoreTimeColors.cyan,
                  fontWeight: FontWeight.w800,
                  letterSpacing: 1.05,
                ),
              ),
            ],
          ),
        ),
        _LiveBadge(text: '$liveCount LIVE'),
        const SizedBox(width: 6),
        IconButton.filledTonal(
          onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SettingsScreen())),
          icon: const Icon(Icons.notifications_none_rounded),
        ),
      ],
    );
  }
}

class _HeroCommandCenter extends StatelessWidget {
  final Map<String, dynamic> featured;
  final int liveCount;
  final Map<String, dynamic> summary;
  const _HeroCommandCenter({
    required this.featured,
    required this.liveCount,
    required this.summary,
  });

  @override
  Widget build(BuildContext context) {
    final width = MediaQuery.sizeOf(context).width;
    final desktop = width >= 900;
    return Container(
      constraints: const BoxConstraints(minHeight: 380),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(desktop ? 30 : 24),
        border: Border.all(color: Colors.white.withValues(alpha: .08)),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF06162E), Color(0xFF071A37), Color(0xFF031126)],
        ),
        boxShadow: [
          BoxShadow(
            color: ScoreTimeColors.blue.withValues(alpha: .12),
            blurRadius: 48,
            spreadRadius: -18,
          ),
        ],
      ),
      clipBehavior: Clip.antiAlias,
      child: Stack(
        children: [
          Positioned(
            right: -90,
            top: -100,
            child: Container(
              width: 360,
              height: 360,
              decoration: BoxDecoration(
                shape: BoxShape.circle,
                gradient: RadialGradient(
                  colors: [
                    ScoreTimeColors.blue.withValues(alpha: .28),
                    Colors.transparent,
                  ],
                ),
              ),
            ),
          ),
          Positioned(
            left: 30,
            bottom: -120,
            child: Container(
              width: 320,
              height: 250,
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(999),
                color: ScoreTimeColors.cyan.withValues(alpha: .05),
              ),
            ),
          ),
          Padding(
            padding: EdgeInsets.all(desktop ? 32 : 22),
            child: desktop
                ? Row(
                    children: [
                      Expanded(
                        flex: 11,
                        child: _HeroCopy(liveCount: liveCount, summary: summary),
                      ),
                      const SizedBox(width: 28),
                      Expanded(
                        flex: 9,
                        child: _FeaturedMatchGlass(match: featured),
                      ),
                    ],
                  )
                : Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      _HeroCopy(liveCount: liveCount, summary: summary),
                      const SizedBox(height: 22),
                      _FeaturedMatchGlass(match: featured),
                    ],
                  ),
          ),
        ],
      ),
    );
  }
}

class _HeroCopy extends StatelessWidget {
  final int liveCount;
  final Map<String, dynamic> summary;
  const _HeroCopy({required this.liveCount, required this.summary});

  @override
  Widget build(BuildContext context) {
    final desktop = MediaQuery.sizeOf(context).width >= 900;
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      crossAxisAlignment: CrossAxisAlignment.start,
      children: [
        Row(
          children: [
            const _LiveBadge(text: 'NEXT-GEN FOOTBALL'),
            const SizedBox(width: 8),
            Text(
              '•  GLOBAL  •  REAL-TIME',
              style: TextStyle(
                color: Theme.of(context).colorScheme.onSurfaceVariant,
                fontSize: 10,
                letterSpacing: 1.2,
                fontWeight: FontWeight.w800,
              ),
            ),
          ],
        ),
        const SizedBox(height: 18),
        Text(
          'Unlimited Football.\nReal-Time. Everywhere.',
          style: (desktop
                  ? Theme.of(context).textTheme.displayLarge
                  : Theme.of(context).textTheme.headlineLarge)
              ?.copyWith(height: 1.02),
        ),
        const SizedBox(height: 12),
        Text(
          'Live scores, deep match intelligence, trusted news, transfers, TV schedules and your personalized football world — in one premium experience.',
          style: Theme.of(context).textTheme.bodyLarge?.copyWith(
                height: 1.55,
                color: Theme.of(context).colorScheme.onSurfaceVariant,
              ),
        ),
        const SizedBox(height: 22),
        Wrap(
          spacing: 10,
          runSpacing: 10,
          children: [
            FilledButton.icon(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const MatchesScreen())),
              icon: const Icon(Icons.bolt_rounded),
              label: Text('Explore $liveCount live matches'),
            ),
            OutlinedButton.icon(
              onPressed: () => Navigator.push(
                context,
                MaterialPageRoute(builder: (_) => const GlobalFootballScreen()),
              ),
              icon: const Icon(Icons.public_rounded),
              label: const Text('Explore world football'),
            ),
          ],
        ),
        const SizedBox(height: 24),
        Wrap(
          spacing: 24,
          runSpacing: 12,
          children: [
            _HeroMetric(value: '${summary['competitions'] ?? '—'}', label: 'Competitions'),
            _HeroMetric(value: '${summary['teams'] ?? '—'}', label: 'Teams'),
            _HeroMetric(value: '${summary['players'] ?? '—'}', label: 'Players'),
            const _HeroMetric(value: '24/7', label: 'Match pulse'),
          ],
        ),
      ],
    );
  }
}

class _HeroMetric extends StatelessWidget {
  final String value;
  final String label;
  const _HeroMetric({required this.value, required this.label});

  @override
  Widget build(BuildContext context) {
    return Row(
      mainAxisSize: MainAxisSize.min,
      children: [
        Container(
          width: 6,
          height: 28,
          decoration: BoxDecoration(
            color: ScoreTimeColors.cyan,
            borderRadius: BorderRadius.circular(9),
            boxShadow: [
              BoxShadow(
                color: ScoreTimeColors.cyan.withValues(alpha: .45),
                blurRadius: 10,
              ),
            ],
          ),
        ),
        const SizedBox(width: 8),
        Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Text(value, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 16)),
            Text(
              label,
              style: TextStyle(
                color: Theme.of(context).colorScheme.onSurfaceVariant,
                fontSize: 10.5,
              ),
            ),
          ],
        ),
      ],
    );
  }
}

class _FeaturedMatchGlass extends StatelessWidget {
  final Map<String, dynamic> match;
  const _FeaturedMatchGlass({required this.match});

  @override
  Widget build(BuildContext context) {
    if (match.isEmpty) {
      return Container(
        constraints: const BoxConstraints(minHeight: 280),
        padding: const EdgeInsets.all(24),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(24),
          color: const Color(0xFF061225).withValues(alpha: .82),
          border: Border.all(color: Colors.white.withValues(alpha: .1)),
        ),
        child: const _EmptyState(
          icon: Icons.event_busy_rounded,
          message: 'No synchronized match is available yet.',
        ),
      );
    }
    final home = Map<String, dynamic>.from(match['home_team'] ?? match['homeTeam'] ?? {});
    final away = Map<String, dynamic>.from(match['away_team'] ?? match['awayTeam'] ?? {});
    final competition = Map<String, dynamic>.from(match['competition'] ?? {});
    final live = ['live', 'halftime'].contains('${match['status']}'.toLowerCase());

    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        color: const Color(0xFF061225).withValues(alpha: .82),
        border: Border.all(color: Colors.white.withValues(alpha: .1)),
      ),
      child: Column(
        children: [
          Row(
            children: [
              Expanded(
                child: Text(
                  '${competition['name_en'] ?? competition['name_ar'] ?? 'Featured match'}',
                  maxLines: 1,
                  overflow: TextOverflow.ellipsis,
                  style: const TextStyle(fontWeight: FontWeight.w800),
                ),
              ),
              _LiveBadge(text: live ? 'LIVE ${match['minute'] ?? ''}\'' : '${match['status'] ?? 'UPCOMING'}'),
            ],
          ),
          const SizedBox(height: 22),
          Row(
            children: [
              Expanded(child: _TeamLockup(team: home, align: CrossAxisAlignment.start)),
              Padding(
                padding: const EdgeInsets.symmetric(horizontal: 14),
                child: Column(
                  children: [
                    Text(
                      '${match['home_score'] ?? 0}  :  ${match['away_score'] ?? 0}',
                      style: const TextStyle(
                        fontSize: 36,
                        fontWeight: FontWeight.w900,
                        letterSpacing: -2,
                      ),
                    ),
                    const SizedBox(height: 4),
                    Text(
                      live ? '${match['minute'] ?? ''}\'' : '${match['venue'] ?? 'Match Center'}',
                      style: TextStyle(
                        color: Theme.of(context).colorScheme.onSurfaceVariant,
                        fontSize: 11,
                      ),
                    ),
                  ],
                ),
              ),
              Expanded(child: _TeamLockup(team: away, align: CrossAxisAlignment.end)),
            ],
          ),
          const SizedBox(height: 22),
          const _EmptyState(
            icon: Icons.analytics_outlined,
            message: 'Open Match Center for synchronized events and available analytics.',
          ),
        ],
      ),
    );
  }
}

class _TeamLockup extends StatelessWidget {
  final Map<String, dynamic> team;
  final CrossAxisAlignment align;
  const _TeamLockup({required this.team, required this.align});

  @override
  Widget build(BuildContext context) {
    final name = '${team['name_en'] ?? team['name_ar'] ?? 'Team'}';
    return Column(
      crossAxisAlignment: align,
      children: [
        _TeamBadge(name: name, size: 52),
        const SizedBox(height: 7),
        Text(
          name,
          maxLines: 2,
          textAlign: align == CrossAxisAlignment.end ? TextAlign.end : TextAlign.start,
          overflow: TextOverflow.ellipsis,
          style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w800),
        ),
      ],
    );
  }
}

class _CompetitionRail extends StatelessWidget {
  final List<dynamic> items;
  const _CompetitionRail({required this.items});

  @override
  Widget build(BuildContext context) {
    final source = items;
    if (source.isEmpty) {
      return const SizedBox(
        height: 90,
        child: Padding(
          padding: EdgeInsets.fromLTRB(16, 14, 16, 4),
          child: _EmptyState(icon: Icons.emoji_events_outlined, message: 'Competitions will appear after synchronization.'),
        ),
      );
    }
    return SizedBox(
      height: 108,
      child: ListView.separated(
        padding: const EdgeInsets.fromLTRB(16, 14, 16, 4),
        scrollDirection: Axis.horizontal,
        itemCount: math.min(source.length, 12),
        separatorBuilder: (_, __) => const SizedBox(width: 9),
        itemBuilder: (context, i) {
          final c = Map<String, dynamic>.from(source[i]);
          final name = '${c['name_en'] ?? c['name_ar'] ?? 'Competition'}';
          return Container(
            width: 138,
            padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 10),
            decoration: BoxDecoration(
              color: Theme.of(context).colorScheme.surface.withValues(alpha: .7),
              borderRadius: BorderRadius.circular(18),
              border: Border.all(color: Theme.of(context).dividerColor),
            ),
            child: Row(
              children: [
                Container(
                  width: 36,
                  height: 36,
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(12),
                    gradient: LinearGradient(
                      colors: [
                        ScoreTimeColors.blue.withValues(alpha: .34),
                        ScoreTimeColors.cyan.withValues(alpha: .10),
                      ],
                    ),
                  ),
                  child: const Icon(Icons.emoji_events_rounded, size: 18),
                ),
                const SizedBox(width: 9),
                Expanded(
                  child: Text(
                    name,
                    maxLines: 2,
                    overflow: TextOverflow.ellipsis,
                    style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w800),
                  ),
                ),
              ],
            ),
          );
        },
      ),
    );
  }
}

class _LiveDesk extends StatelessWidget {
  final bool loading;
  final List<dynamic> matches;
  const _LiveDesk({required this.loading, required this.matches});

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'Live Now',
      kicker: '${matches.length} matches in focus',
      trailing: const _LiveBadge(text: 'MATCH PULSE'),
      child: loading
          ? const SizedBox(height: 260, child: Center(child: CircularProgressIndicator()))
          : matches.isEmpty
              ? const SizedBox(height: 180, child: _EmptyState(icon: Icons.sports_soccer_outlined, message: 'No synchronized matches in this view.'))
              : Column(
              children: matches
                  .take(6)
                  .map((raw) => _MatchRow(match: Map<String, dynamic>.from(raw)))
                  .toList(),
            ),
    );
  }
}

class _MatchRow extends StatelessWidget {
  final Map<String, dynamic> match;
  const _MatchRow({required this.match});

  @override
  Widget build(BuildContext context) {
    final h = Map<String, dynamic>.from(match['home_team'] ?? match['homeTeam'] ?? {});
    final a = Map<String, dynamic>.from(match['away_team'] ?? match['awayTeam'] ?? {});
    final live = ['live', 'halftime'].contains('${match['status']}'.toLowerCase());
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 12),
      decoration: BoxDecoration(
        border: Border(bottom: BorderSide(color: Theme.of(context).dividerColor)),
      ),
      child: Row(
        children: [
          SizedBox(
            width: 38,
            child: Text(
              live ? '${match['minute'] ?? '•'}\'' : '${match['status'] ?? ''}',
              style: TextStyle(
                color: live ? ScoreTimeColors.green : Theme.of(context).colorScheme.onSurfaceVariant,
                fontSize: 10,
                fontWeight: FontWeight.w900,
              ),
            ),
          ),
          Expanded(
            child: Column(
              children: [
                _CompactTeam(team: h, score: '${match['home_score'] ?? 0}'),
                const SizedBox(height: 7),
                _CompactTeam(team: a, score: '${match['away_score'] ?? 0}'),
              ],
            ),
          ),
          const SizedBox(width: 8),
          Icon(Icons.chevron_right_rounded, color: Theme.of(context).colorScheme.onSurfaceVariant),
        ],
      ),
    );
  }
}

class _CompactTeam extends StatelessWidget {
  final Map<String, dynamic> team;
  final String score;
  const _CompactTeam({required this.team, required this.score});

  @override
  Widget build(BuildContext context) {
    final name = '${team['name_en'] ?? team['name_ar'] ?? 'Team'}';
    return Row(
      children: [
        _TeamBadge(name: name, size: 24),
        const SizedBox(width: 8),
        Expanded(
          child: Text(name, maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 12, fontWeight: FontWeight.w700)),
        ),
        Text(score, style: const TextStyle(fontWeight: FontWeight.w900, fontSize: 14)),
      ],
    );
  }
}

class _NewsDesk extends StatelessWidget {
  final List<dynamic> news;
  const _NewsDesk({required this.news});

  @override
  Widget build(BuildContext context) {
    final source = news;
    return _Panel(
      title: 'Top News',
      kicker: 'Editorial + breaking stories',
      child: source.isEmpty
        ? const SizedBox(height: 180, child: _EmptyState(icon: Icons.newspaper_outlined, message: 'No verified news is available yet.'))
        : Column(
        children: source.take(5).toList().asMap().entries.map((entry) {
          final n = Map<String, dynamic>.from(entry.value);
          final feature = entry.key == 0;
          return _NewsRow(news: n, feature: feature);
        }).toList(),
      ),
    );
  }
}

class _NewsRow extends StatelessWidget {
  final Map<String, dynamic> news;
  final bool feature;
  const _NewsRow({required this.news, required this.feature});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: EdgeInsets.only(bottom: feature ? 14 : 11, top: feature ? 0 : 11),
      decoration: BoxDecoration(
        border: Border(bottom: BorderSide(color: Theme.of(context).dividerColor)),
      ),
      child: Row(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Container(
            width: feature ? 96 : 62,
            height: feature ? 76 : 52,
            decoration: BoxDecoration(
              borderRadius: BorderRadius.circular(14),
              gradient: const LinearGradient(
                begin: Alignment.topLeft,
                end: Alignment.bottomRight,
                colors: [Color(0xFF123A70), Color(0xFF081B38)],
              ),
            ),
            child: Icon(
              news['is_breaking'] == true ? Icons.bolt_rounded : Icons.article_rounded,
              color: news['is_breaking'] == true ? ScoreTimeColors.gold : ScoreTimeColors.cyan,
            ),
          ),
          const SizedBox(width: 11),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(
                  '${news['category'] ?? 'Football'}'.toUpperCase(),
                  style: const TextStyle(
                    color: ScoreTimeColors.cyan,
                    fontSize: 8.5,
                    letterSpacing: .8,
                    fontWeight: FontWeight.w900,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  '${news['title'] ?? ''}',
                  maxLines: feature ? 3 : 2,
                  overflow: TextOverflow.ellipsis,
                  style: TextStyle(
                    fontSize: feature ? 14 : 11.5,
                    height: 1.25,
                    fontWeight: FontWeight.w800,
                  ),
                ),
                const SizedBox(height: 4),
                Text(
                  '${news['author_name'] ?? 'ScoreTime'} • now',
                  style: TextStyle(color: Theme.of(context).colorScheme.onSurfaceVariant, fontSize: 9),
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}

class _PlayerDesk extends StatelessWidget {
  final List<dynamic> players;
  const _PlayerDesk({required this.players});

  @override
  Widget build(BuildContext context) {
    final source = players;
    return _Panel(
      title: 'Trending Players',
      kicker: 'Performance radar',
      child: source.isEmpty
        ? const SizedBox(height: 180, child: _EmptyState(icon: Icons.person_search_outlined, message: 'Player rankings need synchronized statistics.'))
        : Column(
        children: source.take(6).toList().asMap().entries.map((entry) {
          final p = Map<String, dynamic>.from(entry.value);
          return _PlayerRank(rank: entry.key + 1, player: p);
        }).toList(),
      ),
    );
  }
}

class _PlayerRank extends StatelessWidget {
  final int rank;
  final Map<String, dynamic> player;
  const _PlayerRank({required this.rank, required this.player});

  @override
  Widget build(BuildContext context) {
    final team = Map<String, dynamic>.from(player['team'] ?? {});
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 11),
      decoration: BoxDecoration(
        border: Border(bottom: BorderSide(color: Theme.of(context).dividerColor)),
      ),
      child: Row(
        children: [
          SizedBox(
            width: 26,
            child: Text('$rank', style: const TextStyle(fontWeight: FontWeight.w900)),
          ),
          CircleAvatar(
            radius: 17,
            backgroundColor: ScoreTimeColors.panel2,
            child: Text(
              _initials('${player['name'] ?? 'P'}'),
              style: const TextStyle(fontSize: 9, fontWeight: FontWeight.w900),
            ),
          ),
          const SizedBox(width: 8),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text('${player['name'] ?? 'Player'}', maxLines: 1, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 11.5, fontWeight: FontWeight.w800)),
                Text('${team['name_en'] ?? player['nationality'] ?? ''}', maxLines: 1, overflow: TextOverflow.ellipsis, style: TextStyle(fontSize: 8.5, color: Theme.of(context).colorScheme.onSurfaceVariant)),
              ],
            ),
          ),
          Container(
            padding: const EdgeInsets.symmetric(horizontal: 8, vertical: 5),
            decoration: BoxDecoration(
              color: ScoreTimeColors.green.withValues(alpha: .13),
              borderRadius: BorderRadius.circular(10),
            ),
            child: Text(
              '${player['rating'] ?? '—'}',
              style: const TextStyle(color: ScoreTimeColors.green, fontSize: 10, fontWeight: FontWeight.w900),
            ),
          ),
        ],
      ),
    );
  }
}

class _Panel extends StatelessWidget {
  final String title;
  final String kicker;
  final Widget child;
  final Widget? trailing;
  const _Panel({required this.title, required this.kicker, required this.child, this.trailing});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        color: Theme.of(context).colorScheme.surface.withValues(alpha: .73),
        border: Border.all(color: Theme.of(context).dividerColor),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        children: [
          Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(title, style: const TextStyle(fontSize: 15, fontWeight: FontWeight.w900)),
                    const SizedBox(height: 2),
                    Text(kicker, style: TextStyle(color: Theme.of(context).colorScheme.onSurfaceVariant, fontSize: 9.5)),
                  ],
                ),
              ),
              if (trailing != null) trailing!,
            ],
          ),
          const SizedBox(height: 13),
          child,
        ],
      ),
    );
  }
}

class _SectionHeading extends StatelessWidget {
  final String kicker;
  final String title;
  final String action;
  const _SectionHeading({required this.kicker, required this.title, required this.action});

  @override
  Widget build(BuildContext context) {
    return Row(
      crossAxisAlignment: CrossAxisAlignment.end,
      children: [
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(kicker, style: const TextStyle(color: ScoreTimeColors.cyan, fontSize: 9, letterSpacing: 1.15, fontWeight: FontWeight.w900)),
              const SizedBox(height: 5),
              Text(title, style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900)),
            ],
          ),
        ),
        Text(action, style: const TextStyle(color: ScoreTimeColors.cyan, fontSize: 12, fontWeight: FontWeight.w800)),
      ],
    );
  }
}

class _TacticalPitchCard extends StatelessWidget {
  const _TacticalPitchCard();

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'Tactical Lens',
      kicker: 'Heatmap • pressure zones • shot locations',
      child: AspectRatio(
        aspectRatio: 1.65,
        child: ClipRRect(
          borderRadius: BorderRadius.circular(16),
          child: CustomPaint(painter: _PitchPainter()),
        ),
      ),
    );
  }
}

class _MomentumCard extends StatelessWidget {
  const _MomentumCard();

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'Match Momentum',
      kicker: 'Live territorial pressure',
      child: SizedBox(
        height: 190,
        child: CustomPaint(painter: _MomentumPainter()),
      ),
    );
  }
}

class _RadarCard extends StatelessWidget {
  const _RadarCard();

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'Player Radar',
      kicker: 'Attribute overview',
      child: SizedBox(
        height: 190,
        child: CustomPaint(painter: _RadarPainter()),
      ),
    );
  }
}

class _FollowCard extends StatelessWidget {
  const _FollowCard();

  @override
  Widget build(BuildContext context) {
    const teams = ['Real', 'Barça', 'City', 'Liverpool', 'PSG', 'Milan'];
    return _Panel(
      title: 'Follow Your Teams',
      kicker: 'Personalized alerts and stories',
      child: Wrap(
        spacing: 8,
        runSpacing: 8,
        children: teams.map((t) => _TeamBadge(name: t, size: 38)).toList(),
      ),
    );
  }
}

class _TVGuideCard extends StatelessWidget {
  const _TVGuideCard();

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'TV Guide',
      kicker: 'Where to watch',
      child: const _EmptyState(icon: Icons.live_tv_outlined, message: 'Broadcast information appears only when supplied by the data source.'),
    );
  }
}

class _TransferPulseCard extends StatelessWidget {
  final List<dynamic> items;
  const _TransferPulseCard({required this.items});

  @override
  Widget build(BuildContext context) {
    final source = items;
    return _Panel(
      title: 'Transfer Pulse',
      kicker: 'Confidence + movement',
      child: source.isEmpty
        ? const _EmptyState(icon: Icons.sync_alt_rounded, message: 'No verified transfer updates are available.')
        : Column(
        children: source.take(3).map((raw) {
          final t = Map<String, dynamic>.from(raw);
          return Padding(
            padding: const EdgeInsets.symmetric(vertical: 7),
            child: Row(
              children: [
                const Icon(Icons.sync_alt_rounded, color: ScoreTimeColors.cyan, size: 18),
                const SizedBox(width: 8),
                Expanded(child: Text('${t['headline'] ?? 'Market movement'}', maxLines: 2, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700))),
                const SizedBox(width: 6),
                if (t['confidence'] != null)
                  Text('${t['confidence']}%', style: const TextStyle(color: ScoreTimeColors.gold, fontSize: 9.5, fontWeight: FontWeight.w900)),
              ],
            ),
          );
        }).toList(),
      ),
    );
  }
}

class _FanZoneCard extends StatelessWidget {
  const _FanZoneCard();

  @override
  Widget build(BuildContext context) {
    return _Panel(
      title: 'Fan Zone',
      kicker: 'Predictions • friends • XP',
      child: Column(
        children: [
          const Row(
            children: [
              Expanded(child: _MiniStat(value: '—', label: 'Picks')),
              SizedBox(width: 8),
              Expanded(child: _MiniStat(value: '—', label: 'Form')),
              SizedBox(width: 8),
              Expanded(child: _MiniStat(value: '—', label: 'XP')),
            ],
          ),
          const SizedBox(height: 12),
          SizedBox(
            width: double.infinity,
            child: OutlinedButton.icon(
              onPressed: () => Navigator.push(context, MaterialPageRoute(builder: (_) => const WorldClassScreen())),
              icon: const Icon(Icons.groups_rounded),
              label: const Text('Open fan experience'),
            ),
          ),
        ],
      ),
    );
  }
}

class _MiniStat extends StatelessWidget {
  final String value;
  final String label;
  const _MiniStat({required this.value, required this.label});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(vertical: 9),
      decoration: BoxDecoration(
        color: Colors.white.withValues(alpha: .035),
        borderRadius: BorderRadius.circular(13),
      ),
      child: Column(
        children: [
          Text(value, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.w900)),
          Text(label, style: TextStyle(fontSize: 8.5, color: Theme.of(context).colorScheme.onSurfaceVariant)),
        ],
      ),
    );
  }
}

class _FeatureRibbon extends StatelessWidget {
  final Map<String, dynamic> summary;
  const _FeatureRibbon({required this.summary});

  @override
  Widget build(BuildContext context) {
    final items = [
      (Icons.bolt_rounded, 'Real-Time Data', 'Fast scores & key events'),
      (Icons.query_stats_rounded, 'Deep Stats', 'xG-ready analytics layer'),
      (Icons.radar_rounded, 'Tactical Insights', 'Heatmaps & momentum'),
      (Icons.tune_rounded, 'Personalized', 'Teams, players & alerts'),
      (Icons.public_rounded, 'Global Coverage', '${summary['competitions'] ?? '—'} competitions'),
      (Icons.verified_user_rounded, 'Secure & Reliable', 'Hardened Laravel backend'),
    ];
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(22),
        color: Theme.of(context).colorScheme.surface.withValues(alpha: .7),
        border: Border.all(color: Theme.of(context).dividerColor),
      ),
      child: LayoutBuilder(
        builder: (context, constraints) => Wrap(
          spacing: 10,
          runSpacing: 10,
          children: items
              .map(
                (x) => SizedBox(
                  width: constraints.maxWidth >= 1000
                      ? (constraints.maxWidth - 50) / 6
                      : constraints.maxWidth >= 600
                          ? (constraints.maxWidth - 20) / 3
                          : (constraints.maxWidth - 10) / 2,
                  child: Row(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        width: 36,
                        height: 36,
                        decoration: BoxDecoration(
                          color: ScoreTimeColors.blue.withValues(alpha: .12),
                          borderRadius: BorderRadius.circular(12),
                        ),
                        child: Icon(x.$1, color: ScoreTimeColors.cyan, size: 18),
                      ),
                      const SizedBox(width: 8),
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            Text(x.$2, style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w900)),
                            const SizedBox(height: 2),
                            Text(x.$3, style: TextStyle(fontSize: 8.5, height: 1.3, color: Theme.of(context).colorScheme.onSurfaceVariant)),
                          ],
                        ),
                      ),
                    ],
                  ),
                ),
              )
              .toList(),
        ),
      ),
    );
  }
}

class _StatusStrip extends StatelessWidget {
  final String message;
  const _StatusStrip({required this.message});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 13, vertical: 10),
      decoration: BoxDecoration(
        color: ScoreTimeColors.gold.withValues(alpha: .08),
        borderRadius: BorderRadius.circular(14),
        border: Border.all(color: ScoreTimeColors.gold.withValues(alpha: .18)),
      ),
      child: Row(
        children: [
          const Icon(Icons.cloud_sync_rounded, color: ScoreTimeColors.gold, size: 17),
          const SizedBox(width: 8),
          Expanded(child: Text(message, style: const TextStyle(fontSize: 10.5, fontWeight: FontWeight.w700))),
        ],
      ),
    );
  }
}

class _EmptyState extends StatelessWidget {
  final IconData icon;
  final String message;
  const _EmptyState({required this.icon, required this.message});

  @override
  Widget build(BuildContext context) => Center(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              Icon(icon, size: 30, color: Theme.of(context).colorScheme.secondary),
              const SizedBox(height: 9),
              Text(
                message,
                textAlign: TextAlign.center,
                style: TextStyle(
                  fontSize: 10.5,
                  color: Theme.of(context).colorScheme.onSurfaceVariant,
                ),
              ),
            ],
          ),
        ),
      );
}

class _LiveBadge extends StatelessWidget {
  final String text;
  const _LiveBadge({required this.text});

  @override
  Widget build(BuildContext context) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 9, vertical: 6),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(999),
        color: ScoreTimeColors.blue.withValues(alpha: .13),
        border: Border.all(color: ScoreTimeColors.cyan.withValues(alpha: .22)),
      ),
      child: Text(
        text,
        style: const TextStyle(
          color: ScoreTimeColors.cyan,
          fontSize: 8.5,
          letterSpacing: .6,
          fontWeight: FontWeight.w900,
        ),
      ),
    );
  }
}

class _TeamBadge extends StatelessWidget {
  final String name;
  final double size;
  const _TeamBadge({required this.name, required this.size});

  @override
  Widget build(BuildContext context) {
    return Container(
      width: size,
      height: size,
      decoration: BoxDecoration(
        shape: BoxShape.circle,
        gradient: LinearGradient(
          colors: [
            ScoreTimeColors.blue.withValues(alpha: .55),
            ScoreTimeColors.cyan.withValues(alpha: .22),
          ],
        ),
        border: Border.all(color: Colors.white.withValues(alpha: .12)),
      ),
      child: Center(
        child: Text(
          _initials(name),
          style: TextStyle(fontSize: size * .25, fontWeight: FontWeight.w900),
        ),
      ),
    );
  }
}

String _initials(String name) {
  final parts = name.trim().split(RegExp(r'\s+')).where((e) => e.isNotEmpty).toList();
  if (parts.isEmpty) return 'ST';
  if (parts.length == 1) return parts.first.substring(0, math.min(2, parts.first.length)).toUpperCase();
  return '${parts.first[0]}${parts.last[0]}'.toUpperCase();
}

class _PitchPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final bg = Paint()..color = const Color(0xFF0B5B34);
    canvas.drawRect(Offset.zero & size, bg);
    final stripe = Paint()..color = Colors.white.withValues(alpha: .035);
    for (var i = 0; i < 8; i += 2) {
      canvas.drawRect(Rect.fromLTWH(i * size.width / 8, 0, size.width / 8, size.height), stripe);
    }
    final line = Paint()
      ..color = Colors.white.withValues(alpha: .35)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1.2;
    final inset = Rect.fromLTWH(12, 10, size.width - 24, size.height - 20);
    canvas.drawRect(inset, line);
    canvas.drawLine(Offset(size.width / 2, 10), Offset(size.width / 2, size.height - 10), line);
    canvas.drawCircle(Offset(size.width / 2, size.height / 2), size.height * .16, line);
    canvas.drawRect(Rect.fromLTWH(12, size.height * .28, size.width * .16, size.height * .44), line);
    canvas.drawRect(Rect.fromLTWH(size.width - 12 - size.width * .16, size.height * .28, size.width * .16, size.height * .44), line);
    final zones = [
      (const Offset(.37, .44), .16, ScoreTimeColors.gold),
      (const Offset(.55, .56), .20, ScoreTimeColors.red),
      (const Offset(.70, .38), .13, ScoreTimeColors.cyan),
    ];
    for (final z in zones) {
      final center = Offset(size.width * z.$1.dx, size.height * z.$1.dy);
      final radius = size.shortestSide * z.$2;
      final shader = RadialGradient(
        colors: [z.$3.withValues(alpha: .58), z.$3.withValues(alpha: 0)],
      ).createShader(Rect.fromCircle(center: center, radius: radius));
      canvas.drawCircle(center, radius, Paint()..shader = shader);
    }
    final dots = [const Offset(.58,.46), const Offset(.72,.62), const Offset(.81,.35), const Offset(.48,.66)];
    for (var i = 0; i < dots.length; i++) {
      final o = Offset(size.width * dots[i].dx, size.height * dots[i].dy);
      canvas.drawCircle(o, 5, Paint()..color = i == 2 ? ScoreTimeColors.gold : ScoreTimeColors.cyan);
      canvas.drawCircle(o, 7, Paint()..color = Colors.white.withValues(alpha: .65)..style = PaintingStyle.stroke..strokeWidth=1.2);
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class _MomentumPainter extends CustomPainter {
  final bool compact;
  const _MomentumPainter({this.compact = false});

  @override
  void paint(Canvas canvas, Size size) {
    final grid = Paint()..color = Colors.white.withValues(alpha: .055)..strokeWidth = 1;
    for (var i = 1; i < 5; i++) {
      final y = size.height * i / 5;
      canvas.drawLine(Offset(0, y), Offset(size.width, y), grid);
    }
    const values = [.22,.31,.26,.48,.40,.58,.73,.55,.49,.68,.82,.61,.88,.76,.64,.83,.72,.92,.69,.79];
    final path = Path();
    for (var i = 0; i < values.length; i++) {
      final x = size.width * i / (values.length - 1);
      final y = size.height * (1 - values[i]) * .82 + size.height * .08;
      if (i == 0) path.moveTo(x, y); else path.lineTo(x, y);
    }
    final glow = Paint()
      ..color = ScoreTimeColors.blue.withValues(alpha: .24)
      ..style = PaintingStyle.stroke
      ..strokeWidth = compact ? 7 : 10
      ..maskFilter = const MaskFilter.blur(BlurStyle.normal, 8);
    canvas.drawPath(path, glow);
    canvas.drawPath(
      path,
      Paint()
        ..shader = const LinearGradient(colors: [ScoreTimeColors.violet, ScoreTimeColors.cyan]).createShader(Offset.zero & size)
        ..style = PaintingStyle.stroke
        ..strokeWidth = compact ? 1.7 : 2.2,
    );
    if (!compact) {
      final fill = Path.from(path)
        ..lineTo(size.width, size.height)
        ..lineTo(0, size.height)
        ..close();
      canvas.drawPath(
        fill,
        Paint()
          ..shader = LinearGradient(
            begin: Alignment.topCenter,
            end: Alignment.bottomCenter,
            colors: [ScoreTimeColors.blue.withValues(alpha: .17), Colors.transparent],
          ).createShader(Offset.zero & size),
      );
    }
  }

  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate) => false;
}

class _RadarPainter extends CustomPainter {
  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(size.width / 2, size.height / 2 + 4);
    final radius = math.min(size.width, size.height) * .38;
    const sides = 6;
    final grid = Paint()
      ..color = Colors.white.withValues(alpha: .12)
      ..style = PaintingStyle.stroke
      ..strokeWidth = 1;
    for (final factor in [.35,.65,1.0]) {
      final path = Path();
      for (var i=0;i<sides;i++) {
        final angle = -math.pi/2 + i*2*math.pi/sides;
        final point = center + Offset(math.cos(angle), math.sin(angle))*radius*factor;
        if (i==0) path.moveTo(point.dx, point.dy); else path.lineTo(point.dx, point.dy);
      }
      path.close();
      canvas.drawPath(path, grid);
    }
    const values=[.92,.78,.86,.70,.81,.89];
    final area=Path();
    for (var i=0;i<sides;i++) {
      final angle=-math.pi/2+i*2*math.pi/sides;
      final point=center+Offset(math.cos(angle),math.sin(angle))*radius*values[i];
      if(i==0) area.moveTo(point.dx,point.dy); else area.lineTo(point.dx,point.dy);
    }
    area.close();
    canvas.drawPath(area, Paint()..color=ScoreTimeColors.blue.withValues(alpha:.22));
    canvas.drawPath(area, Paint()..color=ScoreTimeColors.cyan..style=PaintingStyle.stroke..strokeWidth=1.7);
  }
  @override
  bool shouldRepaint(covariant CustomPainter oldDelegate)=>false;
}
