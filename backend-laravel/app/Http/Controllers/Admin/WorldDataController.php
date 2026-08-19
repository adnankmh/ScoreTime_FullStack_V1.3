<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{Competition,FootballCountry,Player,Team};
use App\Services\FootballProviderManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
class WorldDataController extends Controller {
 public function index(FootballProviderManager $manager){return view('admin.world-data.index',['health'=>$manager->driver()->health(),'counts'=>['countries'=>FootballCountry::count(),'competitions'=>Competition::count(),'teams'=>Team::count(),'players'=>Player::count()],'recent'=>Competition::latest('last_synced_at')->take(12)->get()]);}
 public function sync(Request $r){$data=$r->validate(['scope'=>'required|in:countries,catalog,league,players','country'=>'nullable|string|max:100','season'=>'nullable|integer|min:1900|max:2100','league'=>'nullable|integer|min:1','pages'=>'nullable|integer|min:1|max:50']);if(in_array($data['scope'],['league','players'],true)&&empty($data['league']))return back()->withErrors(['league'=>'Provider league ID is required.']);$args=['scope'=>$data['scope']];foreach(['country','season','league','pages'] as $k)if(!empty($data[$k]))$args['--'.$k]=$data[$k];$code=Artisan::call('football:sync-global',$args);return back()->with($code===0?'status':'error',trim(Artisan::output()));}
}
