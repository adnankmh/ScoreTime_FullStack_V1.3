<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
class UserAdminController extends Controller {
 public function index(Request $r){$q=User::query();if($s=$r->string('q')->trim())$q->where(fn($x)=>$x->where('name','like',"%$s%")->orWhere('username','like',"%$s%")->orWhere('email','like',"%$s%"));return view('admin.users.index',['users'=>$q->latest()->paginate(25)]);}
 public function toggle(Request $r,User $user){abort_if($user->id===$r->user()->id,422);$user->update(['is_active'=>!$user->is_active]);return back()->with('ok',__('ui.saved'));}
}
