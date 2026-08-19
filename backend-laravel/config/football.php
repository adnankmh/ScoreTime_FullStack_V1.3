<?php
return [
    'provider' => env('FOOTBALL_PROVIDER', env('FOOTBALL_DATA_PROVIDER', 'demo')),
    'key' => env('FOOTBALL_DATA_API_KEY', env('FOOTBALL_PROVIDER_KEY')),
    'base_url' => env('FOOTBALL_DATA_BASE_URL', env('FOOTBALL_PROVIDER_BASE_URL', 'https://v3.football.api-sports.io')),

    'secondary_key' => env('FOOTBALL_DATA_ORG_KEY'),
    'secondary_base_url' => env('FOOTBALL_DATA_ORG_BASE_URL', 'https://api.football-data.org/v4'),

    'sync_enabled' => filter_var(env('FOOTBALL_SYNC_ENABLED', false), FILTER_VALIDATE_BOOL),
    'live_refresh_seconds' => (int) env('FOOTBALL_LIVE_REFRESH_SECONDS', 30),
    'cache_seconds' => (int) env('FOOTBALL_CACHE_SECONDS', 60),
    'catalog_scheduler' => filter_var(env('FOOTBALL_CATALOG_SCHEDULER', false), FILTER_VALIDATE_BOOL),
    'catalog_season' => (int) env('FOOTBALL_CATALOG_SEASON', date('Y')),
    'featured_leagues' => array_values(array_filter(array_map('trim', explode(',', (string) env(
        'FOOTBALL_FEATURED_LEAGUES',
        '39,140,135,78,61,2,3,1'
    ))))),
];
