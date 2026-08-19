import 'package:flutter/material.dart';
import '../../../core/i18n/app_strings.dart';
import '../../../core/network/football_repository.dart';

class DataStatusScreen extends StatefulWidget {
  const DataStatusScreen({super.key});

  @override
  State<DataStatusScreen> createState() => _DataStatusScreenState();
}

class _DataStatusScreenState extends State<DataStatusScreen> {
  final repo = FootballRepository();
  late Future<Map<String, dynamic>> future = repo.dataStatus();

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    return Scaffold(
      appBar: AppBar(title: Text(t('data_status'))),
      body: FutureBuilder<Map<String, dynamic>>(
        future: future,
        builder: (context, snapshot) {
          if (snapshot.connectionState != ConnectionState.done) {
            return const Center(child: CircularProgressIndicator());
          }
          if (snapshot.hasError) {
            return Center(child: Text('${snapshot.error}'));
          }
          final data = snapshot.data ?? {};
          final provider = Map<String, dynamic>.from(data['provider'] ?? {});
          final catalog = Map<String, dynamic>.from(data['catalog'] ?? {});
          final freshness = Map<String, dynamic>.from(data['freshness'] ?? {});
          return RefreshIndicator(
            onRefresh: () async => setState(() => future = repo.dataStatus()),
            child: ListView(
              padding: const EdgeInsets.all(18),
              children: [
                _StatusCard(
                  title: t('provider'),
                  value: '${provider['provider'] ?? '-'}',
                  subtitle: provider['ok'] == true ? t('real_time') : '${provider['message'] ?? '-'}',
                  icon: Icons.hub_rounded,
                ),
                const SizedBox(height: 12),
                Wrap(
                  spacing: 12,
                  runSpacing: 12,
                  children: catalog.entries.map((e) => SizedBox(
                    width: 160,
                    child: _StatusCard(
                      title: e.key,
                      value: '${e.value}',
                      subtitle: t('last_updated'),
                      icon: Icons.query_stats_rounded,
                    ),
                  )).toList(),
                ),
                const SizedBox(height: 12),
                ...freshness.entries.map((e) => ListTile(
                  leading: const Icon(Icons.schedule_rounded),
                  title: Text(e.key),
                  subtitle: Text('${e.value ?? '-'}'),
                )),
              ],
            ),
          );
        },
      ),
    );
  }
}

class _StatusCard extends StatelessWidget {
  final String title;
  final String value;
  final String subtitle;
  final IconData icon;
  const _StatusCard({required this.title, required this.value, required this.subtitle, required this.icon});

  @override
  Widget build(BuildContext context) {
    return Card(
      child: Padding(
        padding: const EdgeInsets.all(18),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: Theme.of(context).colorScheme.secondary),
            const SizedBox(height: 14),
            Text(value, style: Theme.of(context).textTheme.headlineMedium),
            const SizedBox(height: 4),
            Text(title, style: const TextStyle(fontWeight: FontWeight.w800)),
            const SizedBox(height: 2),
            Text(subtitle, style: Theme.of(context).textTheme.bodySmall),
          ],
        ),
      ),
    );
  }
}
