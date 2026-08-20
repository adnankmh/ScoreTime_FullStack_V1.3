<?php
namespace App\Services;

use GuzzleHttp\Client;

class NewsApiService
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => rtrim((string) config('news.base_url'), '/') . '/',
            'timeout' => 15,
            'headers' => [
                'X-Api-Key' => (string) config('news.key'),
                'Accept' => 'application/json',
                'User-Agent' => 'ScoreTime/1.7.3',
            ],
        ]);
    }

    public function configured(): bool
    {
        return (bool) config('news.key');
    }

    public function football(string $language = 'en'): array
    {
        if (!$this->configured()) {
            throw new \RuntimeException('NEWS_API_KEY is missing.');
        }

        $query = [
            'q' => config('news.query', 'football OR soccer'),
            'language' => $language,
            'sortBy' => 'publishedAt',
            'pageSize' => config('news.page_size', 50),
        ];

        $domains = config('news.domains', []);
        if ($domains) $query['domains'] = implode(',', $domains);

        $response = $this->http->get('everything', ['query' => $query]);
        $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (($json['status'] ?? 'error') !== 'ok') {
            throw new \RuntimeException((string) ($json['message'] ?? 'News provider error.'));
        }

        return $json['articles'] ?? [];
    }
}
