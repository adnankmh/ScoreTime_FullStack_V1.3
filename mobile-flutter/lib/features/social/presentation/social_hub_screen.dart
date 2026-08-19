import 'package:flutter/material.dart';
import '../../../core/network/football_repository.dart';

class SocialHubScreen extends StatelessWidget {
  const SocialHubScreen({super.key});
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('My Football')),
        body: ListView(padding: const EdgeInsets.all(16), children: [
          _Nav(Icons.people_alt_outlined, 'الأصدقاء', 'طلبات، قبول، وشبكة مشجعين', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const FriendsScreen()))),
          _Nav(Icons.workspace_premium_outlined, 'الإنجازات', 'Badges ونقاط وتقدم الحساب', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const AchievementsScreen()))),
          _Nav(Icons.devices_outlined, 'الأجهزة والجلسات', 'راجع الأجهزة وسجّل الخروج من أي جلسة', () => Navigator.push(context, MaterialPageRoute(builder: (_) => const SessionsScreen()))),
        ]),
      );
}

class _Nav extends StatelessWidget {
  const _Nav(this.icon, this.title, this.subtitle, this.tap);
  final IconData icon;
  final String title, subtitle;
  final VoidCallback tap;
  @override
  Widget build(BuildContext context) => Card(child: ListTile(leading: Icon(icon), title: Text(title, style: const TextStyle(fontWeight: FontWeight.w800)), subtitle: Text(subtitle), trailing: const Icon(Icons.chevron_right), onTap: tap));
}

class FriendsScreen extends StatefulWidget {
  const FriendsScreen({super.key});
  @override
  State<FriendsScreen> createState() => _FriendsScreenState();
}

class _FriendsScreenState extends State<FriendsScreen> {
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = FootballRepository().friends();
  }
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Friends')),
        floatingActionButton: FloatingActionButton(onPressed: () => _add(context), child: const Icon(Icons.person_add)),
        body: FutureBuilder<List<dynamic>>(
          future: future,
          builder: (context, snapshot) {
            if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, i) {
                final x = Map<String, dynamic>.from(snapshot.data![i]);
                return ListTile(title: Text('${x['requester']?['username'] ?? x['addressee']?['username'] ?? 'User'}'), subtitle: Text('${x['status'] ?? ''}'));
              },
            );
          },
        ),
      );
  Future<void> _add(BuildContext context) async {
    final controller = TextEditingController();
    final username = await showDialog<String>(
      context: context,
      builder: (dialogContext) => AlertDialog(
        title: const Text('إضافة صديق'),
        content: TextField(controller: controller, decoration: const InputDecoration(labelText: 'Username')),
        actions: [FilledButton(onPressed: () => Navigator.pop(dialogContext, controller.text.trim()), child: const Text('إرسال'))],
      ),
    );
    if (username != null && username.isNotEmpty) {
      await FootballRepository().addFriend(username);
      setState(() => future = FootballRepository().friends());
    }
  }
}

class AchievementsScreen extends StatelessWidget {
  const AchievementsScreen({super.key});
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Achievements')),
        body: FutureBuilder<List<dynamic>>(
          future: FootballRepository().achievements(),
          builder: (context, snapshot) {
            if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, i) {
                final a = Map<String, dynamic>.from(snapshot.data![i]);
                return Card(child: ListTile(leading: Icon(a['earned'] == true ? Icons.emoji_events : Icons.lock_outline), title: Text('${a['name_ar'] ?? a['name_en']}'), subtitle: Text('${a['description_ar'] ?? ''}'), trailing: Text('+${a['points'] ?? 0}')));
              },
            );
          },
        ),
      );
}

class SessionsScreen extends StatefulWidget {
  const SessionsScreen({super.key});
  @override
  State<SessionsScreen> createState() => _SessionsScreenState();
}

class _SessionsScreenState extends State<SessionsScreen> {
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = FootballRepository().sessions();
  }
  @override
  Widget build(BuildContext context) => Scaffold(
        appBar: AppBar(title: const Text('Devices & Sessions')),
        body: FutureBuilder<List<dynamic>>(
          future: future,
          builder: (context, snapshot) {
            if (!snapshot.hasData) return const Center(child: CircularProgressIndicator());
            return ListView.builder(
              itemCount: snapshot.data!.length,
              itemBuilder: (context, i) {
                final x = Map<String, dynamic>.from(snapshot.data![i]);
                return ListTile(
                  leading: const Icon(Icons.devices),
                  title: Text('${x['device_name'] ?? x['platform'] ?? 'Device'}'),
                  subtitle: Text('${x['ip_address'] ?? ''} • ${x['last_seen_at'] ?? ''}'),
                  trailing: IconButton(
                    icon: const Icon(Icons.logout),
                    onPressed: () async {
                      await FootballRepository().revokeSession(x['id']);
                      setState(() => future = FootballRepository().sessions());
                    },
                  ),
                );
              },
            );
          },
        ),
      );
}
