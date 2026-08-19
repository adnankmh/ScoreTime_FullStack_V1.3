<?php
namespace App\Http\Controllers\Api\V1; use App\Http\Controllers\Controller; use App\Models\Player; use App\Services\PlayerVisualService;
class PlayerVisualController extends Controller { public function compare(Player $playerA,Player $playerB,PlayerVisualService $svc){return response()->json(['data'=>$svc->comparison($playerA,$playerB)]);} public function heatmap(int $match,Player $player,PlayerVisualService $svc){return response()->json(['data'=>$svc->heatmap($match,$player)]);} }
