import 'package:flutter/material.dart';
import '../../../core/network/football_repository.dart';
import '../../discovery/presentation/discovery_screen.dart';
import '../../social/presentation/fan_progress_screen.dart';

class WorldClassScreen extends StatelessWidget {
  const WorldClassScreen({super.key});
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Football Hub')),
        body: ListView(
          padding: const EdgeInsets.all(16),
          children: [
            _Tile(Icons.travel_explore, 'Discover & Trending', 'Smart search and live trends', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const DiscoveryScreen()))),
            _Tile(Icons.military_tech, 'Fan Levels & Challenges', 'XP, levels, streaks and head-to-head challenges', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FanProgressScreen()))),
            _Tile(Icons.emoji_events_outlined, 'الهدافون والإحصائيات', 'Goals • Assists • Ratings • xG', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const LeadersScreen()))),
            _Tile(Icons.notifications_active_outlined, 'مركز التنبيهات', 'الأهداف، التشكيلات، البطاقات والانتقالات', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const NotificationsScreen()))),
            _Tile(Icons.groups_2_outlined, 'الدوريات الخاصة', 'Mini Leagues للتوقعات مع الأصدقاء', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const MiniLeaguesScreen()))),
            const _StatusCard(),
          ],
        ),
      );
}

class _Tile extends StatelessWidget {
  const _Tile(this.icon, this.title, this.subtitle, this.tap);
  final IconData icon;
  final String title, subtitle;
  final VoidCallback tap;
  @override
  Widget build(BuildContext context) => Card(
        child: ListTile(
          leading: Icon(icon),
          title: Text(title, style: const TextStyle(fontWeight: FontWeight.w800)),
          subtitle: Text(subtitle),
          trailing: const Icon(Icons.chevron_right),
          onTap: tap,
        ),
      );
}

class _StatusCard extends StatefulWidget {
  const _StatusCard();
  @override
  State<_StatusCard> createState() => _StatusCardState();
}

class _StatusCardState extends State<_StatusCard> {
  Map<String, dynamic>? data;
  @override
  void initState() {
    super.initState();
    FootballRepository().providerHealth().then((v) {
      if (mounted) setState(() => data = v);
    }).catchError((_) {
      if (mounted) setState(() => data = {'ok': false});
    });
  }

  @override
  Widget build(BuildContext context) => Card(
        child: ListTile(
          leading: Icon(data?['ok'] == true ? Icons.cloud_done : Icons.cloud_off),
          title: const Text('Data Provider'),
          subtitle: Text(data == null ? 'Checking…' : data?['ok'] == true ? 'Online • ${data?['provider'] ?? 'provider'}' : 'Offline'),
        ),
      );
}

class LeadersScreen extends StatefulWidget {
  const LeadersScreen({super.key});
  @override
  State<LeadersScreen> createState() => _LeadersScreenState();
}

class _LeadersScreenState extends State<LeadersScreen> {
  String metric = 'goals';
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = FootballRepository().leaders();
  }
  void load(String value) => setState(() {
        metric = value;
        future = FootballRepository().leaders(metric: value);
      });
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Leaders')),
        body: Column(children: [
          Padding(
            padding: const EdgeInsets.all(12),
            child: SegmentedButton<String>(
              segments: const [
                ButtonSegment(value: 'goals', label: Text('Goals')),
                ButtonSegment(value: 'assists', label: Text('Assists')),
                ButtonSegment(value: 'rating', label: Text('Rating')),
              ],
              selected: {metric},
              onSelectionChanged: (v) => load(v.first),
            ),
          ),
          Expanded(
            child: FutureBuilder<List<dynamic>>(
              future: future,
              builder: (context, snapshot) {
                if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
                return ListView.builder(
                  itemCount: snapshot.data!.length,
                  itemBuilder: (context, i) {
                    final x = Map<String, dynamic>.from(snapshot.data![i]);
                    return ListTile(
                      leading: CircleAvatar(child: Text('${i + 1}')),
                      title: Text('${x['player']?['name'] ?? 'Player'}'),
                      subtitle: Text('${x['competition']?['name_en'] ?? ''}'),
                      trailing: Text('${x[metric] ?? 0}', style: const TextStyle(fontWeight: FontWeight.w900)),
                    );
                  },
                );
              },
            ),
          ),
        ]),
      );
}

class NotificationsScreen extends StatefulWidget {
  const NotificationsScreen({super.key});
  @override
  State<NotificationsScreen> createState() => _NotificationsScreenState();
}

class _NotificationsScreenState extends State<NotificationsScreen> {
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = FootballRepository().notifications();
  }
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Notifications'), actions: [
          IconButton(
            onPressed: () async {
              await FootballRepository().readAllNotifications();
              setState(() => future = FootballRepository().notifications());
            },
            icon: const Icon(Icons.done_all),
          )
        ]),
        body: FutureBuilder<List<dynamic>>(
          future: future,
          builder: (context, snapshot) {
            if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
            if (snapshot.data!.isEmpty) return const Center(child: Text('لا توجد تنبيهات بعد'));
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, i) {
                final n = Map<String, dynamic>.from(snapshot.data![i]);
                return ListTile(
                  leading: Icon(n['read_at'] == null ? Icons.notifications_active : Icons.notifications_none),
                  title: Text('${n['title']}'),
                  subtitle: Text('${n['body'] ?? ''}'),
                );
              },
            );
          },
        ),
      );
}

class MiniLeaguesScreen extends StatefulWidget {
  const MiniLeaguesScreen({super.key});
  @override
  State<MiniLeaguesScreen> createState() => _MiniLeaguesScreenState();
}

class _MiniLeaguesScreenState extends State<MiniLeaguesScreen> {
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = FootballRepository().miniLeagues();
  }
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Mini Leagues')),
        floatingActionButton: FloatingActionButton(onPressed: () => _create(context), child: const Icon(Icons.add)),
        body: FutureBuilder<List<dynamic>>(
          future: future,
          builder: (context, snapshot) {
            if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, i) {
                final x = Map<String, dynamic>.from(snapshot.data![i]);
                return Card(child: ListTile(title: Text('${x['name']}'), subtitle: Text('Code: ${x['join_code'] ?? ''}'), trailing: Text('${x['members_count'] ?? ''}')));
              },
            );
          },
        ),
      );

  Future<void> _create(BuildContext context) async {
    final controller = TextEditingController();
    final name = await showDialog<String>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('دوري خاص جديد'),
        content: TextField(controller: controller, decoration: const InputDecoration(labelText: 'الاسم')),
        actions: [
          TextButton(onPressed: () => Navigator.pop(dialogContext), child: const Text('إلغاء')),
          FilledButton(onPressed: () => Navigator.pop(dialogContext, controller.text.trim()), child: const Text('إنشاء')),
        ],
      ),
    );
    if (name != null && name.isNotEmpty) {
      await FootballRepository().createMiniLeague(name);
      setState(() => future = FootballRepository().miniLeagues());
    }
  }
}
