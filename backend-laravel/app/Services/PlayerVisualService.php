<?php
namespace App\Services;
use App\Models\{Player,PlayerHeatmapPoint,PlayerSeasonStat};
class PlayerVisualService {
 public function comparison(Player $a, Player $b): array {
  $aStats=PlayerSeasonStat::where('player_id',$a->id)->latest('season')->first(); $bStats=PlayerSeasonStat::where('player_id',$b->id)->latest('season')->first();
  $metrics=['appearances','minutes','goals','assists','rating','xg','xa'];
  $rows=[]; foreach($metrics as $m)$rows[]=['metric'=>$m,'a'=>$aStats?->$m,'b'=>$bStats?->$m];
  return ['player_a'=>$a->load('team'),'player_b'=>$b->load('team'),'stats_a'=>$aStats,'stats_b'=>$bStats,'comparison'=>$rows];
 }
 public function heatmap(int $matchId, Player $player): array { return PlayerHeatmapPoint::where('football_match_id',$matchId)->where('player_id',$player->id)->get(['x','y','weight','minute_from','minute_to'])->toArray(); }
}
