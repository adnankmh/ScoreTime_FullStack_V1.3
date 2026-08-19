<?php
namespace App\Services;
use App\Models\FootballMatch;
class MatchVisualService{
 public function package(FootballMatch $match):array{
  $shots=$match->shots()->with(['player:id,name,photo_url','team:id,name_ar,name_en,logo_url'])->get();
  $momentum=$match->momentumPoints()->get(['minute','value']);
  if($shots->isEmpty() && is_array($match->shot_map))$shots=collect($match->shot_map);
  if($momentum->isEmpty() && is_array($match->momentum))$momentum=collect($match->momentum);
  return ['shots'=>$shots,'momentum'=>$momentum,'revision'=>$match->revision,'last_synced_at'=>$match->last_synced_at,'refresh_seconds'=>config('football.live_refresh_seconds',15)];
 }
}
