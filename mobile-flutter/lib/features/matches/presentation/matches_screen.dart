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
  String filter = 'all';

  @override
  void initState() {
    super.initState();
    future = FootballRepository().matches();
  }

  Future<void> reload() async {
    setState(() => future = FootballRepository().matches());
    await future;
  }

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    return Scaffold(
      body: SafeArea(
        child: Column(children: [
          Padding(
            padding: const EdgeInsets.fromLTRB(16, 12, 8, 8),
            child: Row(children: [
              ClipRRect(borderRadius: BorderRadius.circular(13), child: Image.asset('assets/icons/scoretime_icon.png', width: 42, height: 42)),
              const SizedBox(width: 10),
              Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(t('matches'), style: Theme.of(context).textTheme.titleLarge), Text('ScoreTime Live Center', style: Theme.of(context).textTheme.bodySmall)])),
              IconButton(onPressed: reload, icon: const Icon(Icons.refresh_rounded)),
            ]),
          ),
          SizedBox(
            height: 48,
            child: ListView(
              scrollDirection: Axis.horizontal,
              padding: const EdgeInsets.symmetric(horizontal: 16),
              children: [
                _FilterChip(label: 'All', selected: filter == 'all', onTap: () => setState(() => filter = 'all')),
                _FilterChip(label: t('live'), selected: filter == 'live', onTap: () => setState(() => filter = 'live')),
                _FilterChip(label: t('today'), selected: filter == 'today', onTap: () => setState(() => filter = 'today')),
              ],
            ),
          ),
          Expanded(
            child: RefreshIndicator(
              onRefresh: reload,
              child: FutureBuilder<List<dynamic>>(
                future: future,
                builder: (context, snapshot) {
                  if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
                  if (snapshot.hasError) return ListView(children: [const SizedBox(height: 180), const Center(child: Icon(Icons.cloud_off_rounded, size: 50)), Center(child: TextButton(onPressed: reload, child: const Text('Retry')))]);
                  final all = snapshot.data ?? [];
                  final list = filter == 'live' ? all.where((m) => ['live', 'halftime'].contains('${m['status']}'.toLowerCase())).toList() : all;
                  if (list.isEmpty) return ListView(children: const [SizedBox(height: 180), Center(child: Text('No matches in this view.'))]);
                  return ListView.builder(
                    padding: const EdgeInsets.fromLTRB(14, 8, 14, 24),
                    itemCount: list.length,
                    itemBuilder: (context, i) => _MatchCard(match: Map<String, dynamic>.from(list[i])),
                  );
                },
              ),
            ),
          ),
        ]),
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
        padding: const EdgeInsets.only(right: 8),
        child: ChoiceChip(label: Text(label), selected: selected, onSelected: (_) => onTap()),
      );
}

class _MatchCard extends StatelessWidget {
  final Map<String, dynamic> match;
  const _MatchCard({required this.match});
  @override
  Widget build(BuildContext context) {
    final home = Map<String, dynamic>.from(match['home_team'] ?? {});
    final away = Map<String, dynamic>.from(match['away_team'] ?? {});
    final live = ['live', 'halftime'].contains('${match['status']}'.toLowerCase());
    return Container(
      margin: const EdgeInsets.only(bottom: 11),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(24),
        color: Theme.of(context).colorScheme.surface,
        border: Border.all(color: live ? const Color(0x55FF4D6D) : Theme.of(context).colorScheme.outlineVariant.withOpacity(.24)),
      ),
      child: InkWell(
        borderRadius: BorderRadius.circular(24),
        onTap: () => Navigator.push(context, MaterialPageRoute(builder: (_) => MatchDetailScreen(matchId: match['id']))),
        child: Padding(
          padding: const EdgeInsets.all(17),
          child: Column(children: [
            Row(children: [Expanded(child: Text('${match['competition']?['name_en'] ?? match['competition']?['name_ar'] ?? ''}', maxLines: 1, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.labelMedium)), _LiveBadge(status: '${match['status'] ?? ''}', minute: match['minute'])]),
            const SizedBox(height: 18),
            Row(children: [
              Expanded(child: _Team(name: '${home['name_en'] ?? home['name_ar'] ?? 'Home'}', align: CrossAxisAlignment.start)),
              Container(padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 9), decoration: BoxDecoration(borderRadius: BorderRadius.circular(16), color: Theme.of(context).colorScheme.primary.withOpacity(.10)), child: Text('${match['home_score'] ?? 0}  —  ${match['away_score'] ?? 0}', style: const TextStyle(fontSize: 21, fontWeight: FontWeight.w900))),
              Expanded(child: _Team(name: '${away['name_en'] ?? away['name_ar'] ?? 'Away'}', align: CrossAxisAlignment.end)),
            ]),
            const SizedBox(height: 15),
            Row(children: [Icon(Icons.location_on_outlined, size: 15, color: Theme.of(context).colorScheme.secondary), const SizedBox(width: 5), Expanded(child: Text('${match['venue'] ?? 'Venue TBA'}', maxLines: 1, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.bodySmall)), const Icon(Icons.chevron_right_rounded)]),
          ]),
        ),
      ),
    );
  }
}

class _Team extends StatelessWidget {
  final String name;
  final CrossAxisAlignment align;
  const _Team({required this.name, required this.align});
  @override
  Widget build(BuildContext context) => Column(crossAxisAlignment: align, children: [Container(width: 36, height: 36, alignment: Alignment.center, decoration: BoxDecoration(borderRadius: BorderRadius.circular(12), color: Theme.of(context).colorScheme.surfaceContainerHighest), child: Text(name.substring(0, name.length > 1 ? 2 : 1).toUpperCase(), style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w900))), const SizedBox(height: 6), Text(name, maxLines: 2, textAlign: align == CrossAxisAlignment.end ? TextAlign.end : TextAlign.start, style: const TextStyle(fontWeight: FontWeight.w800, height: 1.1))]);
}

class _LiveBadge extends StatelessWidget {
  const _LiveBadge({required this.status, this.minute});
  final String status;
  final dynamic minute;
  @override
  Widget build(BuildContext context) {
    final live = ['live', 'halftime'].contains(status.toLowerCase());
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6),
      decoration: BoxDecoration(borderRadius: BorderRadius.circular(99), color: live ? const Color(0x22FF4D6D) : Theme.of(context).colorScheme.surfaceContainerHighest),
      child: Text(live ? '● LIVE ${minute ?? ''}\'' : status.toUpperCase(), style: TextStyle(fontSize: 10, fontWeight: FontWeight.w900, color: live ? const Color(0xFFFF7088) : null)),
    );
  }
}
