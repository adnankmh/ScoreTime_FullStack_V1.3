<?php
namespace App\Services;
use App\Models\{CustomPage,DesignExperiment,DesignProfile,DesignSchedule,MenuNode,WhiteLabelProfile};
use Illuminate\Support\Facades\Cache;
class NoCodeExperienceService {
 public function activeSchedule(?DesignProfile $design=null): ?DesignSchedule {
  $design ??= DesignProfile::where('is_active',true)->latest('id')->first(); if(!$design)return null;
  return DesignSchedule::where('design_profile_id',$design->id)->where('enabled',true)->where('starts_at','<=',now())->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>',now()))->latest('starts_at')->first();
 }
 public function resolvedDesign(?string $host=null): array {
  $base=DesignProfile::where('is_active',true)->latest('id')->first(); if(!$base)return [];
  $branding=$base->branding??[];$tokens=$base->tokens??[];$features=$base->features??[];
  if($schedule=$this->activeSchedule($base)){ $o=$schedule->overrides??[];$branding=array_replace_recursive($branding,$o['branding']??[]);$tokens=array_replace_recursive($tokens,$o['tokens']??[]);$features=array_replace_recursive($features,$o['features']??[]); }
  if($host){$wl=WhiteLabelProfile::where('enabled',true)->where('host',$host)->first();if($wl){$branding=array_replace_recursive($branding,$wl->branding??[]);$tokens=array_replace_recursive($tokens,$wl->tokens??[]);$features=array_replace_recursive($features,$wl->features??[]);}}
  return ['id'=>$base->id,'name'=>$base->name,'tokens'=>$tokens,'branding'=>$branding,'features'=>$features,'schedule'=>$schedule?->only(['id','name','starts_at','ends_at'])];
 }
 public function experiments(string $surface,string $visitorKey): array {
  return DesignExperiment::where('enabled',true)->whereIn('surface',[$surface,'both'])->where(fn($q)=>$q->whereNull('starts_at')->orWhere('starts_at','<=',now()))->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>',now()))->get()->mapWithKeys(function($e)use($visitorKey){$bucket=abs(crc32($e->key.'|'.$visitorKey))%100;$variant=$bucket<$e->traffic_percent?'b':'a';return[$e->key=>['variant'=>$variant,'payload'=>$variant==='b'?$e->variant_b:$e->variant_a]];})->all();
 }
 public function customPages(string $surface,string $locale): array { return CustomPage::where('is_published',true)->whereIn('surface',[$surface,'both'])->orderBy('slug')->get()->map(fn($p)=>['slug'=>$p->slug,'title'=>$p->title[$locale]??$p->title['en']??$p->slug,'blocks'=>$p->blocks,'seo'=>$p->seo])->all(); }
 public function menu(string $surface,string $locale): array { $roots=MenuNode::where('surface',$surface)->where('enabled',true)->whereNull('parent_id')->orderBy('sort_order')->with('children')->get();$map=function($n)use($locale,&$map){return['key'=>$n->key,'label'=>$n->label[$locale]??$n->label['en']??$n->key,'icon'=>$n->icon,'target'=>$n->target,'location'=>$n->location,'children'=>$n->children->where('enabled',true)->map(fn($c)=>$map($c))->values()->all()];};return $roots->map(fn($n)=>$map($n))->all(); }
 public function clear(): void { Cache::add('scoretime:design:version',1,now()->addYears(5));Cache::increment('scoretime:design:version'); }
}
