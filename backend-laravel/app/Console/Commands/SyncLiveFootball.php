<?php
namespace App\Console\Commands;

use App\Services\RealtimeFootballSyncService;
use Illuminate\Console\Command;

class SyncLiveFootball extends Command
{
    protected $signature = 'football:sync-live {--events : Also synchronize live goals/cards/substitutions}';
    protected $description = 'Synchronize all live fixtures globally and create missing competitions/teams automatically.';

    public function handle(RealtimeFootballSyncService $sync): int
    {
        try {
            $result = $sync->sync(['live' => 'all'], (bool) $this->option('events'));
            $this->info(json_encode($result, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
