<?php
return [
 'name' => 'ScoreTime',
 'default_locale' => 'en',
 'locales' => ['en','ar','fr','es','de','tr'],
 'news' => [
   'enabled' => env('NEWS_INGEST_ENABLED', false),
   'rewrite_mode' => env('NEWS_REWRITE_MODE', 'editorial-summary'),
   'require_source_url' => true,
   'require_attribution' => true,
   'auto_publish' => env('NEWS_AUTO_PUBLISH', false),
 ],
];
