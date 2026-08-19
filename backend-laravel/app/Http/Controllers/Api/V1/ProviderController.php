<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Services\FootballProviderManager;
class ProviderController extends Controller { public function health(FootballProviderManager $m){return $m->health()+['live_refresh_seconds'=>config('football.live_refresh_seconds')];} }
