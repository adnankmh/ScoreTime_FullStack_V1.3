<?php
namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
class AuthController extends Controller {
 public function showLogin(){return view('auth.login');}
 public function showRegister(){return view('auth.register');}
 public function login(Request $r){$data=$r->validate(['login'=>'required|string|max:255','password'=>'required|string|max:255']);$field=filter_var($data['login'],FILTER_VALIDATE_EMAIL)?'email':'username';if(!Auth::attempt([$field=>$data['login'],'password'=>$data['password'],'is_active'=>1],$r->boolean('remember'))){return back()->withErrors(['login'=>__('ui.invalid_credentials')])->onlyInput('login');}$r->session()->regenerate();return redirect()->intended('/');}
 public function register(Request $r){$data=$r->validate(['name'=>'required|string|max:80','username'=>'required|alpha_dash|min:3|max:40|unique:users,username','email'=>'required|email:rfc|max:255|unique:users,email','password'=>['required','confirmed',Password::min(10)->mixedCase()->numbers()]]);$u=User::create($data+['locale'=>app()->getLocale(),'theme'=>'stadium','font_scale'=>1.0,'is_active'=>true]);Auth::login($u);$r->session()->regenerate();return redirect('/');}
 public function logout(Request $r){Auth::logout();$r->session()->invalidate();$r->session()->regenerateToken();return redirect('/');}
}
