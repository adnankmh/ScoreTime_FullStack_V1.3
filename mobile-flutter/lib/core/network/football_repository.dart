import 'package:dio/dio.dart';

import '../config/app_config.dart';
import 'api_client.dart';
import 'demo_data.dart';

class FootballRepository {
  FootballRepository([Dio? dio]) : _dio = dio ?? ApiClient().dio;

  final Dio _dio;

  bool get _demo => AppConfig.webDemoMode;

  Future<List<dynamic>> matches([String? date]) async {
    if (_demo) return List<dynamic>.from(DemoData.matches);
    return List<dynamic>.from((await _dio.get(
      '/matches',
      queryParameters: {if (date != null) 'date': date},
    )).data['data']);
  }

  Future<Map<String, dynamic>> match(int id) async {
    if (_demo) {
      final raw = DemoData.matches.firstWhere(
        (m) => m['id'] == id,
        orElse: () => DemoData.matches.first,
      );
      return Map<String, dynamic>.from(raw);
    }
    return Map<String, dynamic>.from((await _dio.get('/matches/$id')).data['data']);
  }

  Future<Map<String, dynamic>> intelligence(int id) async {
    if (_demo) {
      return {
        'momentum': [42, 48, 56, 51, 63, 70],
        'home_xg': 1.82,
        'away_xg': 1.14,
        'home_win_probability': 58,
        'draw_probability': 24,
        'away_win_probability': 18,
      };
    }
    return Map<String, dynamic>.from(
      (await _dio.get('/matches/$id/intelligence')).data['data'],
    );
  }

  Future<List<dynamic>> transfers() async {
    if (_demo) return List<dynamic>.from(DemoData.transfers);
    return List<dynamic>.from(
      (await _dio.get('/transfers')).data['data']['data'] ?? [],
    );
  }

  Future<List<dynamic>> leaderboard() async {
    if (_demo) return [];
    return List<dynamic>.from((await _dio.get('/leaderboard')).data['data'] ?? []);
  }

  Future<Map<String, dynamic>> search(String q) async {
    if (_demo) {
      final needle = q.toLowerCase();
      return {
        'players': DemoData.players
            .where((p) => '${p['name']}'.toLowerCase().contains(needle))
            .toList(),
        'competitions': DemoData.competitions
            .where((c) => '${c['name_en']}'.toLowerCase().contains(needle))
            .toList(),
      };
    }
    return Map<String, dynamic>.from(
      (await _dio.get('/search', queryParameters: {'q': q})).data['data'],
    );
  }

  Future<List<dynamic>> leaders({String metric = 'goals'}) async {
    if (_demo) return List<dynamic>.from(DemoData.players);
    return List<dynamic>.from(
      (await _dio.get('/stats/leaders', queryParameters: {'metric': metric})).data,
    );
  }

  Future<List<dynamic>> notifications() async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/notifications')).data['data'] ?? [],
    );
  }

  Future<void> readAllNotifications() async {
    if (_demo) return;
    await _dio.post('/notifications/read-all');
  }

  Future<List<dynamic>> miniLeagues() async {
    if (_demo) return [];
    return List<dynamic>.from((await _dio.get('/mini-leagues')).data);
  }

  Future<Map<String, dynamic>> createMiniLeague(String name) async {
    if (_demo) return {'name': name, 'code': 'DEMO'};
    return Map<String, dynamic>.from(
      (await _dio.post('/mini-leagues', data: {'name': name})).data,
    );
  }

  Future<Map<String, dynamic>> joinMiniLeague(String code) async {
    if (_demo) return {'code': code, 'joined': true};
    return Map<String, dynamic>.from(
      (await _dio.post('/mini-leagues/join', data: {'code': code})).data,
    );
  }

  Future<Map<String, dynamic>> providerHealth() async {
    if (_demo) return {'status': 'demo', 'provider': 'ScoreTime Preview'};
    return Map<String, dynamic>.from((await _dio.get('/provider/health')).data);
  }

  Future<void> predict(int id, int h, int a) async {
    if (_demo) return;
    await _dio.post(
      '/matches/$id/prediction',
      data: {'home_score': h, 'away_score': a},
    );
  }

  Future<Map<String, dynamic>> visualMatch(int id) async {
    if (_demo) return {'match': await match(id), 'timeline': [], 'shots': []};
    return Map<String, dynamic>.from(
      (await _dio.get('/matches/$id/visual')).data['data'],
    );
  }

  Future<Map<String, dynamic>> teamHub(int id) async {
    if (_demo) return {'id': id, 'name_en': 'ScoreTime Demo Team'};
    return Map<String, dynamic>.from(
      (await _dio.get('/teams/$id/hub')).data['data'],
    );
  }

  Future<Map<String, dynamic>> suggestions(String q) async {
    if (_demo) return await search(q);
    return Map<String, dynamic>.from(
      (await _dio.get('/search/suggestions', queryParameters: {'q': q}))
          .data['data'],
    );
  }

  Future<List<dynamic>> trending() async {
    if (_demo) return ['Champions League', 'Transfers', 'Live football'];
    return List<dynamic>.from((await _dio.get('/search/trending')).data['data']);
  }

  Future<Map<String, dynamic>> personalizedFeed() async {
    if (_demo) {
      return {
        'matches': DemoData.matches,
        'news': DemoData.news,
      };
    }
    return Map<String, dynamic>.from(
      (await _dio.get('/personalized-feed')).data['data'],
    );
  }

  Future<Map<String, dynamic>> premiumStatus() async {
    if (_demo) return {'active': false, 'plan': 'preview'};
    return Map<String, dynamic>.from(
      (await _dio.get('/premium/status')).data['data'],
    );
  }

  Future<List<dynamic>> challenges() async {
    if (_demo) return [];
    return List<dynamic>.from((await _dio.get('/challenges')).data['data']);
  }

  Future<void> createChallenge(String username, String title) async {
    if (_demo) return;
    await _dio.post(
      '/challenges',
      data: {'username': username, 'title': title},
    );
  }

  Future<Map<String, dynamic>> level() async {
    if (_demo) return {'level': 1, 'xp': 0};
    return Map<String, dynamic>.from((await _dio.get('/level')).data['data']);
  }

  Future<Map<String, dynamic>> realtime(int id, {int after = 0}) async {
    if (_demo) return {'events': [], 'cursor': after};
    return Map<String, dynamic>.from(
      (await _dio.get(
        '/matches/$id/realtime',
        queryParameters: {'after': after},
      ))
          .data['data'],
    );
  }

  Future<Map<String, dynamic>> comparePlayers(int a, int b) async {
    if (_demo) return {'left': DemoData.players.first, 'right': DemoData.players[1]};
    return Map<String, dynamic>.from(
      (await _dio.get('/players/$a/compare/$b')).data['data'],
    );
  }

  Future<List<dynamic>> heatmap(int matchId, int playerId) async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/matches/$matchId/players/$playerId/heatmap'))
          .data['data'],
    );
  }

  Future<Map<String, dynamic>> transferIntelligence({
    String status = 'all',
  }) async {
    if (_demo) {
      final items = status == 'all'
          ? DemoData.transfers
          : DemoData.transfers.where((t) => t['status'] == status).toList();
      return {
        'filters': ['all', 'watch', 'developing'],
        'items': items,
      };
    }
    return Map<String, dynamic>.from(
      (await _dio.get(
        '/transfer-intelligence',
        queryParameters: {'status': status},
      ))
          .data['data'],
    );
  }

  Future<List<dynamic>> predictionSeasons() async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/prediction-seasons')).data['data'],
    );
  }

  Future<List<dynamic>> predictionSeasonLeaderboard(int id) async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/prediction-seasons/$id/leaderboard')).data['data'],
    );
  }

  Future<List<dynamic>> personalizedNews() async {
    if (_demo) return List<dynamic>.from(DemoData.news);
    return List<dynamic>.from(
      (await _dio.get('/personalized-news')).data['data'],
    );
  }

  Future<void> articleSignal(int id, String event) async {
    if (_demo) return;
    await _dio.post('/news/$id/signal', data: {'event': event});
  }

  Future<List<dynamic>> friendActivity() async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/friend-activity')).data['data'],
    );
  }

  Future<Map<String, dynamic>> player(int id) async {
    if (_demo) {
      final raw = DemoData.players.firstWhere(
        (p) => p['id'] == id,
        orElse: () => DemoData.players.first,
      );
      return Map<String, dynamic>.from(raw);
    }
    return Map<String, dynamic>.from(
      (await _dio.get('/players/$id')).data['data'],
    );
  }

  Future<List<dynamic>> friends() async {
    if (_demo) return [];
    return List<dynamic>.from((await _dio.get('/friends')).data['data']);
  }

  Future<void> addFriend(String username) async {
    if (_demo) return;
    await _dio.post('/friends', data: {'username': username});
  }

  Future<List<dynamic>> achievements() async {
    if (_demo) return [];
    return List<dynamic>.from(
      (await _dio.get('/achievements')).data['data'],
    );
  }

  Future<List<dynamic>> sessions() async {
    if (_demo) return [];
    return List<dynamic>.from((await _dio.get('/sessions')).data['data']);
  }

  Future<void> revokeSession(int id) async {
    if (_demo) return;
    await _dio.delete('/sessions/$id');
  }

  Future<void> followTeam(int id) async {
    if (_demo) return;
    await _dio.put(
      '/teams/$id/follow',
      data: {'news': true, 'matches': true, 'transfers': true},
    );
  }

  Future<void> subscribeMatch(int id) async {
    if (_demo) return;
    await _dio.put(
      '/matches/$id/subscribe',
      data: {
        'goal': true,
        'lineup': true,
        'red_card': true,
        'kickoff': true,
        'full_time': true,
      },
    );
  }

  Future<Map<String, dynamic>> worldSummary() async {
    if (_demo) return Map<String, dynamic>.from(DemoData.worldSummary);
    return Map<String, dynamic>.from(
      (await _dio.get('/world/summary')).data,
    );
  }

  Future<List<dynamic>> worldCountries() async {
    if (_demo) {
      return ['England', 'Spain', 'Palestine', 'Australia']
          .map((name) => {'name': name})
          .toList();
    }
    return List<dynamic>.from(
      (await _dio.get('/world/countries')).data['data'] ?? [],
    );
  }

  Future<List<dynamic>> worldCompetitions({
    String q = '',
    String? country,
    String? type,
  }) async {
    if (_demo) {
      Iterable<Map<String, dynamic>> items = DemoData.competitions;
      if (q.isNotEmpty) {
        final needle = q.toLowerCase();
        items = items.where(
          (c) => '${c['name_en']}'.toLowerCase().contains(needle),
        );
      }
      if (country != null) {
        items = items.where((c) => c['country'] == country);
      }
      if (type != null) {
        items = items.where((c) => c['type'] == type);
      }
      return items.toList();
    }
    return List<dynamic>.from(
      (await _dio.get(
        '/world/competitions',
        queryParameters: {
          if (q.isNotEmpty) 'q': q,
          if (country != null) 'country': country,
          if (type != null) 'type': type,
        },
      ))
          .data['data'] ?? [],
    );
  }

  Future<List<dynamic>> worldTeams({
    String q = '',
    String? country,
    String? type,
  }) async {
    if (_demo) {
      return [
        {'id': 1, 'name_en': 'Northbridge FC', 'country': 'England'},
        {'id': 2, 'name_en': 'Atlas United', 'country': 'Spain'},
        {'id': 3, 'name_en': 'Falcon SC', 'country': 'Palestine'},
      ];
    }
    return List<dynamic>.from(
      (await _dio.get(
        '/world/teams',
        queryParameters: {
          if (q.isNotEmpty) 'q': q,
          if (country != null) 'country': country,
          if (type != null) 'type': type,
        },
      ))
          .data['data'] ?? [],
    );
  }

  Future<List<dynamic>> worldPlayers({
    String q = '',
    int? teamId,
    String? position,
    String? nationality,
  }) async {
    if (_demo) {
      Iterable<Map<String, dynamic>> items = DemoData.players;
      if (q.isNotEmpty) {
        final needle = q.toLowerCase();
        items = items.where(
          (p) => '${p['name']}'.toLowerCase().contains(needle),
        );
      }
      if (position != null) {
        items = items.where((p) => p['position'] == position);
      }
      if (nationality != null) {
        items = items.where((p) => p['nationality'] == nationality);
      }
      return items.toList();
    }
    return List<dynamic>.from(
      (await _dio.get(
        '/world/players',
        queryParameters: {
          if (q.isNotEmpty) 'q': q,
          if (teamId != null) 'team_id': teamId,
          if (position != null) 'position': position,
          if (nationality != null) 'nationality': nationality,
        },
      ))
          .data['data'] ?? [],
    );
  }

  Future<Map<String, dynamic>> dataStatus() async {
    if (_demo) {
      return {
        'provider': {'ok': true, 'provider': 'preview', 'configured': false},
        'freshness': {},
        'catalog': {
          'teams': DemoData.worldSummary['teams'] ?? 0,
          'players': DemoData.worldSummary['players'] ?? 0,
          'matches': DemoData.matches.length,
          'articles': DemoData.news.length,
        },
      };
    }
    return Map<String, dynamic>.from(
      (await _dio.get('/data-status')).data['data'],
    );
  }
}
