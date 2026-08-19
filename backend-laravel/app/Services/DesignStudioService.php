<?php
namespace App\Services;
use App\Models\{DesignProfile,DesignVersion,NavigationItem,PageLayout,MenuNode};
use Illuminate\Support\Facades\Cache;
class DesignStudioService {
 public function bootstrap(string $surface='web', string $locale='en', ?string $host=null, ?string $visitorKey=null): array {
  return Cache::remember("design:v09:$surface:$locale:".sha1((string)$host),120,function()use($surface,$locale,$host,$visitorKey){
   $experience=app(NoCodeExperienceService::class);$resolved=$experience->resolvedDesign($host);
   $layouts=PageLayout::where('surface',$surface)->where('is_published',true)->whereIn('locale',['*',$locale])->get()->groupBy('page_key')->map(fn($r)=>($r->firstWhere('locale',$locale)??$r->first())->blocks);
   $v09Nav=MenuNode::where('surface',$surface)->where('enabled',true)->whereNull('parent_id')->orderBy('sort_order')->get();$nav=($v09Nav->isNotEmpty()?$v09Nav:NavigationItem::where('surface',$surface)->where('enabled',true)->orderBy('sort_order')->get())->map(function($n)use($locale,$surface){$target=$n->target;if($surface==='web'){if(str_starts_with($target,'page:'))$target='/p/'.substr($target,5);elseif(!str_starts_with($target,'/')&&!str_starts_with($target,'http'))$target=$target==='home'?'/':'/'.$target;}return['key'=>$n->key,'label'=>$n->label[$locale]??$n->label['en']??$n->key,'icon'=>$n->icon,'target'=>$target,'location'=>$n->location];});
   return ['design'=>$resolved,'layouts'=>$layouts,'navigation'=>$nav,'customPages'=>$experience->customPages($surface,$locale),'menuTree'=>$experience->menu($surface,$locale),'experiments'=>$experience->experiments($surface,$visitorKey??'guest'),'revision'=>now()->timestamp];
  });
 }
 public function clear():void { Cache::flush(); }
 public function snapshot(DesignProfile $profile,?int $userId,string $note=''):DesignVersion { $version=((int)$profile->versions()->max('version'))+1; return $profile->versions()->create(['version'=>$version,'snapshot'=>['tokens'=>$profile->tokens,'branding'=>$profile->branding,'features'=>$profile->features],'note'=>$note,'created_by'=>$userId]); }
}
