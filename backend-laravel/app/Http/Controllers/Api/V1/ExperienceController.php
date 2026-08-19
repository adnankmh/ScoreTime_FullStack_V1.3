<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\CustomPage;
use App\Services\{DynamicBlockService,NoCodeExperienceService};
use Illuminate\Http\Request;
class ExperienceController extends Controller {
 public function page(CustomPage $customPage,Request $r,DynamicBlockService $blocks){abort_unless($customPage->is_published,404);$locale=$r->query('locale',app()->getLocale());return response()->json(['data'=>['slug'=>$customPage->slug,'title'=>$customPage->title[$locale]??$customPage->title['en']??$customPage->slug,'blocks'=>$blocks->hydrate($customPage->blocks??[]),'seo'=>$customPage->seo]]);}
 public function bootstrap(Request $r,NoCodeExperienceService $svc){$surface=$r->query('surface','app');abort_unless(in_array($surface,['web','app'],true),422);$locale=$r->query('locale',app()->getLocale());$visitor=$r->header('X-Visitor-Key',$r->ip()??'guest');return response()->json(['data'=>['design'=>$svc->resolvedDesign($r->getHost()),'pages'=>$svc->customPages($surface,$locale),'menu'=>$svc->menu($surface,$locale),'experiments'=>$svc->experiments($surface,$visitor)]]);}
}
