<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\PlayerSeasonStat;use Illuminate\Http\Request;
class StatsController extends Controller { public function leaders(Request $r){$q=PlayerSeasonStat::with(['player.team','competition'])->when($r->integer('competition_id'),fn($x,$id)=>$x->where('competition_id',$id))->when($r->string('season')->toString(),fn($x,$s)=>$x->where('season',$s));$metric=in_array($r->get('metric'),['goals','assists','rating','xg','xa'])?$r->get('metric'):'goals';return $q->orderByDesc($metric)->limit(50)->get();} }
