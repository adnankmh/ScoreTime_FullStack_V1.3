<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
class PreferenceController extends Controller {
 public function locale(Request $r,string $locale){abort_unless(in_array($locale,['en','ar','fr','es','de','tr'],true),404);$r->session()->put('locale',$locale);if($r->user())$r->user()->update(['locale'=>$locale]);return back();}
 public function appearance(Request $r){$d=$r->validate(['theme'=>'required|in:stadium,midnight,light','font_scale'=>'required|numeric|min:.85|max:1.30']);$r->session()->put($d);$r->user()?->update($d);return back();}
}
