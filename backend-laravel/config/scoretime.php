<?php
return [
 'name' => 'ScoreTime',
 'default_locale' => 'en',
 'locales' => ['en','ar','fr','es','de','tr'],
 'seed_demo_data' => filter_var(env('SCORETIME_SEED_DEMO_DATA', false), FILTER_VALIDATE_BOOL),
 'news' => [
   'enabled' => env('NEWS_INGEST_ENABLED', false),
   'rewrite_mode' => env('NEWS_REWRITE_MODE', 'editorial-summary'),
   'require_source_url' => true,
   'require_attribution' => true,
   'auto_publish' => env('NEWS_AUTO_PUBLISH', false),
 ],
];
