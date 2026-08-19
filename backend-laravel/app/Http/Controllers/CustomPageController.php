<?php
namespace App\Http\Controllers;
use App\Models\CustomPage;
use App\Services\{DynamicBlockService,NoCodeExperienceService};
class CustomPageController extends Controller { public function show(string $slug,NoCodeExperienceService $svc,DynamicBlockService $blocks){$page=CustomPage::where('slug',$slug)->where('is_published',true)->whereIn('surface',['web','both'])->firstOrFail();$page->blocks=$blocks->hydrate($page->blocks??[]);return view('custom-page',['page'=>$page,'resolvedDesign'=>$svc->resolvedDesign(request()->getHost())]);} }
