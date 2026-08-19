<?php

namespace App\Services;

use App\Models\FootballMatch;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

class FootballDataService
{
    public function matchesForDate(?string $date = null, array $filters = []): Collection
    {
        $day = $date ? Carbon::createFromFormat('Y-m-d', $date) : now();
        $version = (int) Cache::get('scoretime:matches:version:'.$day->toDateString(), 1);
        $cacheKey = 'scoretime:matches:'.$day->toDateString().':'.$version.':'.hash('sha256', json_encode($filters));

        return Cache::remember($cacheKey, now()->addSeconds((int) config('football.cache_seconds', 120)), function () use ($day, $filters) {
            return FootballMatch::with(['competition', 'homeTeam', 'awayTeam'])
                ->whereDate('kickoff_at', $day->toDateString())
                ->when($filters['status'] ?? null, fn ($query, $status) => $query->where('status', $status))
                ->when($filters['competition_id'] ?? null, fn ($query, $id) => $query->where('competition_id', $id))
                ->when($filters['team_id'] ?? null, fn ($query, $id) => $query->where(
                    fn ($teams) => $teams->where('home_team_id', $id)->orWhere('away_team_id', $id)
                ))
                ->orderBy('kickoff_at')
                ->get();
        });
    }

    public function live(): Collection
    {
        $version = (int) Cache::get('scoretime:matches:version:live', 1);
        return Cache::remember('scoretime:matches:live:'.$version, now()->addSeconds(30), fn () =>
            FootballMatch::with(['competition', 'homeTeam', 'awayTeam'])
                ->whereIn('status', ['live', 'halftime'])
                ->orderBy('kickoff_at')
                ->get()
        );
    }
}
