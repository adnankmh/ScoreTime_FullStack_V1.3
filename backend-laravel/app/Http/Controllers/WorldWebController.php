<?php
namespace App\Http\Controllers;
use App\Models\{Competition,FootballCountry,Player,Team};
use Illuminate\Http\Request;
class WorldWebController extends Controller {
 public function index(Request $r){$q=trim((string)$r->get('q',''));return view('world.index',['countries'=>FootballCountry::withCount('competitions')->orderByDesc('competitions_count')->limit(30)->get(),'competitions'=>Competition::when($q,fn($x)=>$x->where(fn($y)=>$y->where('name_en','like','%'.$q.'%')->orWhere('name_ar','like','%'.$q.'%')))->orderByDesc('is_featured')->orderBy('name_en')->paginate(36)->withQueryString(),'counts'=>['countries'=>FootballCountry::count(),'competitions'=>Competition::count(),'teams'=>Team::count(),'players'=>Player::count()],'q'=>$q]);}
 public function players(Request $r){$q=trim((string)$r->get('q',''));return view('world.players',['players'=>Player::with('team')->when($q,fn($x)=>$x->where('name','like','%'.$q.'%'))->orderByDesc('rating')->paginate(50)->withQueryString(),'q'=>$q]);}
}
