<?php
namespace App\Services\Providers;

use App\Contracts\FootballDataProvider;
use App\Services\ProviderQuotaService;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ApiFootballProvider implements FootballDataProvider
{
    private Client $http;

    public function __construct(private ProviderQuotaService $quota)
    {
        $this->http = new Client([
            'base_uri' => rtrim((string) config('football.base_url', 'https://v3.football.api-sports.io'), '/') . '/',
            'timeout' => 15,
            'connect_timeout' => 8,
            'headers' => [
                'x-apisports-key' => (string) config('football.key'),
                'Accept' => 'application/json',
                'User-Agent' => 'ScoreTime/1.7.0',
            ],
        ]);
    }

    public function name(): string { return 'api-football'; }
    public function countries(): array { return $this->get('countries', [], 86400); }
    public function leagues(array $filters = []): array { return $this->get('leagues', $filters, 21600); }
    public function teams(array $filters = []): array { return $this->get('teams', $filters, 21600); }
    public function squads(int|string $teamId): array { return $this->get('players/squads', ['team' => $teamId], 21600); }
    public function fixtures(array $filters = []): array
    {
        $ttl = isset($filters['live']) ? 60 : 600;
        return $this->get('fixtures', $filters, $ttl);
    }
    public function standings(int|string $competitionId, ?string $season = null): array
    {
        return $this->get('standings', array_filter([
            'league' => $competitionId,
            'season' => $season,
        ]), 1800);
    }
    public function lineups(int|string $fixtureId): array
    {
        return $this->get('fixtures/lineups', ['fixture' => $fixtureId], config('football.free_detail_cache_seconds', 1800));
    }
    public function events(int|string $fixtureId): array
    {
        return $this->get('fixtures/events', ['fixture' => $fixtureId], 300);
    }
    public function statistics(int|string $fixtureId): array
    {
        return $this->get('fixtures/statistics', ['fixture' => $fixtureId], config('football.free_detail_cache_seconds', 1800));
    }
    public function fixturePlayers(int|string $fixtureId): array
    {
        return $this->get('fixtures/players', ['fixture' => $fixtureId], config('football.free_detail_cache_seconds', 1800));
    }
    public function players(array $filters = []): array { return $this->get('players', $filters, 21600); }
    public function transfers(array $filters = []): array { return $this->get('transfers', $filters, 21600); }
    public function injuries(array $filters = []): array { return $this->get('injuries', $filters, 3600); }
    public function coaches(array $filters = []): array { return $this->get('coachs', $filters, 21600); }
    public function topScorers(int|string $league, int|string $season): array
    {
        return $this->get('players/topscorers', ['league' => $league, 'season' => $season], 3600);
    }
    public function topAssists(int|string $league, int|string $season): array
    {
        return $this->get('players/topassists', ['league' => $league, 'season' => $season], 3600);
    }
    public function predictions(int|string $fixtureId): array
    {
        return $this->get('predictions', ['fixture' => $fixtureId], 21600);
    }
    public function trophies(array $filters = []): array { return $this->get('trophies', $filters, 86400); }
    public function sidelined(array $filters = []): array { return $this->get('sidelined', $filters, 21600); }

    public function health(): array
    {
        if (!config('football.key')) {
            return [
                'ok' => false,
                'provider' => $this->name(),
                'configured' => false,
                'message' => 'Add a free API-Football key to enable real data.',
                'quota' => $this->quota->state($this->name()),
            ];
        }

        $quota = $this->quota->state($this->name());
        return [
            'ok' => $quota['last_request_ok'] ?? null,
            'provider' => $this->name(),
            'configured' => true,
            'mode' => 'passive',
            'message' => $quota['last_error'] ?? 'Configured. Waiting for the first scheduled synchronization.',
            'quota' => $quota,
        ];
    }

    private function get(string $endpoint, array $query = [], int $ttl = 0): array
    {
        if (!config('football.key')) {
            throw new \RuntimeException('FOOTBALL_DATA_API_KEY is missing.');
        }

        ksort($query);
        $cacheKey = 'scoretime:api-football:'.hash('sha256', $endpoint.'?'.http_build_query($query));

        $resolver = function () use ($endpoint, $query) {
            $this->quota->beforeCall($this->name(), $this->bucket($endpoint, $query));

            try {
                $response = $this->http->get($endpoint, ['query' => $query]);
                $this->quota->syncHeaders($this->name(), $response->getHeaders());

                $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                if (!empty($json['errors'])) {
                    throw new \RuntimeException(
                        'API-Football error: '.json_encode($json['errors'], JSON_UNESCAPED_UNICODE)
                    );
                }

                $this->quota->recordResult($this->name(), true);
                return $json['response'] ?? [];
            } catch (\Throwable $e) {
                $this->quota->recordResult($this->name(), false, $e->getMessage());
                throw $e;
            }
        };

        if ($ttl <= 0) {
            return $resolver();
        }

        return Cache::remember($cacheKey, now()->addSeconds($ttl), $resolver);
    }

    private function bucket(string $endpoint, array $query): string
    {
        if ($endpoint === 'fixtures' && array_key_exists('live', $query)) return 'live';
        if ($endpoint === 'fixtures' && array_key_exists('date', $query)) return 'schedule';
        if ($endpoint === 'fixtures') return 'catalog';
        if (str_starts_with($endpoint, 'fixtures/') || $endpoint === 'predictions') return 'details';
        if (in_array($endpoint, ['countries', 'leagues', 'teams', 'players', 'players/squads', 'standings', 'players/topscorers', 'players/topassists', 'transfers', 'injuries', 'coachs', 'trophies', 'sidelined'], true)) return 'catalog';
        return 'other';
    }
}
