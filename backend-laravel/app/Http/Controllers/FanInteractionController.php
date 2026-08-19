<?php
namespace App\Http\Controllers;
use App\Models\{FanMessage,FootballMatch,Prediction};
use Illuminate\Http\Request;
class FanInteractionController extends Controller {
 public function prediction(Request $request, FootballMatch $footballMatch){abort_if($footballMatch->kickoff_at->isPast(),422,'Predictions are locked after kickoff.');$d=$request->validate(['home_score'=>'required|integer|min:0|max:20','away_score'=>'required|integer|min:0|max:20']);Prediction::updateOrCreate(['user_id'=>$request->user()->id,'football_match_id'=>$footballMatch->id],$d);return back()->with('ok','Prediction saved.');}
 public function message(Request $request, FootballMatch $footballMatch){$d=$request->validate(['body'=>'required|string|min:1|max:500']);FanMessage::create(['user_id'=>$request->user()->id,'football_match_id'=>$footballMatch->id,'body'=>$d['body']]);return back()->with('ok','Message posted.');}
}
