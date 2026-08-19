<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\PushDevice;use App\Models\UserNotification;use Illuminate\Http\Request;
class NotificationController extends Controller {
 public function index(Request $r){return UserNotification::where('user_id',$r->user()->id)->latest()->paginate(30);}
 public function read(Request $r,UserNotification $notification){abort_unless($notification->user_id===$r->user()->id,403);$notification->update(['read_at'=>now()]);return ['ok'=>true];}
 public function readAll(Request $r){UserNotification::where('user_id',$r->user()->id)->whereNull('read_at')->update(['read_at'=>now()]);return ['ok'=>true];}
 public function registerDevice(Request $r){$d=$r->validate(['token'=>'required|string|max:512','platform'=>'nullable|string|max:30','device_name'=>'nullable|string|max:120','locale'=>'nullable|string|max:10']);$device=PushDevice::updateOrCreate(['token'=>$d['token']],array_merge($d,['user_id'=>$r->user()->id,'enabled'=>true,'last_seen_at'=>now()]));return response()->json($device,201);}
 public function unregisterDevice(Request $r){$d=$r->validate(['token'=>'required|string|max:512']);PushDevice::where('user_id',$r->user()->id)->where('token',$d['token'])->delete();return response()->noContent();}
}
