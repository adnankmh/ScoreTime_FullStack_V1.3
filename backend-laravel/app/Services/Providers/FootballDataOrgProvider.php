<?php
namespace App\Services\Providers;

use App\Contracts\FootballDataProvider;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class FootballDataOrgProvider implements FootballDataProvider
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => rtrim((string) config('football.secondary_base_url', 'https://api.football-data.org/v4'), '/') . '/',
            'timeout' => 15,
            'headers' => [
                'X-Auth-Token' => (string) config('football.secondary_key'),
                'Accept' => 'application/json',
                'User-Agent' => 'ScoreTime/1.7.1',
            ],
        ]);
    }

    public function name(): string { return 'football-data'; }

    public function countries(): array { return []; }

    public function leagues(array $filters = []): array
    {
        $json = $this->get('competitions');
        return array_map(fn ($c) => [
            'league' => [
                'id' => $c['id'],
                'name' => $c['name'],
                'type' => strtolower((string) ($c['type'] ?? 'league')),
                'logo' => $c['emblem'] ?? null,
            ],
            'country' => [
                'name' => data_get($c, 'area.name', 'International'),
                'code' => data_get($c, 'area.code'),
                'flag' => data_get($c, 'area.flag'),
            ],
            'seasons' => !empty($c['currentSeason']) ? [[
                'year' => (int) substr((string) data_get($c, 'currentSeason.startDate', date('Y')), 0, 4),
                'start' => data_get($c, 'currentSeason.startDate'),
                'end' => data_get($c, 'currentSeason.endDate'),
                'current' => true,
                'coverage' => [],
            ]] : [],
        ], $json['competitions'] ?? []);
    }

    public function teams(array $filters = []): array { return []; }
    public function squads(int|string $teamId): array { return []; }

    public function fixtures(array $filters = []): array
    {
        $query = [];
        if (!empty($filters['date'])) {
            $query['dateFrom'] = $filters['date'];
            $query['dateTo'] = $filters['date'];
        }
        if (!empty($filters['live'])) $query['status'] = 'IN_PLAY,PAUSED';
        if (!empty($filters['status'])) $query['status'] = $filters['status'];
        $json = $this->get('matches', $query, (!empty($filters['status']) || !empty($filters['live'])) ? 60 : 600);
        return array_map(fn ($m) => [
            'fixture' => [
                'id' => $m['id'],
                'date' => $m['utcDate'],
                'status' => [
                    'short' => match($m['status'] ?? '') {
                        'IN_PLAY' => '2H',
                        'PAUSED' => 'HT',
                        'FINISHED' => 'FT',
                        'SCHEDULED', 'TIMED' => 'NS',
                        default => $m['status'] ?? 'NS',
                    },
                    'elapsed' => null,
                ],
                'venue' => ['name' => null],
            ],
            'league' => [
                'id' => data_get($m, 'competition.id'),
                'name' => data_get($m, 'competition.name'),
                'country' => data_get($m, 'area.name'),
                'logo' => data_get($m, 'competition.emblem'),
                'round' => 'Matchday ' . ($m['matchday'] ?? ''),
                'season' => (int) substr((string) data_get($m, 'season.startDate', date('Y')), 0, 4),
            ],
            'teams' => [
                'home' => [
                    'id' => data_get($m, 'homeTeam.id'),
                    'name' => data_get($m, 'homeTeam.name'),
                    'logo' => data_get($m, 'homeTeam.crest'),
                ],
                'away' => [
                    'id' => data_get($m, 'awayTeam.id'),
                    'name' => data_get($m, 'awayTeam.name'),
                    'logo' => data_get($m, 'awayTeam.crest'),
                ],
            ],
            'goals' => [
                'home' => data_get($m, 'score.fullTime.home'),
                'away' => data_get($m, 'score.fullTime.away'),
            ],
        ], $json['matches'] ?? []);
    }

    public function standings(int|string $competitionId, ?string $season = null): array { return []; }
    public function lineups(int|string $fixtureId): array { return []; }
    public function events(int|string $fixtureId): array { return []; }
    public function statistics(int|string $fixtureId): array { return []; }
    public function fixturePlayers(int|string $fixtureId): array { return []; }
    public function players(array $filters = []): array { return []; }
    public function transfers(array $filters = []): array { return []; }
    public function injuries(array $filters = []): array { return []; }
    public function coaches(array $filters = []): array { return []; }
    public function topScorers(int|string $league, int|string $season): array { return []; }
    public function topAssists(int|string $league, int|string $season): array { return []; }
    public function predictions(int|string $fixtureId): array { return []; }
    public function trophies(array $filters = []): array { return []; }
    public function sidelined(array $filters = []): array { return []; }

    public function health(): array
    {
        return [
            'ok' => Cache::get('scoretime:football-data:last-ok'),
            'provider' => $this->name(),
            'configured' => (bool) config('football.secondary_key'),
            'mode' => 'passive',
            'last_request_at' => Cache::get('scoretime:football-data:last-at'),
            'message' => Cache::get('scoretime:football-data:last-error'),
        ];
    }

    private function get(string $endpoint, array $query = [], int $ttl = 21600): array
    {
        if (!config('football.secondary_key')) {
            throw new \RuntimeException('FOOTBALL_DATA_ORG_KEY is missing.');
        }
        ksort($query);
        $key = 'scoretime:football-data:'.hash('sha256', $endpoint.'?'.http_build_query($query));
        return Cache::remember($key, now()->addSeconds($ttl), function () use ($endpoint, $query) {
            try {
                $response = $this->http->get($endpoint, ['query' => $query]);
                $data = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);
                Cache::put('scoretime:football-data:last-ok', true, now()->addDays(2));
                Cache::put('scoretime:football-data:last-at', now('UTC')->toIso8601String(), now()->addDays(2));
                Cache::forget('scoretime:football-data:last-error');
                return $data;
            } catch (\Throwable $e) {
                Cache::put('scoretime:football-data:last-ok', false, now()->addDays(2));
                Cache::put('scoretime:football-data:last-error', mb_substr($e->getMessage(), 0, 500), now()->addDays(2));
                throw $e;
            }
        });
    }
}
