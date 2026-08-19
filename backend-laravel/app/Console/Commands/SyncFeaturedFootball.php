<?php

namespace App\Console\Commands;

use App\Models\Competition;
use App\Services\GlobalFootballCatalogService;
use Illuminate\Console\Command;

class SyncFeaturedFootball extends Command
{
    protected $signature = 'football:sync-featured {--league=} {--season=} {--player-pages=}';
    protected $description = 'Rotate one featured league per day and cache its teams, fixtures, table and a protected player-stat slice.';

    public function handle(GlobalFootballCatalogService $sync): int
    {
        try {
            $featured = config('football.featured_leagues', []);
            $league = (string) ($this->option('league') ?: ($featured[now('UTC')->dayOfYear % max(1, count($featured))] ?? ''));
            $season = (int) ($this->option('season') ?: config('football.catalog_season', now()->year));
            $pages = max(0, min(2, (int) ($this->option('player-pages') ?? config('football.free_catalog_player_pages', 2))));
            if ($league === '') throw new \RuntimeException('FOOTBALL_FEATURED_LEAGUES is empty.');

            if (!Competition::where('provider_id', $league)->exists()) {
                $sync->syncCompetitions(null, $season);
            }

            $result = $sync->syncLeague($league, $season, false);
            if ($pages > 0) {
                $result = array_merge($result, $sync->syncPlayerStats($league, $season, $pages));
            }
            $competition = Competition::where('provider_id', $league)->firstOrFail();
            $teams = $competition->teams()->whereNotNull('provider_id')->orderBy('teams.id')->get();
            if ($teams->isNotEmpty()) {
                $team = $teams[now('UTC')->dayOfYear % $teams->count()];
                $result = array_merge($result, $sync->syncTeamTransfers($team));
            }
            $result = array_merge($result, $sync->syncLeagueInjuries($league, $season));

            $this->info(json_encode(['league' => $league, 'season' => $season] + $result, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
