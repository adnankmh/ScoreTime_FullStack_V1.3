<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\FootballMatch;
use App\Services\FootballDataService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MatchController extends Controller
{
    public function __construct(private FootballDataService $football) {}

    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'],
            'status' => ['nullable', 'in:scheduled,live,halftime,finished,postponed,cancelled,abandoned,suspended'],
            'competition_id' => ['nullable', 'integer', 'min:1'],
            'team_id' => ['nullable', 'integer', 'min:1'],
        ]);

        $date = $validated['date'] ?? now()->toDateString();
        $matches = $this->football->matchesForDate($date, array_filter([
            'status' => $validated['status'] ?? null,
            'competition_id' => $validated['competition_id'] ?? null,
            'team_id' => $validated['team_id'] ?? null,
        ]));

        return response()->json([
            'data' => $matches,
            'meta' => [
                'date' => $date,
                'source' => 'scoretime_database',
                'generated_at' => now()->toIso8601String(),
                'latest_sync_at' => $matches->max('last_synced_at')?->toIso8601String(),
            ],
        ])->header('Cache-Control', 'public, max-age=30, stale-while-revalidate=120');
    }

    public function show(FootballMatch $footballMatch): JsonResponse
    {
        return response()->json([
            'data' => $footballMatch->load([
                'competition',
                'homeTeam',
                'awayTeam',
                'matchEvents.player',
                'matchEvents.team',
                'commentaries',
                'lineupEntries.player',
                'lineupEntries.team',
            ]),
            'meta' => ['source' => 'scoretime_database'],
        ])->header('Cache-Control', 'public, max-age=15, stale-while-revalidate=60');
    }
}
