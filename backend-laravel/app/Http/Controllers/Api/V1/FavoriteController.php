<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{Competition,Favorite,FootballMatch,Player,Team};
use Illuminate\Http\Request;
class FavoriteController extends Controller {
 private function typeMap(string $type): string { return match($type){'team'=>Team::class,'player'=>Player::class,'competition'=>Competition::class,'match'=>FootballMatch::class}; }
 public function index(Request $r){return response()->json(['data'=>Favorite::where('user_id',$r->user()->id)->latest()->get()]);}
 public function store(Request $r){$d=$r->validate(['type'=>'required|in:team,player,competition,match','item_id'=>'required|integer|min:1']);$class=$this->typeMap($d['type']);abort_unless($class::whereKey($d['item_id'])->exists(),422,'Favorite item not found.');$f=Favorite::firstOrCreate(['user_id'=>$r->user()->id,'favoritable_type'=>$class,'favoritable_id'=>$d['item_id']]);return response()->json(['data'=>$f],201);}
 public function destroy(Request $r,Favorite $favorite){abort_unless($favorite->user_id===$r->user()->id,403);$favorite->delete();return response()->noContent();}
}
