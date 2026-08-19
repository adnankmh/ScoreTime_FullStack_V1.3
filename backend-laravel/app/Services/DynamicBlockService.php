<?php
namespace App\Services;
use App\Models\{Article,FootballMatch,Transfer};
class DynamicBlockService {
 public function hydrate(array $blocks): array { return collect($blocks)->map(function($b){if(!is_array($b))return $b;$cfg=$b['config']??[];$limit=max(1,min(20,(int)($cfg['limit']??6)));$type=$b['type']??'';$data=[];
   if($type==='live_matches')$data=FootballMatch::with(['homeTeam','awayTeam','competition'])->whereIn('status',['live','halftime'])->orderBy('kickoff_at')->limit($limit)->get()->map(fn($m)=>['id'=>$m->id,'status'=>$m->status,'minute'=>$m->minute,'home'=>$m->homeTeam?->name_en??$m->homeTeam?->name_ar,'away'=>$m->awayTeam?->name_en??$m->awayTeam?->name_ar,'home_score'=>$m->home_score,'away_score'=>$m->away_score,'competition'=>$m->competition?->name_en??$m->competition?->name_ar])->all();
   elseif(in_array($type,['latest_news','breaking_news'],true)){$q=Article::whereNotNull('published_at')->latest('published_at');if($type==='breaking_news')$q->where('is_breaking',true);$data=$q->limit($limit)->get()->map(fn($a)=>['id'=>$a->id,'title'=>$a->title,'slug'=>$a->slug,'excerpt'=>$a->excerpt,'image_url'=>$a->image_url,'published_at'=>$a->published_at?->toIso8601String()])->all();}
   elseif($type==='transfers')$data=Transfer::with(['player','fromTeam','toTeam'])->latest('transfer_date')->limit($limit)->get()->map(fn($t)=>['id'=>$t->id,'player'=>$t->player?->name_en??$t->player?->name_ar,'from'=>$t->fromTeam?->name_en??$t->fromTeam?->name_ar,'to'=>$t->toTeam?->name_en??$t->toTeam?->name_ar,'status'=>$t->status,'fee'=>$t->fee,'currency'=>$t->currency])->all();
   if($data)$b['data']=$data;return $b;})->all(); }
}
