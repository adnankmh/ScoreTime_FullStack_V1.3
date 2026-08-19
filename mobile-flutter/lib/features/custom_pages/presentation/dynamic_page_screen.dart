import 'package:flutter/material.dart';
import '../../../core/network/api_client.dart';

class DynamicPageScreen extends StatefulWidget {
  final String slug;
  const DynamicPageScreen({super.key, required this.slug});
  @override
  State<DynamicPageScreen> createState() => _DynamicPageScreenState();
}

class _DynamicPageScreenState extends State<DynamicPageScreen> {
  Map<String, dynamic>? page;
  String? error;

  @override
  void initState() {
    super.initState();
    _load();
  }

  Future<void> _load() async {
    try {
      final r = await ApiClient().dio.get('/pages/${widget.slug}');
      if (mounted) setState(() => page = Map<String, dynamic>.from(r.data['data']));
    } catch (_) {
      if (mounted) setState(() => error = 'Could not load this page.');
    }
  }

  @override
  Widget build(BuildContext context) {
    if (error != null) return Center(child: Text(error!));
    if (page == null) return const Center(child: CircularProgressIndicator());
    final blocks = List<dynamic>.from(page!['blocks'] ?? []);
    return SafeArea(
      child: CustomScrollView(
        slivers: [
          SliverToBoxAdapter(
            child: Padding(
              padding: const EdgeInsets.fromLTRB(18, 18, 18, 8),
              child: Text('${page!['title']}', style: Theme.of(context).textTheme.headlineMedium?.copyWith(fontWeight: FontWeight.w900)),
            ),
          ),
          ...blocks.where((b) => b is Map && (b['enabled'] ?? true) == true).map((b) => _renderBlock(context, Map<String, dynamic>.from(b as Map))),
          const SliverToBoxAdapter(child: SizedBox(height: 30)),
        ],
      ),
    );
  }

  Widget _renderBlock(BuildContext context, Map<String, dynamic> block) {
    final type = '${block['type']}';
    final config = Map<String, dynamic>.from(block['config'] ?? {});
    switch (type) {
      case 'hero':
        return SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.all(16),
            child: Container(
              constraints: const BoxConstraints(minHeight: 190),
              padding: const EdgeInsets.all(22),
              decoration: BoxDecoration(
                borderRadius: BorderRadius.circular(26),
                gradient: const LinearGradient(colors: [Color(0xFF0C2E61), Color(0xFF061329)]),
              ),
              child: Column(mainAxisAlignment: MainAxisAlignment.end, crossAxisAlignment: CrossAxisAlignment.start, children: [
                const Text('SCORETIME FEATURED', style: TextStyle(fontWeight: FontWeight.w900, letterSpacing: 1.1)),
                const SizedBox(height: 8),
                Text('${config['headline'] ?? 'Football, your way.'}', style: const TextStyle(fontSize: 30, fontWeight: FontWeight.w900)),
                if ('${config['subheadline'] ?? ''}'.isNotEmpty) Padding(padding: const EdgeInsets.only(top: 8), child: Text('${config['subheadline']}')),
              ]),
            ),
          ),
        );
      case 'rich_text':
        return SliverToBoxAdapter(child: Padding(padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8), child: Card(child: Padding(padding: const EdgeInsets.all(18), child: Text('${config['text'] ?? ''}')))));
      case 'live_matches':
      case 'latest_news':
      case 'breaking_news':
      case 'transfers':
        final data = List<dynamic>.from(block['data'] ?? []);
        final limit = (config['limit'] as num?)?.toInt() ?? 8;
        return SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Card(
              child: Padding(
                padding: const EdgeInsets.all(14),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(type.replaceAll('_', ' ').toUpperCase(), style: const TextStyle(fontWeight: FontWeight.w900)),
                    const SizedBox(height: 8),
                    if (data.isEmpty)
                      const Text('No items right now.')
                    else
                      ...data.take(limit).map((x) {
                        final m = Map<String, dynamic>.from(x);
                        final title = type == 'live_matches'
                            ? '${m['home']}  ${m['home_score']}–${m['away_score']}  ${m['away']}'
                            : type == 'transfers'
                                ? '${m['player']} · ${m['from']} → ${m['to']}'
                                : '${m['title']}';
                        return Padding(
                          padding: const EdgeInsets.symmetric(vertical: 7),
                          child: Row(children: [
                            Expanded(child: Text(title, style: const TextStyle(fontWeight: FontWeight.w700))),
                            if (type == 'live_matches') Text("${m['minute'] ?? ''}'"),
                          ]),
                        );
                      }),
                  ],
                ),
              ),
            ),
          ),
        );
      default:
        return SliverToBoxAdapter(
          child: Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
            child: Card(child: ListTile(title: Text(type.replaceAll('_', ' ').toUpperCase()), subtitle: Text(config['limit'] != null ? 'Showing up to ${config['limit']} items' : 'Dynamic block'))),
          ),
        );
    }
  }
}
