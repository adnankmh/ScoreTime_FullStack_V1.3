<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\FootballMatch; use App\Services\RealtimeMatchService; use Illuminate\Http\Request;
class RealtimeController extends Controller {
 public function snapshot(Request $r,FootballMatch $footballMatch,RealtimeMatchService $svc){return response()->json(['data'=>$svc->snapshot($footballMatch,(int)$r->query('after',0))])->header('Cache-Control','no-store');}
 public function stream(Request $r,FootballMatch $footballMatch,RealtimeMatchService $svc){$after=(int)$r->query('after',0);return response()->stream(function()use($footballMatch,$svc,$after){$start=microtime(true);$cursor=$after;while((microtime(true)-$start)<config('realtime.sse_max_seconds')){$data=$svc->snapshot($footballMatch->fresh(),$cursor);if(count($data['commentary']))$cursor=(int)collect($data['commentary'])->max('id');echo 'event: match'."\n".'data: '.json_encode($data,JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES)."\n\n";if(ob_get_level()>0)ob_flush();flush();usleep(config('realtime.sse_sleep_ms')*1000);}echo "event: close\ndata: {}\n\n";},200,['Content-Type'=>'text/event-stream','Cache-Control'=>'no-cache, no-transform','X-Accel-Buffering'=>'no','Connection'=>'keep-alive']);}
}
