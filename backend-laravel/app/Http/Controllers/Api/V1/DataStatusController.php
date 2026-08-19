<?php
namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\DataProviderSyncLog;
use App\Models\FootballMatch;
use App\Models\Player;
use App\Models\Team;
use App\Services\FootballProviderManager;

class DataStatusController extends Controller
{
    public function __invoke(FootballProviderManager $manager)
    {
        return response()->json([
            'data' => [
                'provider' => $manager->health(),
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
