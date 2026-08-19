<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\{FanMessage,FootballMatch}; use Illuminate\Http\Request;
class FanRoomController extends Controller {public function index(FootballMatch $footballMatch){return response()->json(['data'=>FanMessage::with('user:id,name,username')->where('football_match_id',$footballMatch->id)->where('status','visible')->latest()->limit(100)->get()->reverse()->values()]);}public function store(Request $r,FootballMatch $footballMatch){$d=$r->validate(['body'=>'required|string|min:1|max:500']);$m=FanMessage::create(['user_id'=>$r->user()->id,'football_match_id'=>$footballMatch->id,'body'=>$d['body']]);return response()->json(['data'=>$m->load('user:id,name,username')],201);}}
