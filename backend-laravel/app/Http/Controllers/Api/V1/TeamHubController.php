<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\{Team,Player,PlayerInjury,Transfer,Standing};
class TeamHubController extends Controller{public function show(Team $team){return response()->json(['data'=>['team'=>$team,'squad'=>Player::where('team_id',$team->id)->orderBy('position')->orderBy('number')->get(),'injuries'=>PlayerInjury::with('player')->where('team_id',$team->id)->latest()->get(),'transfers'=>Transfer::with(['player','fromTeam','toTeam'])->where(fn($q)=>$q->where('from_team_id',$team->id)->orWhere('to_team_id',$team->id))->latest('transfer_date')->limit(30)->get(),'standings'=>Standing::with('competition')->where('team_id',$team->id)->get()]]);}}
