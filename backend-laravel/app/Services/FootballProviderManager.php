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
        return match (config('football.provider', 'demo')) {
            'api-football' => app(ApiFootballProvider::class),
            'football-data' => app(FootballDataOrgProvider::class),
            default => app(DemoFootballProvider::class),
        };
    }

    public function driver(): FootballDataProvider { return $this->current(); }
    public function health(): array { return $this->current()->health(); }
}
