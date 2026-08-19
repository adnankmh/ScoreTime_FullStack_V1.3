import 'package:cached_network_image/cached_network_image.dart';
import 'package:flutter/material.dart';

import '../../../core/i18n/app_strings.dart';
import '../../../core/network/football_repository.dart';
import 'match_detail_screen.dart';

class MatchesScreen extends StatefulWidget {
  const MatchesScreen({super.key});

  @override
  State<MatchesScreen> createState() => _MatchesScreenState();
}

class _MatchesScreenState extends State<MatchesScreen> {
  late Future<List<dynamic>> future;
  DateTime selectedDate = DateUtils.dateOnly(DateTime.now());
  String filter = 'all';

  String get dateParameter => selectedDate.toIso8601String().substring(0, 10);

  @override
  void initState() {
    super.initState();
    future = FootballRepository().matches(dateParameter);
  }

  Future<void> reload() async {
    setState(() => future = FootballRepository().matches(dateParameter));
    await future;
  }

  void changeDay(int days) {
    selectedDate = DateUtils.dateOnly(selectedDate.add(Duration(days: days)));
    reload();
  }

  Future<void> chooseDate() async {
    final picked = await showDatePicker(
      context: context,
      initialDate: selectedDate,
      firstDate: DateTime(2000),
      lastDate: DateTime.now().add(const Duration(days: 730)),
    );
    if (picked == null) return;
    selectedDate = DateUtils.dateOnly(picked);
    await reload();
  }

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    final isToday = DateUtils.isSameDay(selectedDate, DateTime.now());
    return Scaffold(
      body: SafeArea(
        child: Column(
          children: [
            Padding(
              padding: const EdgeInsets.fromLTRB(16, 12, 8, 8),
              child: Row(
                children: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(13),
                    child: Image.asset('assets/icons/scoretime_icon.png', width: 42, height: 42),
                  ),
                  const SizedBox(width: 10),
                  Expanded(
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Text(t('matches'), style: Theme.of(context).textTheme.titleLarge),
                        Text(t('verified_live_center'), style: Theme.of(context).textTheme.bodySmall),
                      ],
                    ),
                  ),
                  IconButton(onPressed: reload, tooltip: t('refresh'), icon: const Icon(Icons.refresh_rounded)),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
              child: Row(
                children: [
                  IconButton.filledTonal(onPressed: () => changeDay(-1), icon: const Icon(Icons.chevron_left_rounded)),
                  const SizedBox(width: 8),
                  Expanded(
                    child: OutlinedButton.icon(
                      onPressed: chooseDate,
                      icon: const Icon(Icons.calendar_month_rounded),
                      label: Text(isToday ? t('today') : MaterialLocalizations.of(context).formatMediumDate(selectedDate)),
                    ),
                  ),
                  const SizedBox(width: 8),
                  IconButton.filledTonal(onPressed: () => changeDay(1), icon: const Icon(Icons.chevron_right_rounded)),
                ],
              ),
            ),
            SizedBox(
              height: 52,
              child: ListView(
                scrollDirection: Axis.horizontal,
                padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 4),
                children: [
                  _FilterChip(label: t('all'), selected: filter == 'all', onTap: () => setState(() => filter = 'all')),
                  _FilterChip(label: t('live'), selected: filter == 'live', onTap: () => setState(() => filter = 'live')),
                  _FilterChip(label: t('upcoming'), selected: filter == 'upcoming', onTap: () => setState(() => filter = 'upcoming')),
                  _FilterChip(label: t('completed'), selected: filter == 'completed', onTap: () => setState(() => filter = 'completed')),
                ],
              ),
            ),
            Expanded(
              child: RefreshIndicator(
                onRefresh: reload,
                child: FutureBuilder<List<dynamic>>(
                  future: future,
                  builder: (context, snapshot) {
                    if (snapshot.connectionState == ConnectionState.waiting) {
                      return const Center(child: CircularProgressIndicator());
                    }
                    if (snapshot.hasError) {
                      return _ScrollableMessage(
                        icon: Icons.cloud_off_rounded,
                        message: t('live_data_unavailable'),
                        action: t('retry'),
                        onPressed: reload,
                      );
                    }
                    final all = snapshot.data ?? [];
                    final list = all.where((raw) {
                      final status = '${raw['status']}'.toLowerCase();
                      return switch (filter) {
                        'live' => ['live', 'halftime'].contains(status),
                        'upcoming' => status == 'scheduled',
                        'completed' => status == 'finished',
                        _ => true,
                      };
                    }).toList();
                    if (list.isEmpty) {
                      return _ScrollableMessage(
                        icon: Icons.event_busy_rounded,
                        message: t('no_matches'),
                        action: t('refresh'),
                        onPressed: reload,
                      );
                    }
                    return ListView.builder(
                      padding: const EdgeInsets.fromLTRB(14, 8, 14, 24),
                      itemCount: list.length,
                      itemBuilder: (context, i) => _MatchCard(match: Map<String, dynamic>.from(list[i])),
                    );
                  },
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }
}

class _FilterChip extends StatelessWidget {
  final String label;
  final bool selected;
  final VoidCallback onTap;
  const _FilterChip({required this.label, required this.selected, required this.onTap});

  @override
  Widget build(BuildContext context) => Padding(
        padding: const EdgeInsetsDirectional.only(end: 8),
        child: ChoiceChip(label: Text(label), selected: selected, onSelected: (_) => onTap()),
      );
}

class _MatchCard extends StatelessWidget {
  final Map<String, dynamic> match;
  const _MatchCard({required this.match});

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    final home = Map<String, dynamic>.from(match['home_team'] ?? {});
    final away = Map<String, dynamic>.from(match['away_team'] ?? {});
    final status = '${match['status'] ?? 'scheduled'}'.toLowerCase();
    final live = ['live', 'halftime'].contains(status);
    final id = match['id'] is num ? (match['id'] as num).toInt() : int.tryParse('${match['id']}');
    return Container(
      margin: const EdgeInsets.only(bottom: 11),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        color: Theme.of(context).colorScheme.surface,
        border: Border.all(
          color: live
              ? const Color(0x55FF4D6D)
              : Theme.of(context).colorScheme.outlineVariant.withValues(alpha: .24),
        ),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(24),
        onTap: id == null
            ? null
            : () => Navigator.push(context, MaterialPageRoute(builder: (_) => MatchDetailScreen(matchId: id))),
        child: Padding(
          padding: const EdgeInsets.all(17),
          child: Column(
            children: [
              Row(
                children: [
                  Expanded(
                    child: Text(
                      '${match['competition']?['name_en'] ?? match['competition']?['name_ar'] ?? ''}',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.labelMedium,
                    ),
                  ),
                  _StatusBadge(status: status, minute: match['minute']),
                ],
              ),
              const SizedBox(height: 18),
              Row(
                children: [
                  Expanded(child: _Team(team: home, align: CrossAxisAlignment.start)),
                  Container(
                    padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9),
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(16),
                      color: Theme.of(context).colorScheme.primary.withValues(alpha: .10),
                    ),
                    child: Text(
                      _centerLabel(context, status),
                      style: const TextStyle(fontSize: 21, fontWeight: FontWeight.w900),
                    ),
                  ),
                  Expanded(child: _Team(team: away, align: CrossAxisAlignment.end)),
                ],
              ),
              const SizedBox(height: 15),
              Row(
                children: [
                  Icon(Icons.location_on_outlined, size: 15, color: Theme.of(context).colorScheme.secondary),
                  const SizedBox(width: 5),
                  Expanded(
                    child: Text(
                      '${match['venue'] ?? ''}'.trim().isEmpty ? t('venue_tba') : '${match['venue']}',
                      maxLines: 1,
                      overflow: TextOverflow.ellipsis,
                      style: Theme.of(context).textTheme.bodySmall,
                    ),
                  ),
                  const Icon(Icons.chevron_right_rounded),
                ],
              ),
            ],
          ),
        ),
      ),
    );
  }

  String _centerLabel(BuildContext context, String status) {
    if (status == 'scheduled') {
      final kickoff = DateTime.tryParse('${match['kickoff_at'] ?? ''}')?.toLocal();
      return kickoff == null ? '—' : TimeOfDay.fromDateTime(kickoff).format(context);
    }
    return '${match['home_score'] ?? 0}  —  ${match['away_score'] ?? 0}';
  }
}

class _Team extends StatelessWidget {
  final Map<String, dynamic> team;
  final CrossAxisAlignment align;
  const _Team({required this.team, required this.align});

  @override
  Widget build(BuildContext context) {
    final name = '${team['name_en'] ?? team['name_ar'] ?? 'Team'}';
    final logo = '${team['logo_url'] ?? ''}';
    return Column(
      crossAxisAlignment: align,
      children: [
        SizedBox(
          width: 42,
          height: 42,
          child: logo.isEmpty
              ? _InitialsBadge(name: name)
              : CachedNetworkImage(
                  imageUrl: logo,
                  fit: BoxFit.contain,
                  errorWidget: (_, __, ___) => _InitialsBadge(name: name),
                ),
        ),
        const SizedBox(height: 7),
        Text(
          name,
          maxLines: 2,
          overflow: TextOverflow.ellipsis,
          textAlign: align == CrossAxisAlignment.end ? TextAlign.end : TextAlign.start,
          style: const TextStyle(fontWeight: FontWeight.w800, height: 1.1),
        ),
      ],
    );
  }
}

class _InitialsBadge extends StatelessWidget {
  final String name;
  const _InitialsBadge({required this.name});

  @override
  Widget build(BuildContext context) {
    final clean = name.trim();
    final length = clean.length.clamp(1, 2).toInt();
    final letters = clean.isEmpty ? 'ST' : clean.substring(0, length).toUpperCase();
    return Container(
      alignment: Alignment.center,
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(13),
        color: Theme.of(context).colorScheme.surfaceContainerHighest,
      ),
      child: Text(letters, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900)),
    );
  }
}

class _StatusBadge extends StatelessWidget {
  const _StatusBadge({required this.status, this.minute});
  final String status;
  final dynamic minute;

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    final live = ['live', 'halftime'].contains(status);
    final label = live ? '● ${t('live')} ${minute ?? ''}\'' : (status == 'scheduled' ? t('upcoming') : status.toUpperCase());
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(99),
        color: live ? const Color(0x22FF4D6D) : Theme.of(context).colorScheme.surfaceContainerHighest,
      ),
      child: Text(
        label,
        style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: live ? const Color(0xFFFF7088) : null),
      ),
    );
  }
}

class _ScrollableMessage extends StatelessWidget {
  final IconData icon;
  final String message;
  final String action;
  final Future<void> Function() onPressed;
  const _ScrollableMessage({required this.icon, required this.message, required this.action, required this.onPressed});

  @override
  Widget build(BuildContext context) => ListView(
        children: [
          const SizedBox(height: 150),
          Icon(icon, size: 52, color: Theme.of(context).colorScheme.secondary),
          const SizedBox(height: 12),
          Text(message, textAlign: TextAlign.center),
          Center(child: TextButton(onPressed: onPressed, child: Text(action))),
        ],
      );
}
