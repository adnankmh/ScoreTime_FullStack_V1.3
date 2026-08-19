<?php

namespace App\Console\Commands;

use App\Models\FootballMatch;
use App\Services\MatchDetailSyncService;
use Illuminate\Console\Command;

class SyncPriorityMatchDetails extends Command
{
    protected $signature = 'football:sync-priority-details {match? : Local ScoreTime match id}';
    protected $description = 'Cache lineups, statistics and events for one priority match within the protected details quota.';

    public function handle(MatchDetailSyncService $sync): int
    {
        $match = $this->argument('match')
            ? FootballMatch::find($this->argument('match'))
            : FootballMatch::query()
                ->whereIn('status', ['live', 'halftime'])
                ->orderByRaw('last_synced_at IS NULL DESC')
                ->orderBy('last_synced_at')
                ->first();

        if (!$match) {
            $this->info('Skipped: no eligible match. Zero provider requests used.');
            return self::SUCCESS;
        }

        try {
            $this->info(json_encode($sync->sync($match), JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
