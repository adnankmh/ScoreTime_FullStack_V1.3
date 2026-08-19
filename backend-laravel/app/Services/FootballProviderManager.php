<?php
namespace App\Services;
use App\Contracts\FootballDataProvider;
use App\Services\Providers\{ApiFootballProvider,DemoFootballProvider};
class FootballProviderManager {
 public function current(): FootballDataProvider { return match(config('football.provider','demo')) { 'api-football' => app(ApiFootballProvider::class), default => app(DemoFootballProvider::class), }; }
 public function driver(): FootballDataProvider { return $this->current(); }
 public function health(): array { return $this->current()->health(); }
}
