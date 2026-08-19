<?php
namespace App\Services\Providers;

use App\Contracts\FootballDataProvider;
use GuzzleHttp\Client;

class ApiFootballProvider implements FootballDataProvider
{
    private Client $http;

    public function __construct()
    {
        $this->http = new Client([
            'base_uri' => rtrim((string) config('football.base_url', 'https://v3.football.api-sports.io'), '/') . '/',
            'timeout' => 15,
            'connect_timeout' => 8,
            'headers' => [
                'x-apisports-key' => (string) config('football.key'),
                'Accept' => 'application/json',
                'User-Agent' => 'ScoreTime/1.6',
            ],
        ]);
    }

    public function name(): string { return 'api-football'; }
    public function countries(): array { return $this->get('countries'); }
    public function leagues(array $filters = []): array { return $this->get('leagues', $filters); }
    public function teams(array $filters = []): array { return $this->get('teams', $filters); }
    public function squads(int|string $teamId): array { return $this->get('players/squads', ['team' => $teamId]); }
    public function fixtures(array $filters = []): array { return $this->get('fixtures', $filters); }
    public function standings(int|string $competitionId, ?string $season = null): array
    {
        return $this->get('standings', array_filter(['league' => $competitionId, 'season' => $season]));
    }
    public function lineups(int|string $fixtureId): array { return $this->get('fixtures/lineups', ['fixture' => $fixtureId]); }
    public function events(int|string $fixtureId): array { return $this->get('fixtures/events', ['fixture' => $fixtureId]); }
    public function statistics(int|string $fixtureId): array { return $this->get('fixtures/statistics', ['fixture' => $fixtureId]); }
    public function fixturePlayers(int|string $fixtureId): array { return $this->get('fixtures/players', ['fixture' => $fixtureId]); }
    public function players(array $filters = []): array { return $this->get('players', $filters); }
    public function transfers(array $filters = []): array { return $this->get('transfers', $filters); }
    public function injuries(array $filters = []): array { return $this->get('injuries', $filters); }
    public function coaches(array $filters = []): array { return $this->get('coachs', $filters); }
    public function topScorers(int|string $league, int|string $season): array
    {
        return $this->get('players/topscorers', ['league' => $league, 'season' => $season]);
    }
    public function topAssists(int|string $league, int|string $season): array
    {
        return $this->get('players/topassists', ['league' => $league, 'season' => $season]);
    }
    public function predictions(int|string $fixtureId): array { return $this->get('predictions', ['fixture' => $fixtureId]); }
    public function trophies(array $filters = []): array { return $this->get('trophies', $filters); }
    public function sidelined(array $filters = []): array { return $this->get('sidelined', $filters); }

    public function health(): array
    {
        try {
            $status = $this->get('status');
            return [
                'ok' => true,
                'provider' => $this->name(),
                'configured' => (bool) config('football.key'),
                'status' => $status,
            ];
        } catch (\Throwable $e) {
            return [
                'ok' => false,
                'provider' => $this->name(),
                'configured' => (bool) config('football.key'),
                'message' => $e->getMessage(),
            ];
        }
    }

    private function get(string $endpoint, array $query = []): array
    {
        if (!config('football.key')) {
            throw new \RuntimeException('FOOTBALL_DATA_API_KEY is missing.');
        }

        $response = $this->http->get($endpoint, ['query' => $query]);
        $json = json_decode((string) $response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        if (!empty($json['errors'])) {
            throw new \RuntimeException('API-Football error: ' . json_encode($json['errors'], JSON_UNESCAPED_UNICODE));
        }

        return $json['response'] ?? [];
    }
}
