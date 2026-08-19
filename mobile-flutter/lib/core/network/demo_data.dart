class DemoData {
  static final List<Map<String, dynamic>> matches = [
    {
      'id': 9001,
      'status': 'live',
      'minute': 67,
      'home_score': 2,
      'away_score': 1,
      'venue': 'ScoreTime Arena',
      'competition': {'name_en': 'Global Champions League'},
      'home_team': {'name_en': 'Northbridge FC'},
      'away_team': {'name_en': 'Atlas United'},
    },
    {
      'id': 9002,
      'status': 'scheduled',
      'minute': null,
      'home_score': 0,
      'away_score': 0,
      'venue': 'Capital Stadium',
      'competition': {'name_en': 'Premier Division'},
      'home_team': {'name_en': 'Royal City'},
      'away_team': {'name_en': 'Blue Coast'},
    },
    {
      'id': 9003,
      'status': 'halftime',
      'minute': 45,
      'home_score': 1,
      'away_score': 1,
      'venue': 'Continental Park',
      'competition': {'name_en': 'Continental Cup'},
      'home_team': {'name_en': 'Falcon SC'},
      'away_team': {'name_en': 'Nova Athletic'},
    },
    {
      'id': 9004,
      'status': 'scheduled',
      'minute': null,
      'home_score': 0,
      'away_score': 0,
      'venue': 'Metro Football Centre',
      'competition': {'name_en': 'Elite League'},
      'home_team': {'name_en': 'Metro Stars'},
      'away_team': {'name_en': 'Union Eleven'},
    },
  ];

  static final List<Map<String, dynamic>> news = [
    {
      'id': 7001,
      'title': 'Matchday intelligence: the tactical trends shaping tonight’s games',
      'excerpt': 'A ScoreTime preview built around form, momentum and matchup context.',
      'category': 'Match Intelligence',
      'author_name': 'ScoreTime Editorial',
      'is_breaking': true,
    },
    {
      'id': 7002,
      'title': 'Transfer radar: five profiles drawing attention across Europe',
      'excerpt': 'A concise look at player roles, fit and market movement.',
      'category': 'Transfers',
      'author_name': 'ScoreTime Editorial',
      'is_breaking': false,
    },
    {
      'id': 7003,
      'title': 'The numbers behind the weekend’s most efficient attacks',
      'excerpt': 'Chance creation, shot quality and final-third efficiency explained.',
      'category': 'Analytics',
      'author_name': 'ScoreTime Data Desk',
      'is_breaking': false,
    },
    {
      'id': 7004,
      'title': 'Fan pulse: fixtures generating the biggest global conversation',
      'excerpt': 'What supporters are following and why these matches matter.',
      'category': 'Fan Hub',
      'author_name': 'ScoreTime',
      'is_breaking': false,
    },
  ];

  static const Map<String, dynamic> worldSummary = {
    'countries': 211,
    'competitions': 1250,
    'teams': 18500,
    'players': 310000,
  };

  static final List<Map<String, dynamic>> competitions = [
    {'id': 1, 'name_en': 'Global Champions League', 'country': 'International', 'type': 'cup', 'is_international': true},
    {'id': 2, 'name_en': 'Premier Division', 'country': 'England', 'type': 'league', 'is_international': false},
    {'id': 3, 'name_en': 'Iberian Elite League', 'country': 'Spain', 'type': 'league', 'is_international': false},
    {'id': 4, 'name_en': 'Continental Nations Cup', 'country': 'International', 'type': 'cup', 'is_international': true},
  ];

  static final List<Map<String, dynamic>> players = [
    {'id': 1, 'name': 'Adam Vale', 'position': 'Forward', 'nationality': 'England', 'rating': 8.4, 'team': {'name_en': 'Northbridge FC'}},
    {'id': 2, 'name': 'Leo Marin', 'position': 'Midfielder', 'nationality': 'Spain', 'rating': 8.1, 'team': {'name_en': 'Atlas United'}},
    {'id': 3, 'name': 'Noah Carter', 'position': 'Goalkeeper', 'nationality': 'Australia', 'rating': 7.9, 'team': {'name_en': 'Royal City'}},
    {'id': 4, 'name': 'Sami Kareem', 'position': 'Defender', 'nationality': 'Palestine', 'rating': 8.0, 'team': {'name_en': 'Falcon SC'}},
  ];

  static final List<Map<String, dynamic>> transfers = [
    {
      'id': 1,
      'headline': 'Northbridge tracking a high-intensity central midfielder',
      'status': 'watch',
      'confidence': 74,
      'source_name': 'ScoreTime Transfer Desk',
      'player': {'name': 'Leo Marin'},
      'from_team': {'name_en': 'Atlas United'},
      'to_team': {'name_en': 'Northbridge FC'},
    },
    {
      'id': 2,
      'headline': 'Royal City exploring a versatile attacking option',
      'status': 'developing',
      'confidence': 62,
      'source_name': 'ScoreTime Transfer Desk',
      'player': {'name': 'Adam Vale'},
      'from_team': {'name_en': 'Northbridge FC'},
      'to_team': {'name_en': 'Royal City'},
    },
  ];
}
