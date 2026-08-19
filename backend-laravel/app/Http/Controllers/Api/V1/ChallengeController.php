<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\{User,UserChallenge,UserLevel};use Illuminate\Http\Request;
class ChallengeController extends Controller{
 public function index(Request $r){return response()->json(['data'=>UserChallenge::with(['creator:id,name,username,avatar_url','opponent:id,name,username,avatar_url'])->where(fn($q)=>$q->where('creator_id',$r->user()->id)->orWhere('opponent_id',$r->user()->id))->latest()->get()]);}
 public function store(Request $r){$d=$r->validate(['username'=>'required|string','title'=>'required|string|max:140','type'=>'nullable|in:predictions,streak']);$op=User::where('username',$d['username'])->firstOrFail();abort_if($op->id===$r->user()->id,422,'Cannot challenge yourself.');$c=UserChallenge::create(['creator_id'=>$r->user()->id,'opponent_id'=>$op->id,'title'=>$d['title'],'type'=>$d['type']??'predictions','status'=>'pending','starts_at'=>now()]);return response()->json(['data'=>$c],201);}
 public function accept(Request $r,UserChallenge $challenge){abort_unless($challenge->opponent_id===$r->user()->id,403);$challenge->update(['status'=>'active']);return response()->json(['data'=>$challenge->fresh()]);}
 public function level(Request $r){$level=UserLevel::firstOrCreate(['user_id'=>$r->user()->id]);return response()->json(['data'=>$level]);}
}
