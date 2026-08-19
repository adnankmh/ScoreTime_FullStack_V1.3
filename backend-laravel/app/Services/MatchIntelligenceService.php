<?php
namespace App\Services;
use App\Models\FootballMatch;
class MatchIntelligenceService{
 public function __construct(private MatchVisualService $visual){}
 public function package(FootballMatch $match):array{
  $match->load(['competition','homeTeam','awayTeam','matchEvents.player','matchEvents.team','lineupEntries.player','lineupEntries.team','shots.player','shots.team']);
  $homeForm=$this->form($match->home_team_id,$match->id);$awayForm=$this->form($match->away_team_id,$match->id);$visual=$this->visual->package($match);
  return ['match'=>$match,'home_form'=>$homeForm,'away_form'=>$awayForm,'h2h'=>$this->h2h($match),'win_probability'=>$this->probability($homeForm,$awayForm),'lineups'=>$match->lineupEntries->groupBy('team_id'),'shot_map'=>$visual['shots'],'momentum'=>$visual['momentum'],'revision'=>$visual['revision'],'last_synced_at'=>$visual['last_synced_at'],'refresh_seconds'=>$visual['refresh_seconds']];
 }
 private function form(int $teamId,int $exclude):array{return FootballMatch::query()->where('id','!=',$exclude)->where(fn($q)=>$q->where('home_team_id',$teamId)->orWhere('away_team_id',$teamId))->whereIn('status',['finished','ft'])->latest('kickoff_at')->limit(5)->get()->map(function($m)use($teamId){$for=$m->home_team_id===$teamId?$m->home_score:$m->away_score;$against=$m->home_team_id===$teamId?$m->away_score:$m->home_score;return ['id'=>$m->id,'result'=>$for>$against?'W':($for===$against?'D':'L'),'for'=>$for,'against'=>$against,'kickoff_at'=>$m->kickoff_at];})->values()->all();}
 private function h2h(FootballMatch $m):array{return FootballMatch::with(['homeTeam:id,name_ar,name_en,logo_url','awayTeam:id,name_ar,name_en,logo_url'])->where('id','!=',$m->id)->where(function($q)use($m){$q->where(fn($x)=>$x->where('home_team_id',$m->home_team_id)->where('away_team_id',$m->away_team_id))->orWhere(fn($x)=>$x->where('home_team_id',$m->away_team_id)->where('away_team_id',$m->home_team_id));})->latest('kickoff_at')->limit(6)->get()->toArray();}
 private function probability(array $h,array $a):array{$score=fn($f)=>array_sum(array_map(fn($x)=>$x['result']==='W'?3:($x['result']==='D'?1:0),$f));$hs=$score($h)+2;$as=$score($a)+2;$draw=3;$sum=$hs+$as+$draw;return ['home'=>round($hs/$sum*100),'draw'=>round($draw/$sum*100),'away'=>round($as/$sum*100)];}
}
