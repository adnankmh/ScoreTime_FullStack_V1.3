<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\{Competition,Standing};
class CompetitionController extends Controller {public function index(){return response()->json(['data'=>Competition::orderBy('sort_order')->get()]);} public function show(Competition $competition){return response()->json(['data'=>['competition'=>$competition,'standings'=>Standing::with('team')->where('competition_id',$competition->id)->orderBy('position')->get(),'matches'=>$competition->matches()->with(['homeTeam','awayTeam'])->latest('kickoff_at')->take(30)->get()]]);}}
