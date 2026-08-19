<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\{CompetitionBracketNode,TeamOfWeekEntry,PlayerRadarSnapshot,MatchStoryItem,OnboardingFlow,FootballMatch};
use Illuminate\Http\Request;

class ReleaseCandidateController extends Controller {
 public function calendar(Request $r) {
  $from=$r->date('from',now()->startOfDay()); $to=$r->date('to',now()->addDays(14)->endOfDay());
  return FootballMatch::with(['homeTeam','awayTeam','competition'])->whereBetween('starts_at',[$from,$to])->orderBy('starts_at')->paginate(100);
 }
 public function bracket($competition) {
  return CompetitionBracketNode::where('competition_id',$competition)->with('footballMatch.homeTeam','footballMatch.awayTeam')->orderBy('round_order')->orderBy('slot_order')->get()->groupBy('stage');
 }
 public function teamOfWeek(Request $r) {
  return TeamOfWeekEntry::with(['player','team'])->when($r->competition_id,fn($q,$v)=>$q->where('competition_id',$v))->when($r->week,fn($q,$v)=>$q->where('week',$v))->orderBy('position')->get();
 }
 public function playerRadar($player) {
  return PlayerRadarSnapshot::where('player_id',$player)->latest()->firstOrFail();
 }
 public function matchStory($match) {
  return MatchStoryItem::where('football_match_id',$match)->orderBy('sequence')->get();
 }
 public function onboarding(Request $r) {
  $locale=$r->get('locale','en');
  return OnboardingFlow::where('locale',$locale)->where('published',true)->latest('version')->first()
      ?? OnboardingFlow::where('locale','en')->where('published',true)->latest('version')->first();
 }
 public function deepLinkManifest() {
  return response()->json(['scheme'=>'scoretime','routes'=>[
   'match'=>'scoretime://match/{id}','team'=>'scoretime://team/{id}','player'=>'scoretime://player/{id}',
   'competition'=>'scoretime://competition/{id}','news'=>'scoretime://news/{id}'
  ]]);
 }
}
