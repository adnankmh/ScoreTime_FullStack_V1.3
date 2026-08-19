<?php
return [
 'provider'=>env('FOOTBALL_PROVIDER',env('FOOTBALL_DATA_PROVIDER','demo')),
 'key'=>env('FOOTBALL_DATA_API_KEY',env('FOOTBALL_PROVIDER_KEY')),
 'base_url'=>env('FOOTBALL_DATA_BASE_URL',env('FOOTBALL_PROVIDER_BASE_URL','https://v3.football.api-sports.io')),
 'sync_enabled'=>(bool)env('FOOTBALL_SYNC_ENABLED',false),
 'live_refresh_seconds'=>(int)env('FOOTBALL_LIVE_REFRESH_SECONDS',30),
 'cache_seconds'=>(int)env('FOOTBALL_CACHE_SECONDS',60),
 'catalog_scheduler'=>(bool)env('FOOTBALL_CATALOG_SCHEDULER',false),
 'catalog_season'=>(int)env('FOOTBALL_CATALOG_SEASON',date('Y')),
];
