<?php
return [
    'provider' => env('FOOTBALL_PROVIDER', 'auto'),

    'key' => env('FOOTBALL_DATA_API_KEY'),
    'base_url' => env('FOOTBALL_DATA_BASE_URL', 'https://v3.football.api-sports.io'),

    'secondary_key' => env('FOOTBALL_DATA_ORG_KEY'),
    'secondary_base_url' => env('FOOTBALL_DATA_ORG_BASE_URL', 'https://api.football-data.org/v4'),

    'free_plan_mode' => filter_var(env('FOOTBALL_FREE_PLAN_MODE', true), FILTER_VALIDATE_BOOL),
    'free_daily_limit' => (int) env('FOOTBALL_FREE_DAILY_LIMIT', 100),
    'free_daily_reserve' => (int) env('FOOTBALL_FREE_DAILY_RESERVE', 20),
    'free_live_daily_cap' => (int) env('FOOTBALL_FREE_LIVE_DAILY_CAP', 40),
    'free_detail_daily_cap' => (int) env('FOOTBALL_FREE_DETAIL_DAILY_CAP', 20),
    'free_catalog_daily_cap' => (int) env('FOOTBALL_FREE_CATALOG_DAILY_CAP', 8),
    'free_live_cron' => env('FOOTBALL_FREE_LIVE_CRON', '*/10 * * * *'),
    'free_today_cron' => env('FOOTBALL_FREE_TODAY_CRON', '5 0 * * *'),
    'free_detail_cron' => env('FOOTBALL_FREE_DETAIL_CRON', '13,43 * * * *'),
    'free_live_window_before_minutes' => (int) env('FOOTBALL_FREE_LIVE_WINDOW_BEFORE_MINUTES', 210),
    'free_live_window_after_minutes' => (int) env('FOOTBALL_FREE_LIVE_WINDOW_AFTER_MINUTES', 20),
    'free_detail_cache_seconds' => (int) env('FOOTBALL_FREE_DETAIL_CACHE_SECONDS', 1800),

    'sync_enabled' => filter_var(env('FOOTBALL_SYNC_ENABLED', false), FILTER_VALIDATE_BOOL),
    'cache_seconds' => (int) env('FOOTBALL_CACHE_SECONDS', 120),
    'catalog_scheduler' => filter_var(env('FOOTBALL_CATALOG_SCHEDULER', false), FILTER_VALIDATE_BOOL),
    'catalog_season' => (int) env('FOOTBALL_CATALOG_SEASON', date('Y')),
    'free_catalog_player_pages' => (int) env('FOOTBALL_FREE_CATALOG_PLAYER_PAGES', 2),
    'featured_leagues' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('FOOTBALL_FEATURED_LEAGUES', '39,140,135,78,61,2,3,1'))
    ))),
];
