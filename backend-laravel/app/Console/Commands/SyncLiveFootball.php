<?php
namespace App\Console\Commands;

use App\Services\RealtimeFootballSyncService;
use App\Models\FootballMatch;
use Illuminate\Console\Command;

class SyncLiveFootball extends Command
{
    protected $signature = 'football:sync-live {--events : Also synchronize live goals/cards/substitutions} {--force : Call the provider even when no local fixture is in the live window}';
    protected $description = 'Synchronize all live fixtures globally and create missing competitions/teams automatically.';

    public function handle(RealtimeFootballSyncService $sync): int
    {
        try {
            if (config('football.free_plan_mode', true) && !$this->option('force') && !$this->hasLiveCandidate()) {
                $this->info('Skipped: no locally cached fixture is inside the live window. Zero provider requests used.');
                return self::SUCCESS;
            }
            $result = $sync->sync(['live' => 'all'], (bool) $this->option('events'));
            $this->info(json_encode($result, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }

    private function hasLiveCandidate(): bool
    {
        $from = now()->subMinutes((int) config('football.free_live_window_before_minutes', 210));
        $until = now()->addMinutes((int) config('football.free_live_window_after_minutes', 20));

        return FootballMatch::query()
            ->whereBetween('kickoff_at', [$from, $until])
            ->whereNotIn('status', ['finished', 'cancelled', 'postponed', 'abandoned'])
            ->exists();
    }
}
