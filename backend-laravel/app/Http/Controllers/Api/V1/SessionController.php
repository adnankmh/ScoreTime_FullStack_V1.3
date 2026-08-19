<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use App\Models\DeviceSession; use Illuminate\Http\Request;
class SessionController extends Controller {public function index(Request $r){return response()->json(['data'=>DeviceSession::where('user_id',$r->user()->id)->whereNull('revoked_at')->latest('last_seen_at')->get()]);} public function destroy(Request $r,DeviceSession $deviceSession){abort_unless($deviceSession->user_id===$r->user()->id,403);$deviceSession->update(['revoked_at'=>now()]);return response()->json(['message'=>'Session revoked']);}}
