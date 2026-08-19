<?php
namespace App\Contracts;

interface FootballDataProvider
{
    public function name(): string;
    public function countries(): array;
    public function leagues(array $filters = []): array;
    public function teams(array $filters = []): array;
    public function squads(int|string $teamId): array;
    public function fixtures(array $filters = []): array;
    public function standings(int|string $competitionId, ?string $season = null): array;
    public function lineups(int|string $fixtureId): array;
    public function events(int|string $fixtureId): array;
    public function statistics(int|string $fixtureId): array;
    public function fixturePlayers(int|string $fixtureId): array;
    public function players(array $filters = []): array;
    public function transfers(array $filters = []): array;
    public function injuries(array $filters = []): array;
    public function coaches(array $filters = []): array;
    public function topScorers(int|string $league, int|string $season): array;
    public function topAssists(int|string $league, int|string $season): array;
    public function predictions(int|string $fixtureId): array;
    public function trophies(array $filters = []): array;
    public function sidelined(array $filters = []): array;
    public function health(): array;
}
