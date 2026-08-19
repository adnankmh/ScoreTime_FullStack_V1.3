<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\FootballMatch; use App\Services\FootballDataService;
class MatchController extends Controller {public function __construct(private FootballDataService $football){} public function index(){return response()->json(['data'=>$this->football->matchesForDate(request('date'))]);} public function show(FootballMatch $footballMatch){return response()->json(['data'=>$footballMatch->load(['competition','homeTeam','awayTeam','matchEvents.player','matchEvents.team'])]);}}
