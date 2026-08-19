<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\FootballMatch; use App\Services\MatchIntelligenceService;
class MatchIntelligenceController extends Controller {public function __construct(private MatchIntelligenceService $service){} public function show(FootballMatch $footballMatch){return response()->json(['data'=>$this->service->package($footballMatch)]);} }
