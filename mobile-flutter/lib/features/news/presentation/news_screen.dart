import 'package:flutter/material.dart';
import '../../../core/i18n/app_strings.dart';
import '../../../core/network/football_repository.dart';

class NewsScreen extends StatefulWidget {
  const NewsScreen({super.key});
  @override
  State<NewsScreen> createState() => _NewsScreenState();
}

class _NewsScreenState extends State<NewsScreen> {
  final repo = FootballRepository();
  late Future<List<dynamic>> future;
  @override
  void initState() {
    super.initState();
    future = repo.personalizedNews();
  }

  Future<void> reload() async {
    setState(() => future = repo.personalizedNews());
    await future;
  }

  @override
  Widget build(BuildContext context) {
    final t = AppStrings.of(context);
    return Scaffold(
      body: SafeArea(
        child: RefreshIndicator(
          onRefresh: reload,
          child: FutureBuilder<List<dynamic>>(
            future: future,
            builder: (context, snapshot) {
              if (snapshot.connectionState == ConnectionState.waiting) return const Center(child: CircularProgressIndicator());
              if (snapshot.hasError) return ListView(children: [const SizedBox(height: 180), const Center(child: Icon(Icons.cloud_off_rounded, size: 48)), Center(child: TextButton(onPressed: reload, child: const Text('Retry')))]);
              final items = snapshot.data ?? [];
              return ListView(
                padding: const EdgeInsets.fromLTRB(16, 12, 16, 30),
                children: [
                  Row(children: [ClipRRect(borderRadius: BorderRadius.circular(13), child: Image.asset('assets/icons/scoretime_icon.png', width: 42, height: 42)), const SizedBox(width: 10), Expanded(child: Column(crossAxisAlignment: CrossAxisAlignment.start, children: [Text(t('news'), style: Theme.of(context).textTheme.headlineSmall?.copyWith(fontWeight: FontWeight.w900)), Text('For you • Breaking • Transfers', style: Theme.of(context).textTheme.bodySmall)]))]),
                  const SizedBox(height: 18),
                  if (items.isNotEmpty) _FeaturedStory(item: Map<String, dynamic>.from(items.first), repo: repo),
                  const SizedBox(height: 18),
                  Text('SCORETIME EDITORIAL', style: TextStyle(color: Theme.of(context).colorScheme.secondary, fontSize: 11, fontWeight: FontWeight.w900, letterSpacing: 1.2)),
                  const SizedBox(height: 10),
                  if (items.isEmpty) const Padding(padding: EdgeInsets.all(40), child: Center(child: Text('No published stories yet.'))),
                  ...items.skip(1).map((raw) => _StoryCard(item: Map<String, dynamic>.from(raw), repo: repo)),
                ],
              );
            },
          ),
        ),
      ),
    );
  }
}

class _FeaturedStory extends StatelessWidget {
  final Map<String, dynamic> item;
  final FootballRepository repo;
  const _FeaturedStory({required this.item, required this.repo});
  @override
  Widget build(BuildContext context) => InkWell(
        borderRadius: BorderRadius.circular(28),
        onTap: () { final id = item['id']; if (id is int) repo.articleSignal(id, 'open'); },
        child: Container(
          minHeight: 270,
          padding: const EdgeInsets.all(22),
          decoration: BoxDecoration(borderRadius: BorderRadius.circular(28), gradient: const LinearGradient(begin: Alignment.topLeft, end: Alignment.bottomRight, colors: [Color(0xFF0C2E61), Color(0xFF071B3B), Color(0xFF061020)]), border: Border.all(color: Colors.white.withOpacity(.08))),
          child: Column(crossAxisAlignment: CrossAxisAlignment.start, mainAxisAlignment: MainAxisAlignment.end, children: [
            Row(children: [Container(padding: const EdgeInsets.symmetric(horizontal: 10, vertical: 6), decoration: BoxDecoration(borderRadius: BorderRadius.circular(99), color: item['is_breaking'] == true ? const Color(0x22FF4D6D) : const Color(0x220B8CFF)), child: Text('${item['category'] ?? 'Football'}', style: TextStyle(fontWeight: FontWeight.w900, color: item['is_breaking'] == true ? const Color(0xFFFF8096) : const Color(0xFF62C7FF)))), const Spacer(), const Icon(Icons.arrow_outward_rounded)]),
            const SizedBox(height: 22),
            Text('${item['title'] ?? ''}', maxLines: 4, overflow: TextOverflow.ellipsis, style: const TextStyle(fontSize: 25, height: 1.08, fontWeight: FontWeight.w900, letterSpacing: -.6)),
            const SizedBox(height: 10),
            Text('${item['excerpt'] ?? item['author_name'] ?? ''}', maxLines: 2, overflow: TextOverflow.ellipsis, style: Theme.of(context).textTheme.bodyMedium?.copyWith(color: Colors.white70)),
          ]),
        ),
      );
}

class _StoryCard extends StatelessWidget {
  final Map<String, dynamic> item;
  final FootballRepository repo;
  const _StoryCard({required this.item, required this.repo});
  @override
  Widget build(BuildContext context) => Container(
        margin: const EdgeInsets.only(bottom: 10),
        decoration: BoxDecoration(borderRadius: BorderRadius.circular(22), color: Theme.of(context).colorScheme.surface, border: Border.all(color: Theme.of(context).colorScheme.outlineVariant.withOpacity(.24))),
        child: ListTile(
          contentPadding: const EdgeInsets.all(14),
          leading: Container(width: 54, height: 54, decoration: BoxDecoration(borderRadius: BorderRadius.circular(17), gradient: const LinearGradient(colors: [Color(0xFF0B8CFF), Color(0xFF18D7FF)])), child: Icon(item['is_breaking'] == true ? Icons.bolt_rounded : Icons.article_rounded, color: Colors.white)),
          title: Text('${item['title'] ?? ''}', maxLines: 3, overflow: TextOverflow.ellipsis, style: const TextStyle(fontWeight: FontWeight.w900, height: 1.2)),
          subtitle: Padding(padding: const EdgeInsets.only(top: 7), child: Text('${item['category'] ?? 'Football'} • ${item['author_name'] ?? 'ScoreTime'}')),
          trailing: const Icon(Icons.chevron_right_rounded),
          onTap: () { final id = item['id']; if (id is int) repo.articleSignal(id, 'open'); },
        ),
      );
}
