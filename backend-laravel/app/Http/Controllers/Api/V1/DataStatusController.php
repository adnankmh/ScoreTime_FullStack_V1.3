<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\DataProviderSyncLog;
use App\Models\FootballMatch;
use App\Models\Player;
use App\Models\Team;
use App\Services\FootballProviderManager;
use App\Services\ProviderQuotaService;

class DataStatusController extends Controller
{
    public function __invoke(
        FootballProviderManager $manager,
        ProviderQuotaService $quota
    ) {
        $provider = $manager->current();

        return response()->json([
            'data' => [
                'provider' => $provider->health(),
                'quota' => $quota->state($provider->name()),
                'free_plan_strategy' => [
                    'enabled' => (bool) config('football.free_plan_mode', true),
                    'live_cron' => config('football.free_live_cron'),
                    'today_cron' => config('football.free_today_cron'),
                    'detail_cache_seconds' => config('football.free_detail_cache_seconds'),
                    'detail_cron' => config('football.free_detail_cron'),
                    'live_is_database_gated' => true,
                    'live_daily_cap' => config('football.free_live_daily_cap'),
                    'detail_daily_cap' => config('football.free_detail_daily_cap'),
                    'catalog_daily_cap' => config('football.free_catalog_daily_cap'),
                    'public_health_uses_provider_request' => false,
                ],
                'last_syncs' => DataProviderSyncLog::latest()->limit(8)->get(),
                'freshness' => [
                    'latest_match_sync' => FootballMatch::max('last_synced_at'),
                    'latest_news' => Article::max('published_at'),
                ],
                'catalog' => [
                    'teams' => Team::count(),
                    'players' => Player::count(),
                    'matches' => FootballMatch::count(),
                    'articles' => Article::count(),
                ],
            ],
        ]);
    }
}
