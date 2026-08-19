<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Services\DesignStudioService;
use Illuminate\Http\Request;
class DesignController extends Controller { public function bootstrap(Request $r,DesignStudioService $svc){$surface=$r->query('surface','app');abort_unless(in_array($surface,['app','web'],true),422);$data=$svc->bootstrap($surface,$r->query('locale',app()->getLocale()),$r->getHost(),$r->header('X-Visitor-Key',$r->ip()??'guest'));return response()->json(['data'=>$data])->header('Cache-Control','public, max-age=60');} }
