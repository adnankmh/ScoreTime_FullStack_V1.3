<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\{FootballMatch,FriendActivity,Prediction}; use Illuminate\Http\Request;
class PredictionController extends Controller {
 public function store(Request $r,FootballMatch $footballMatch){abort_if($footballMatch->kickoff_at->isPast(),422,'Predictions are locked.');$d=$r->validate(['home_score'=>'required|integer|min:0|max:20','away_score'=>'required|integer|min:0|max:20']);$p=Prediction::updateOrCreate(['user_id'=>$r->user()->id,'football_match_id'=>$footballMatch->id],$d);FriendActivity::create(['user_id'=>$r->user()->id,'type'=>'prediction_made','subject_type'=>FootballMatch::class,'subject_id'=>$footballMatch->id,'meta'=>$d]);return response()->json(['data'=>$p],201);}
 public function leaderboard(){return response()->json(['data'=>Prediction::selectRaw('user_id, SUM(points) total_points')->with('user:id,name,username')->groupBy('user_id')->orderByDesc('total_points')->limit(100)->get()]);}
}
