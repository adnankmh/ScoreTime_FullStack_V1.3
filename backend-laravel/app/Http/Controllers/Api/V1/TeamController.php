<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\Team;
class TeamController extends Controller {public function show(Team $team){return response()->json(['data'=>['team'=>$team,'recent_matches'=>$team->homeMatches()->with(['homeTeam','awayTeam','competition'])->latest('kickoff_at')->take(10)->get()->merge($team->awayMatches()->with(['homeTeam','awayTeam','competition'])->latest('kickoff_at')->take(10)->get())->sortByDesc('kickoff_at')->values()->take(10)]]);}}
