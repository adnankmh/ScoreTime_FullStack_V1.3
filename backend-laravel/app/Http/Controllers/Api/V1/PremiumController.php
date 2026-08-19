<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\PremiumEntitlement;use Illuminate\Http\Request;
class PremiumController extends Controller{public function status(Request $r){$u=$r->user();$active=$u->plan==='premium'&&($u->premium_until===null||$u->premium_until->isFuture());return response()->json(['data'=>['plan'=>$u->plan,'premium_until'=>$u->premium_until,'active'=>$active,'entitlements'=>PremiumEntitlement::where('user_id',$u->id)->where(fn($q)=>$q->whereNull('ends_at')->orWhere('ends_at','>',now()))->pluck('feature')]]);}}
