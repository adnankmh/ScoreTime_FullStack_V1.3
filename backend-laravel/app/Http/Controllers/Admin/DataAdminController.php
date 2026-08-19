<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Competition;use App\Models\FootballMatch;use App\Models\Team;use Illuminate\Http\Request;
class DataAdminController extends Controller{
 public function competitions(){return view('admin.data.competitions',['items'=>Competition::orderBy('sort_order')->paginate(30)]);}
 public function teams(){return view('admin.data.teams',['items'=>Team::orderBy('name_en')->paginate(30)]);}
 public function matches(){return view('admin.data.matches',['items'=>FootballMatch::with(['competition','homeTeam','awayTeam'])->latest('kickoff_at')->paginate(30)]);}
 public function updateMatch(Request $r,FootballMatch $footballMatch){$d=$r->validate(['status'=>'required|in:scheduled,live,halftime,finished,postponed,cancelled','minute'=>'nullable|integer|min:0|max:150','home_score'=>'required|integer|min:0|max:99','away_score'=>'required|integer|min:0|max:99']);$footballMatch->update($d);return back()->with('ok',__('ui.saved'));}
}
