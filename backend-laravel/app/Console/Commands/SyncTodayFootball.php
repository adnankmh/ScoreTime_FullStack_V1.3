<?php
namespace App\Console\Commands;

use App\Services\RealtimeFootballSyncService;
use Illuminate\Console\Command;

class SyncTodayFootball extends Command
{
    protected $signature = 'football:sync-today {--date=}';
    protected $description = 'Synchronize the complete global match calendar for a date.';

    public function handle(RealtimeFootballSyncService $sync): int
    {
        try {
            $date = $this->option('date') ?: now()->toDateString();
            $result = $sync->sync(['date' => $date], false);
            $this->info(json_encode(['date' => $date] + $result, JSON_PRETTY_PRINT));
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error($e->getMessage());
            return self::FAILURE;
        }
    }
}
