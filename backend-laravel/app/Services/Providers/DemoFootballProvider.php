<?php
namespace App\Services\Providers;

use App\Contracts\FootballDataProvider;

class DemoFootballProvider implements FootballDataProvider
{
    public function name(): string { return 'demo'; }
    public function countries(): array { return []; }
    public function leagues(array $filters = []): array { return []; }
    public function teams(array $filters = []): array { return []; }
    public function squads(int|string $teamId): array { return []; }
    public function fixtures(array $filters = []): array { return []; }
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
            'ok' => true,
            'provider' => 'demo',
            'configured' => false,
            'message' => 'Preview provider. Configure a licensed football provider for live global data.',
        ];
    }
}
