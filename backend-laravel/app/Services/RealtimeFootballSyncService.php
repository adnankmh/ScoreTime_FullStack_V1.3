<?php
namespace App\Services;

use App\Models\Competition;
use App\Models\DataProviderSyncLog;
use App\Models\FootballCountry;
use App\Models\FootballMatch;
use App\Models\LiveCommentary;
use App\Models\Team;
use App\Support\FootballStatus;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class RealtimeFootballSyncService
{
    public function __construct(private FootballProviderManager $manager) {}

    public function sync(array $filters, bool $withEvents = false): array
    {
        $provider = $this->manager->driver();
        $started = now();
        $clock = microtime(true);
        $matches = 0;
        $events = 0;

        try {
            foreach ($provider->fixtures($filters) as $raw) {
                $fixtureId = data_get($raw, 'fixture.id');
                if (!$fixtureId) continue;

                $competition = $this->upsertCompetition($raw);
                $home = $this->upsertTeam(data_get($raw, 'teams.home', []));
                $away = $this->upsertTeam(data_get($raw, 'teams.away', []));
                if (!$competition || !$home || !$away) continue;

                $rawStatus = (string) data_get($raw, 'fixture.status.short', 'NS');
                $status = FootballStatus::canonical($rawStatus);
                $existing = FootballMatch::where('provider_id', (string) $fixtureId)->first();
                $broadcastMeta = array_replace($existing?->broadcast_meta ?? [], [
                    'provider' => $provider->name(),
                    'provider_status' => $rawStatus,
                ]);
                $match = FootballMatch::updateOrCreate(
                    ['provider_id' => (string) $fixtureId],
                    [
                        'competition_id' => $competition->id,
                        'home_team_id' => $home->id,
                        'away_team_id' => $away->id,
                        'kickoff_at' => data_get($raw, 'fixture.date'),
                        'status' => $status,
                        'minute' => data_get($raw, 'fixture.status.elapsed'),
                        'home_score' => (int) (data_get($raw, 'goals.home') ?? 0),
                        'away_score' => (int) (data_get($raw, 'goals.away') ?? 0),
                        'venue' => data_get($raw, 'fixture.venue.name'),
                        'round' => data_get($raw, 'league.round'),
                        'realtime_state' => FootballStatus::isLive($rawStatus) ? 'active' : 'idle',
                        'realtime_heartbeat_at' => now(),
                        'revision' => ((int) ($existing?->revision ?? 0)) + 1,
                        'broadcast_meta' => $broadcastMeta,
                        'last_synced_at' => now(),
                    ]
                );
                $matches++;

                if ($withEvents) {
                    foreach ($provider->events($fixtureId) as $event) {
                        $fingerprint = hash('sha256', json_encode([
                            $fixtureId,
                            data_get($event, 'time.elapsed'),
                            data_get($event, 'time.extra'),
                            data_get($event, 'team.id'),
                            data_get($event, 'player.id'),
                            data_get($event, 'type'),
                            data_get($event, 'detail'),
                        ]));

                        LiveCommentary::firstOrCreate(
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
                                'importance' => in_array(strtolower((string) data_get($event, 'type')), ['goal', 'card']) ? 5 : 2,
                                'payload' => $event,
                            ]
                        );
                        $events++;
                    }
                }
            }

            DataProviderSyncLog::create([
                'provider' => $provider->name(),
                'resource' => 'fixtures',
                'status' => 'success',
                'records' => $matches,
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'meta' => ['filters' => $filters, 'events' => $events],
                'started_at' => $started,
                'finished_at' => now(),
            ]);

            if (!empty($filters['date'])) {
                $key = 'scoretime:matches:version:'.(string) $filters['date'];
                Cache::add($key, 1, now()->addDays(2));
                Cache::increment($key);
            }
            Cache::add('scoretime:matches:version:live', 1, now()->addDays(2));
            Cache::increment('scoretime:matches:version:live');

            return ['matches' => $matches, 'events' => $events];
        } catch (\Throwable $e) {
            DataProviderSyncLog::create([
                'provider' => $provider->name(),
                'resource' => 'fixtures',
                'status' => 'failed',
                'records' => $matches,
                'duration_ms' => (int) ((microtime(true) - $clock) * 1000),
                'message' => $e->getMessage(),
                'meta' => ['filters' => $filters],
                'started_at' => $started,
                'finished_at' => now(),
            ]);
            throw $e;
        }
    }

    private function upsertCompetition(array $raw): ?Competition
    {
        $id = data_get($raw, 'league.id');
        $name = trim((string) data_get($raw, 'league.name'));
        if (!$id || $name === '') return null;

        $countryName = data_get($raw, 'league.country', 'International') ?: 'International';
        $country = FootballCountry::firstOrCreate(
            ['name' => $countryName],
            ['is_active' => true]
        );

        return Competition::updateOrCreate(
            ['provider_id' => (string) $id],
            [
                'football_country_id' => $country->id,
                'name_en' => $name,
                'name_ar' => $name,
                'slug' => Str::slug($name . '-' . $id),
                'country' => $countryName,
                'logo_url' => data_get($raw, 'league.logo'),
                'type' => 'league',
                'is_international' => strtolower($countryName) === 'world',
                'season' => (string) (data_get($raw, 'league.season') ?? ''),
                'last_synced_at' => now(),
            ]
        );
    }

    private function upsertTeam(array $raw): ?Team
    {
        $id = $raw['id'] ?? null;
        $name = trim((string) ($raw['name'] ?? ''));
        if (!$id || $name === '') return null;

        return Team::updateOrCreate(
            ['provider_id' => (string) $id],
            [
                'name_en' => $name,
                'name_ar' => $name,
                'slug' => Str::slug($name . '-' . $id),
                'logo_url' => $raw['logo'] ?? null,
                'last_synced_at' => now(),
            ]
        );
    }
}
