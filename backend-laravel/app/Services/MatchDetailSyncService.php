<?php

namespace App\Services;

use App\Models\DataProviderSyncLog;
use App\Models\FootballMatch;
use App\Models\LiveCommentary;
use Illuminate\Support\Str;

class MatchDetailSyncService
{
    public function __construct(private FootballProviderManager $manager, private ProviderQuotaService $quota) {}

    public function sync(FootballMatch $match): array
    {
        if (!$match->provider_id) {
            throw new \InvalidArgumentException('The match has no provider_id and cannot be synchronized.');
        }

        $provider = $this->manager->driver();
        if (!$this->quota->canSpend($provider->name(), 'details', 3)) {
            throw new \RuntimeException('ScoreTime details quota does not have three protected requests available.');
        }
        $started = now();
        $clock = microtime(true);

        try {
            // Three cache-backed calls. API-Football requests are also protected by
            // the independent details bucket before any network request is made.
            $lineups = $provider->lineups($match->provider_id);
            $statistics = $provider->statistics($match->provider_id);
            $events = $provider->events($match->provider_id);
            $created = 0;

            foreach ($events as $event) {
                $fingerprint = hash('sha256', json_encode([
                    $match->provider_id,
                    data_get($event, 'time.elapsed'),
                    data_get($event, 'time.extra'),
                    data_get($event, 'team.id'),
                    data_get($event, 'player.id'),
                    data_get($event, 'type'),
                    data_get($event, 'detail'),
                ]));

                $commentary = LiveCommentary::firstOrCreate(
                    [
                        'football_match_id' => $match->id,
                        'provider_event_id' => $fingerprint,
                    ],
                    [
                        'minute' => data_get($event, 'time.elapsed'),
                        'stoppage' => data_get($event, 'time.extra', 0) ?? 0,
                        'type' => Str::slug((string) data_get($event, 'type', 'event'), '_'),
                        'text' => trim(implode(' — ', array_filter([
                            data_get($event, 'player.name'),
                            data_get($event, 'type'),
                            data_get($event, 'detail'),
                        ]))),
                        'importance' => in_array(strtolower((string) data_get($event, 'type')), ['goal', 'card'], true) ? 5 : 2,
                        'payload' => $event,
                    ]
                );
                if ($commentary->wasRecentlyCreated) $created++;
            }

            $match->update([
                'lineups' => $lineups,
                'stats' => $statistics,
                'events' => $events,
                'revision' => ((int) $match->revision) + 1,
                'last_synced_at' => now(),
            ]);

            DataProviderSyncLog::create([
                'provider' => $provider->name(),
                'resource' => 'fixture-details',
                'status' => 'success',
                'records' => count($events),
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'meta' => ['match_id' => $match->id, 'lineups' => count($lineups), 'statistics' => count($statistics), 'new_events' => $created],
                'started_at' => $started,
                'finished_at' => now(),
            ]);

            return ['match_id' => $match->id, 'lineups' => count($lineups), 'statistics' => count($statistics), 'events' => count($events), 'new_events' => $created];
        } catch (\Throwable $e) {
            DataProviderSyncLog::create([
                'provider' => $provider->name(),
                'resource' => 'fixture-details',
                'status' => 'failed',
                'records' => 0,
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'message' => $e->getMessage(),
                'meta' => ['match_id' => $match->id],
                'started_at' => $started,
                'finished_at' => now(),
            ]);
            throw $e;
        }
    }
}
