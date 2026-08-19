<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class AdminAccountController extends Controller{
 public function edit(){return view('admin.account.edit');}
 public function update(Request $r){$u=$r->user();$d=$r->validate(['name'=>'required|string|max:80','email'=>'required|email|max:255|unique:users,email,'.$u->id,'current_password'=>'nullable|required_with:password|string','password'=>['nullable','confirmed',Password::min(10)->mixedCase()->numbers()]]);if(!empty($d['password'])){abort_unless(Hash::check((string)$d['current_password'],$u->password),422,'Current password is incorrect');}$u->update(['name'=>$d['name'],'email'=>$d['email']]+(!empty($d['password'])?['password'=>$d['password']]:[]));return back()->with('ok',__('ui.saved'));}
}
