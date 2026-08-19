<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller; use Illuminate\Http\Request; use Illuminate\Support\Facades\Hash; use Illuminate\Validation\Rule;
class ProfileController extends Controller {
 public function show(Request $r){$u=$r->user()->loadCount(['tokens']);return response()->json(['data'=>$u]);}
 public function update(Request $r){$u=$r->user();$d=$r->validate(['name'=>'required|string|max:120','username'=>['required','string','max:60',Rule::unique('users')->ignore($u->id)],'bio'=>'nullable|string|max:280','locale'=>'nullable|in:ar,en,tr,fr,es','theme'=>'nullable|in:stadium,midnight,light','font_scale'=>'nullable|numeric|min:.85|max:1.30','profile_public'=>'nullable|boolean']);$u->update($d);return response()->json(['data'=>$u->fresh()]);}
 public function password(Request $r){$d=$r->validate(['current_password'=>'required','password'=>'required|string|min:10|confirmed']);abort_unless(Hash::check($d['current_password'],$r->user()->password),422,'Current password is incorrect.');$r->user()->update(['password'=>$d['password']]);$r->user()->tokens()->delete();return response()->json(['message'=>'Password updated. Please sign in again.']);}
}