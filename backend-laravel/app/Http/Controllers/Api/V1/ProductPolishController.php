<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{TvBroadcast,SmartAlertRule,ExperiencePreset};
use Illuminate\Http\Request;
class ProductPolishController extends Controller {
 public function tvGuide(Request $r){
  return response()->json(TvBroadcast::with('match.homeTeam','match.awayTeam')->latest()->limit(100)->get());
 }
 public function presets(){
  return response()->json(ExperiencePreset::where('published',true)->get()->groupBy(fn($x)=>$x->surface.':'.$x->screen));
 }
 public function alerts(Request $r){ return response()->json($r->user()->smartAlertRules()->get()); }
 public function storeAlert(Request $r){
  $d=$r->validate(['event_type'=>'required|string|max:40','subject_type'=>'nullable|string|max:40','subject_id'=>'nullable|integer','push'=>'boolean','in_app'=>'boolean','quiet_hours'=>'nullable|array']);
  return response()->json($r->user()->smartAlertRules()->create($d),201);
 }
}
