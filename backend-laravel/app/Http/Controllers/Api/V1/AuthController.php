<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller {
 public function register(Request $r){$d=$r->validate(['name'=>'required|string|max:80','username'=>'required|alpha_dash|min:3|max:40|unique:users,username','email'=>'required|email|max:255|unique:users,email','password'=>['required','confirmed',Password::min(10)->mixedCase()->numbers()]]);$u=User::create($d+['is_active'=>true]);return response()->json(['token'=>$u->createToken('mobile')->plainTextToken,'user'=>$u],201);}
 public function login(Request $r){$d=$r->validate(['login'=>'required|string','password'=>'required|string']);$field=filter_var($d['login'],FILTER_VALIDATE_EMAIL)?'email':'username';$u=User::where($field,$d['login'])->where('is_active',1)->first();abort_unless($u&&Hash::check($d['password'],$u->password),422,'Invalid credentials');$u->tokens()->delete();return ['token'=>$u->createToken('mobile')->plainTextToken,'user'=>$u];}
 public function me(Request $r){return $r->user();}
 public function logout(Request $r){$r->user()->currentAccessToken()?->delete();return response()->noContent();}
}
