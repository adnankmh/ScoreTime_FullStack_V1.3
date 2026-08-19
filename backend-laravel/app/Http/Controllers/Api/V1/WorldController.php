<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{Competition,FootballCountry,Player,Team,Coach};
use Illuminate\Http\Request;
class WorldController extends Controller {
 public function countries(){return FootballCountry::where('is_active',true)->withCount('competitions')->orderBy('name')->paginate(100);}
 public function competitions(Request $r){$q=Competition::query()->with('countryRelation')->when($r->country,fn($x,$v)=>$x->where('country',$v))->when($r->type,fn($x,$v)=>$x->where('type',$v))->when($r->q,fn($x,$v)=>$x->where(fn($y)=>$y->where('name_en','like','%'.$v.'%')->orWhere('name_ar','like','%'.$v.'%')));return $q->orderByDesc('is_featured')->orderBy('name_en')->paginate(min((int)$r->get('per_page',50),100));}
 public function teams(Request $r){return Team::query()->when($r->country,fn($q,$v)=>$q->where('country',$v))->when($r->type,fn($q,$v)=>$q->where('team_type',$v))->when($r->q,fn($q,$v)=>$q->where(fn($x)=>$x->where('name_en','like','%'.$v.'%')->orWhere('name_ar','like','%'.$v.'%')))->orderBy('name_en')->paginate(min((int)$r->get('per_page',50),100));}
 public function players(Request $r){return Player::query()->with('team:id,name_en,name_ar,logo_url')->when($r->team_id,fn($q,$v)=>$q->where('team_id',$v))->when($r->position,fn($q,$v)=>$q->where('position',$v))->when($r->nationality,fn($q,$v)=>$q->where('nationality',$v))->when($r->q,fn($q,$v)=>$q->where('name','like','%'.$v.'%'))->orderByDesc('rating')->paginate(min((int)$r->get('per_page',50),100));}
 public function coaches(Request $r){return Coach::query()->with('team:id,name_en,name_ar,logo_url')->when($r->team_id,fn($q,$v)=>$q->where('team_id',$v))->when($r->q,fn($q,$v)=>$q->where('name','like','%'.$v.'%'))->paginate(50);}
 public function summary(){return ['countries'=>FootballCountry::count(),'competitions'=>Competition::count(),'teams'=>Team::count(),'players'=>Player::count(),'coaches'=>Coach::count(),'last_sync'=>collect([Competition::max('last_synced_at'),Team::max('last_synced_at'),Player::max('last_synced_at')])->filter()->max()];}
}
