<?php
return [
    'release' => '1.3.0',
    'production' => [
        'require_https' => env('SCORETIME_REQUIRE_HTTPS', true),
        'require_admin_2fa' => env('SCORETIME_REQUIRE_ADMIN_2FA', true),
        'health_token' => env('SCORETIME_HEALTH_TOKEN'),
        'maintenance_contact' => env('SCORETIME_MAINTENANCE_CONTACT'),
    ],
    'features' => [
        'newsroom' => true,
        'tv_guide' => true,
        'smart_alerts' => true,
        'calendar' => true,
        'brackets' => true,
        'national_teams' => true,
        'womens_football' => true,
        'team_of_week' => true,
        'player_radar' => true,
        'match_story' => true,
        'deep_links' => true,
        'share_cards' => true,
        'dynamic_onboarding' => true,
    ],
];
