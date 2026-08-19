<?php
namespace App\Services;

use Illuminate\Support\Facades\Cache;

class ProviderQuotaService
{
    public function beforeCall(string $provider, string $bucket = 'other'): void
    {
        if (!$this->enabled($provider)) {
            return;
        }

        $key = $this->key($provider);
        Cache::lock($key.':lock', 5)->block(3, function () use ($provider, $bucket, $key) {
            $state = $this->state($provider);
            $usable = max(0, $state['limit'] - $state['reserve']);
            $reported = $state['provider_reported_remaining'];

            if ($state['used'] >= $usable || ($reported !== null && $reported <= $state['reserve'])) {
                throw new \RuntimeException(
                    "ScoreTime free-plan safety stop: {$provider} protected its final {$state['reserve']} daily requests."
                );
            }

            $bucketCap = $this->bucketCap($bucket);
            $bucketUsed = (int) Cache::get($this->bucketKey($provider, $bucket), 0);
            if ($bucketCap !== null && $bucketUsed >= $bucketCap) {
                throw new \RuntimeException(
                    "ScoreTime free-plan bucket stop: {$bucket} reached {$bucketUsed}/{$bucketCap} requests today."
                );
            }

            $expires = now('UTC')->addDays(2);
            Cache::add($key, 0, $expires);
            Cache::increment($key);
            Cache::add($this->bucketKey($provider, $bucket), 0, $expires);
            Cache::increment($this->bucketKey($provider, $bucket));
        });
    }

    public function recordResult(string $provider, bool $ok, ?string $message = null): void
    {
        $expires = now('UTC')->addDays(2);
        Cache::put($this->key($provider).':last-at', now('UTC')->toIso8601String(), $expires);
        Cache::put($this->key($provider).':last-ok', $ok, $expires);
        if ($ok) {
            Cache::forget($this->key($provider).':last-error');
        } elseif ($message) {
            Cache::put($this->key($provider).':last-error', mb_substr($message, 0, 500), $expires);
        }
    }

    public function canSpend(string $provider, string $bucket, int $requests = 1): bool
    {
        if (!$this->enabled($provider)) return true;
        $state = $this->state($provider);
        $usable = max(0, $state['limit'] - $state['reserve']);
        $bucketState = $state['buckets'][$bucket] ?? ['used' => 0, 'cap' => null];
        $withinDaily = $state['used'] + $requests <= $usable;
        $withinBucket = $bucketState['cap'] === null || $bucketState['used'] + $requests <= $bucketState['cap'];
        return $withinDaily && $withinBucket;
    }

    public function syncHeaders(string $provider, array $headers): void
    {
        if (!$this->enabled($provider)) {
            return;
        }

        $remaining = $this->headerInt($headers, 'x-ratelimit-requests-remaining');
        $limit = $this->headerInt($headers, 'x-ratelimit-requests-limit');

        if ($limit !== null) {
            Cache::put($this->key($provider).':reported-limit', $limit, now('UTC')->addDays(2));
        }
        if ($remaining !== null) {
            Cache::put($this->key($provider).':reported-remaining', $remaining, now('UTC')->addDays(2));
        }
    }

    public function state(string $provider = 'api-football'): array
    {
        $limit = (int) config('football.free_daily_limit', 100);
        $used = (int) Cache::get($this->key($provider), 0);
        $reportedLimit = Cache::get($this->key($provider).':reported-limit');
        $reportedRemaining = Cache::get($this->key($provider).':reported-remaining');

        if (is_numeric($reportedLimit)) {
            $limit = (int) $reportedLimit;
        }

        return [
            'provider' => $provider,
            'free_plan_mode' => $this->enabled($provider),
            'limit' => $limit,
            'reserve' => (int) config('football.free_daily_reserve', 20),
            'used' => $used,
            'estimated_remaining' => max(0, $limit - $used),
            'provider_reported_remaining' => is_numeric($reportedRemaining) ? (int) $reportedRemaining : null,
            'buckets' => [
                'live' => $this->bucketState($provider, 'live'),
                'details' => $this->bucketState($provider, 'details'),
                'schedule' => $this->bucketState($provider, 'schedule'),
                'catalog' => $this->bucketState($provider, 'catalog'),
                'other' => $this->bucketState($provider, 'other'),
            ],
            'last_request_at' => Cache::get($this->key($provider).':last-at'),
            'last_request_ok' => Cache::get($this->key($provider).':last-ok'),
            'last_error' => Cache::get($this->key($provider).':last-error'),
            'reset_timezone' => 'UTC',
            'date' => now('UTC')->toDateString(),
        ];
    }

    private function enabled(string $provider): bool
    {
        return $provider === 'api-football'
            && (bool) config('football.free_plan_mode', true);
    }

    private function key(string $provider): string
    {
        return 'scoretime:quota:'.$provider.':'.now('UTC')->toDateString();
    }

    private function bucketKey(string $provider, string $bucket): string
    {
        return $this->key($provider).':bucket:'.$bucket;
    }

    private function bucketCap(string $bucket): ?int
    {
        return match ($bucket) {
            'live' => (int) config('football.free_live_daily_cap', 40),
            'details' => (int) config('football.free_detail_daily_cap', 20),
            'catalog' => (int) config('football.free_catalog_daily_cap', 8),
            default => null,
        };
    }

    private function bucketState(string $provider, string $bucket): array
    {
        return [
            'used' => (int) Cache::get($this->bucketKey($provider, $bucket), 0),
            'cap' => $this->bucketCap($bucket),
        ];
    }

    private function headerInt(array $headers, string $name): ?int
    {
        foreach ($headers as $key => $value) {
            if (strtolower((string) $key) !== strtolower($name)) {
                continue;
            }
            $raw = is_array($value) ? ($value[0] ?? null) : $value;
            return is_numeric($raw) ? (int) $raw : null;
        }
        return null;
    }
}
