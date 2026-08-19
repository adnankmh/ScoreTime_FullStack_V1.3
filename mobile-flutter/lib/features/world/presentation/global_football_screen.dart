import 'package:flutter/material.dart';
import '../../../core/network/football_repository.dart';
import '../../../core/i18n/app_strings.dart';

class GlobalFootballScreen extends StatefulWidget {
  const GlobalFootballScreen({super.key});
  @override
  State<GlobalFootballScreen> createState() => _GlobalFootballScreenState();
}

class _GlobalFootballScreenState extends State<GlobalFootballScreen> {
  final repo = FootballRepository();
  late Future<Map<String, dynamic>> summary;
  late Future<List<dynamic>> competitions;
  late Future<List<dynamic>> players;
  String query = '';

  @override
  void initState() {
    super.initState();
    summary = repo.worldSummary();
    competitions = repo.worldCompetitions();
    players = repo.worldPlayers();
  }

  void search(String value) {
    setState(() {
      query = value.trim();
      competitions = repo.worldCompetitions(q: query);
      players = repo.worldPlayers(q: query);
    });
  }

  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: Text(AppStrings.of(context)('world'))),
        body: RefreshIndicator(
          onRefresh: () async {
            setState(() {
              summary = repo.worldSummary();
              competitions = repo.worldCompetitions(q: query);
              players = repo.worldPlayers(q: query);
            });
            await summary;
          },
          child: ListView(
            padding: const EdgeInsets.all(16),
            children: [
              FutureBuilder<Map<String, dynamic>>(
                future: summary,
                builder: (context, snap) {
                  final d = snap.data;
                  if (d == null) return const LinearProgressIndicator();
                  return Wrap(
                    spacing: 8,
                    runSpacing: 8,
                    children: [
                      _Metric('Countries', d['countries']),
                      _Metric('Competitions', d['competitions']),
                      _Metric('Teams', d['teams']),
                      _Metric('Players', d['players']),
                    ],
                  );
                },
              ),
              const SizedBox(height: 16),
              SearchBar(
                hintText: AppStrings.of(context)('search_world'),
                leading: const Icon(Icons.search),
                onSubmitted: search,
              ),
              const SizedBox(height: 20),
              Text(AppStrings.of(context)('competitions'), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
              const SizedBox(height: 8),
              FutureBuilder<List<dynamic>>(
                future: competitions,
                builder: (context, snap) {
                  if (!snap.hasData) return const Center(child: CircularProgressIndicator());
                  return Column(
                    children: snap.data!.take(12).map((x) {
                      final c = Map<String, dynamic>.from(x);
                      return Card(
                        child: ListTile(
                          leading: c['logo_url'] == null
                              ? const CircleAvatar(child: Icon(Icons.emoji_events_outlined))
                              : CircleAvatar(backgroundImage: NetworkImage('${c['logo_url']}')),
                          title: Text('${c['name_en'] ?? c['name_ar'] ?? ''}'),
                          subtitle: Text('${c['country'] ?? 'International'} • ${c['type'] ?? 'league'}'),
                          trailing: c['is_international'] == true ? const Icon(Icons.public) : null,
                        ),
                      );
                    }).toList(),
                  );
                },
              ),
              const SizedBox(height: 20),
              Text(AppStrings.of(context)('players'), style: const TextStyle(fontSize: 20, fontWeight: FontWeight.w900)),
              const SizedBox(height: 8),
              FutureBuilder<List<dynamic>>(
                future: players,
                builder: (context, snap) {
                  if (!snap.hasData) return const Center(child: CircularProgressIndicator());
                  return Column(
                    children: snap.data!.take(20).map((x) {
                      final p = Map<String, dynamic>.from(x);
                      final team = p['team'] is Map ? Map<String, dynamic>.from(p['team']) : <String, dynamic>{};
                      return ListTile(
                        leading: p['photo_url'] == null
                            ? const CircleAvatar(child: Icon(Icons.person))
                            : CircleAvatar(backgroundImage: NetworkImage('${p['photo_url']}')),
                        title: Text('${p['name'] ?? ''}'),
                        subtitle: Text('${team['name_en'] ?? ''} • ${p['position'] ?? ''} • ${p['nationality'] ?? ''}'),
                        trailing: p['rating'] == null ? null : Text('${p['rating']}', style: const TextStyle(fontWeight: FontWeight.w900)),
                      );
                    }).toList(),
                  );
                },
              ),
            ],
          ),
        ),
      );
}

class _Metric extends StatelessWidget {
  const _Metric(this.label, this.value);
  final String label;
  final dynamic value;
  @override
  Widget build(BuildContext context) => SizedBox(
        width: 150,
        child: Card(
          child: Padding(
            padding: const EdgeInsets.all(14),
            child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [
              Text(label, style: Theme.of(context).textTheme.labelMedium),
              const SizedBox(height: 4),
              Text('${value ?? 0}', style: const TextStyle(fontSize: 24, fontWeight: FontWeight.w900)),
            ]),
          ),
        ),
      );
}
