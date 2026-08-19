<?php
namespace App\Services;

use App\Contracts\FootballDataProvider;
use App\Services\Providers\ApiFootballProvider;
use App\Services\Providers\DemoFootballProvider;
use App\Services\Providers\FootballDataOrgProvider;

class FootballProviderManager
{
    public function current(): FootballDataProvider
    {
        $configured = (string) config('football.provider', 'auto');

        if ($configured === 'auto') {
            if (config('football.key')) {
                return app(ApiFootballProvider::class);
            }
            if (config('football.secondary_key')) {
                return app(FootballDataOrgProvider::class);
            }
            return app(DemoFootballProvider::class);
        }

        return match ($configured) {
            'api-football' => app(ApiFootballProvider::class),
            'football-data' => app(FootballDataOrgProvider::class),
            default => app(DemoFootballProvider::class),
        };
    }

    public function driver(): FootballDataProvider
    {
        return $this->current();
    }

    public function health(): array
    {
        return $this->current()->health();
    }
}
