<?php
return [
    'provider' => env('NEWS_PROVIDER', 'newsapi'),
    'key' => env('NEWS_API_KEY'),
    'base_url' => env('NEWS_API_BASE_URL', 'https://newsapi.org/v2/'),
    'query' => env('NEWS_QUERY', 'football OR soccer'),
    'languages' => array_values(array_filter(array_map('trim', explode(',', env('NEWS_LANGUAGES', 'en,ar,fr,es,de'))))),
    'domains' => array_values(array_filter(array_map('trim', explode(',', env('NEWS_ALLOWED_DOMAINS', ''))))),
    'page_size' => (int) env('NEWS_PAGE_SIZE', 50),
    'auto_publish' => filter_var(env('NEWS_AUTO_PUBLISH', true), FILTER_VALIDATE_BOOL),
];
