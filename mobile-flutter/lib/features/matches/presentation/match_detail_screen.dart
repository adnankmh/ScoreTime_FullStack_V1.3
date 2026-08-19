import 'dart:async';

import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../../../core/i18n/app_strings.dart';
import '../../../core/network/football_repository.dart';
import '../../realtime/presentation/realtime_match_screen.dart';
import 'visual_match_widgets.dart';

class MatchDetailScreen extends StatefulWidget {
  const MatchDetailScreen({super.key, required this.matchId});
  final int matchId;

  @override
  State<MatchDetailScreen> createState() => _MatchDetailScreenState();
}

class _MatchDetailScreenState extends State<MatchDetailScreen> {
  final repo = FootballRepository();
  Map<String, dynamic>? data;
  String? error;
  Timer? timer;
  bool fetching = false;

  @override
  void initState() {
    super.initState();
    load();
  }

  @override
  void dispose() {
    timer?.cancel();
    super.dispose();
  }

  Future<void> load() async {
    if (fetching) return;
    fetching = true;
    try {
      final next = await repo.intelligence(widget.matchId);
      if (!mounted) return;
      setState(() {
        data = next;
        error = null;
      });
      final seconds = ((next['refresh_seconds'] ?? 15) as num).toInt().clamp(10, 120).toInt();
      timer?.cancel();
      final status = '${next['match']?['status'] ?? ''}'.toLowerCase();
      if (['live', 'halftime'].contains(status)) {
        timer = Timer.periodic(Duration(seconds: seconds), (_) => load());
      }
    } catch (_) {
      if (mounted) setState(() => error = 'live_data_unavailable');
    } finally {
      fetching = false;
    }
  }

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    if (data == null) {
      return Scaffold(
        appBar: AppBar(title: Text(t('match_center'))),
        body: Center(
          child: error == null
              ? const CircularProgressIndicator()
              : Column(
                  mainAxisSize: MainAxisSize.min,
                  children: [
                    const Icon(Icons.cloud_off_rounded, size: 48),
                    const SizedBox(height: 12),
                    Text(t(error!)),
                    TextButton(onPressed: load, child: Text(t('retry'))),
                  ],
                ),
        ),
      );
    }

    final current = data!;
    final match = Map<String, dynamic>.from(current['match'] ?? {});
    final home = Map<String, dynamic>.from(match['home_team'] ?? {});
    final away = Map<String, dynamic>.from(match['away_team'] ?? {});
    final probability = Map<String, dynamic>.from(current['win_probability'] ?? {});
    final lineups = Map<String, dynamic>.from(current['lineups'] ?? {});
    final shots = List<dynamic>.from(current['shot_map'] ?? []);
    final momentum = List<dynamic>.from(current['momentum'] ?? []);
    final statistics = List<dynamic>.from(current['statistics'] ?? []);
    final matchEvents = List<dynamic>.from(match['match_events'] ?? []);
    final commentaries = List<dynamic>.from(match['commentaries'] ?? []);
    final events = matchEvents.isNotEmpty ? matchEvents : commentaries;
    final entries = <dynamic>[...lineups.values.expand((value) => List<dynamic>.from(value))];

    return Scaffold(
      appBar: AppBar(
        title: Text(t('match_center')),
        actions: [IconButton(onPressed: load, tooltip: t('refresh'), icon: const Icon(Icons.refresh_rounded))],
      ),
      body: RefreshIndicator(
        onRefresh: load,
        child: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            if (error != null)
              Card(
                color: Theme.of(context).colorScheme.errorContainer,
                child: Padding(padding: const EdgeInsets.all(12), child: Text(t(error!))),
              ),
            _Score(match, home, away),
            _Section(
              t('predictions'),
              Column(
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.spaceAround,
                    children: [
                      _Probability(t('home'), probability['home']),
                      _Probability(t('draw'), probability['draw']),
                      _Probability(t('away'), probability['away']),
                    ],
                  ),
                  const SizedBox(height: 9),
                  Text(
                    probability['basis'] == 'scoretime_recent_form_model'
                        ? t('model_probability_notice')
                        : t('insufficient_analytics'),
                    textAlign: TextAlign.center,
                    style: Theme.of(context).textTheme.bodySmall,
                  ),
                ],
              ),
            ),
            _Section(
              t('lineups'),
              entries.isEmpty ? _Unavailable(t('no_lineups')) : TacticalPitch(entries: entries),
            ),
            _Section(
              t('shot_map'),
              shots.isEmpty ? _Unavailable(t('no_shot_data')) : ShotMap(shots: shots),
            ),
            _Section(
              t('momentum'),
              momentum.isEmpty ? _Unavailable(t('no_momentum_data')) : MomentumChart(points: momentum),
            ),
            _Section(
              t('statistics'),
              statistics.isEmpty ? _Unavailable(t('insufficient_analytics')) : _Statistics(groups: statistics),
            ),
            _Section(
              t('events'),
              events.isEmpty
                  ? _Unavailable(t('no_events'))
                  : Column(children: events.map((event) => _EventTile(event: Map<String, dynamic>.from(event))).toList()),
            ),
            _Section(
              t('form'),
              Column(
                children: [
                  _Form(t('home'), List<dynamic>.from(current['home_form'] ?? [])),
                  _Form(t('away'), List<dynamic>.from(current['away_form'] ?? [])),
                ],
              ),
            ),
            _Section(
              t('match_intelligence'),
              Text(
                'xG ${match['home_xg'] ?? '—'} : ${match['away_xg'] ?? '—'}\n'
                '${t('referee')}: ${match['referee'] ?? '—'}\n'
                '${t('venue')}: ${match['venue'] ?? '—'}\n'
                '${t('attendance')}: ${match['attendance'] ?? '—'}\n'
                '${t('last_updated')}: ${current['last_synced_at'] ?? '—'}',
              ),
            ),
            Row(
              children: [
                Expanded(
                  child: FilledButton.icon(
                    onPressed: () => Navigator.push(
                      context,
                      MaterialPageRoute(builder: (_) => RealtimeMatchScreen(matchId: widget.matchId)),
                    ),
                    icon: const Icon(Icons.bolt_rounded),
                    label: Text(t('open_live_center')),
                  ),
                ),
                const SizedBox(width: 8),
                Expanded(
                  child: OutlinedButton.icon(
                    onPressed: () async {
                      try {
                        await repo.subscribeMatch(widget.matchId);
                        if (context.mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(t('alerts_enabled'))));
                        }
                      } catch (_) {
                        if (context.mounted) {
                          ScaffoldMessenger.of(context).showSnackBar(SnackBar(content: Text(t('login_for_alerts'))));
                        }
                      }
                    },
                    icon: const Icon(Icons.notifications_active_outlined),
                    label: Text(t('alerts')),
                  ),
                ),
              ],
            ),
          ],
        ),
      ),
    );
  }
}

class _Score extends StatelessWidget {
  const _Score(this.match, this.home, this.away);
  final Map<String, dynamic> match;
  final Map<String, dynamic> home;
  final Map<String, dynamic> away;

  @override
  Widget build(BuildContext context) {
    final status = '${match['status'] ?? ''}';
    final minute = match['minute'];
    final scheduled = status == 'scheduled';
    final kickoff = DateTime.tryParse('${match['kickoff_at'] ?? ''}')?.toLocal();
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(20),
        child: Column(
          children: [
            Text('${match['competition']?['name_en'] ?? match['competition']?['name_ar'] ?? ''}', style: Theme.of(context).textTheme.labelLarge),
            const SizedBox(height: 16),
            Row(
              children: [
                Expanded(child: _Team(team: home)),
                Text(
                  scheduled && kickoff != null
                      ? TimeOfDay.fromDateTime(kickoff).format(context)
                      : '${match['home_score'] ?? 0}  :  ${match['away_score'] ?? 0}',
                  style: Theme.of(context).textTheme.headlineLarge?.copyWith(fontWeight: FontWeight.w900),
                ),
                Expanded(child: _Team(team: away)),
              ],
            ),
            const SizedBox(height: 10),
            Text(minute == null ? status : "$status • $minute'"),
          ],
        ),
      ),
    );
  }
}

class _Team extends StatelessWidget {
  const _Team({required this.team});
  final Map<String, dynamic> team;

  @override
  Widget build(BuildContext context) {
    final name = '${team['name_en'] ?? team['name_ar'] ?? 'Team'}';
    final logo = '${team['logo_url'] ?? ''}';
    return Column(
      children: [
        SizedBox(
          width: 56,
          height: 56,
          child: logo.isEmpty
              ? CircleAvatar(child: Text(_initials(name)))
              : CachedNetworkImage(
                  imageUrl: logo,
                  fit: BoxFit.contain,
                  errorWidget: (_, __, ___) => CircleAvatar(child: Text(_initials(name))),
                ),
        ),
        const SizedBox(height: 8),
        Text(name, textAlign: TextAlign.center, style: const TextStyle(fontWeight: FontWeight.w800)),
      ],
    );
  }
}

class _Probability extends StatelessWidget {
  const _Probability(this.title, this.value);
  final String title;
  final dynamic value;

  @override
  Widget build(BuildContext context) => Column(
        children: [
          Text(value == null ? '—' : '$value%', style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900)),
          Text(title),
        ],
      );
}

class _Form extends StatelessWidget {
  const _Form(this.title, this.form);
  final String title;
  final List<dynamic> form;

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 6),
        child: Row(
          children: [
            SizedBox(width: 64, child: Text(title)),
            if (form.isEmpty) const Text('—'),
            ...form.map(
              (item) => Padding(
                padding: const EdgeInsetsDirectional.only(end: 5),
                child: CircleAvatar(
                  radius: 14,
                  child: Text('${item['result'] ?? '-'}', style: const TextStyle(fontSize: 11, fontWeight: FontWeight.bold)),
                ),
              ),
            ),
          ],
        ),
      );
}

class _EventTile extends StatelessWidget {
  const _EventTile({required this.event});
  final Map<String, dynamic> event;

  @override
  Widget build(BuildContext context) {
    final title = '${event['text'] ?? event['detail'] ?? event['type'] ?? ''}';
    final player = '${event['player']?['name'] ?? ''}';
    return ListTile(
      dense: true,
      leading: CircleAvatar(child: Text('${event['minute'] ?? ''}')),
      title: Text(title),
      subtitle: player.isEmpty ? null : Text(player),
    );
  }
}

class _Statistics extends StatelessWidget {
  const _Statistics({required this.groups});
  final List<dynamic> groups;

  @override
  Widget build(BuildContext context) => Column(
        children: groups.map((raw) {
          final group = Map<String, dynamic>.from(raw as Map);
          final team = Map<String, dynamic>.from(group['team'] ?? {});
          final rows = List<dynamic>.from(group['statistics'] ?? []);
          return ExpansionTile(
            initiallyExpanded: groups.length <= 2,
            title: Text('${team['name'] ?? 'Team'}', style: const TextStyle(fontWeight: FontWeight.w800)),
            children: rows.map((rawRow) {
              final row = Map<String, dynamic>.from(rawRow as Map);
              return ListTile(
                dense: true,
                title: Text('${row['type'] ?? ''}'),
                trailing: Text('${row['value'] ?? '—'}', style: const TextStyle(fontWeight: FontWeight.w900)),
              );
            }).toList(),
          );
        }).toList(),
      );
}

class _Unavailable extends StatelessWidget {
  const _Unavailable(this.message);
  final String message;

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsets.symmetric(vertical: 18),
        child: Center(child: Text(message, textAlign: TextAlign.center, style: Theme.of(context).textTheme.bodySmall)),
      );
}

class _Section extends StatelessWidget {
  const _Section(this.title, this.child);
  final String title;
  final Widget child;

  @override
  Widget build(BuildContext context) => Card(
        child: Padding(
          padding: const EdgeInsets.all(16),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text(title, style: Theme.of(context).textTheme.titleLarge?.copyWith(fontWeight: FontWeight.w900)),
              const SizedBox(height: 14),
              child,
            ],
          ),
        ),
      );
}

String _initials(String name) {
  final parts = name.trim().split(RegExp(r'\s+')).where((part) => part.isNotEmpty).toList();
  if (parts.isEmpty) return 'ST';
  if (parts.length == 1) {
    return parts.first.substring(0, parts.first.length.clamp(1, 2).toInt()).toUpperCase();
  }
  return '${parts.first[0]}${parts.last[0]}'.toUpperCase();
}
