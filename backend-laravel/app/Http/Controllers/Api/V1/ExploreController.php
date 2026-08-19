<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\{Article,Competition,Player,Team,Transfer}; use Illuminate\Http\Request;
class ExploreController extends Controller {
 public function search(Request $r){$q=trim((string)$r->query('q')); if(mb_strlen($q)<2)return response()->json(['data'=>[]]); $like='%'.str_replace(['%','_'],['\%','\_'],$q).'%'; return response()->json(['data'=>[
  'teams'=>Team::where(function($x)use($like){$x->where('name_ar','like',$like)->orWhere('name_en','like',$like)->orWhere('slug','like',$like);})->limit(8)->get(),
  'players'=>Player::where('name','like',$like)->limit(8)->get(),
  'competitions'=>Competition::where(function($x)use($like){$x->where('name_ar','like',$like)->orWhere('name_en','like',$like)->orWhere('slug','like',$like);})->limit(8)->get(),
  'news'=>Article::whereNotNull('published_at')->where('title','like',$like)->latest('published_at')->limit(8)->get(['id','title','slug','published_at'])
 ]]);}
 public function players(){return response()->json(['data'=>Player::with('team')->orderByDesc('rating')->paginate(30)]);} public function player(Player $player){return response()->json(['data'=>$player->load('team')]);}
 public function transfers(){return response()->json(['data'=>Transfer::with(['player','fromTeam','toTeam'])->latest('transfer_date')->paginate(30)]);} 
}
