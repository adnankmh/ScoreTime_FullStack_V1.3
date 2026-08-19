<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\NewsSource;
use Illuminate\Http\Request;
class NewsSourceController extends Controller {
 public function index(){ return view('admin.news-sources.index',['sources'=>NewsSource::latest()->paginate(30)]); }
 public function store(Request $r){
  $d=$r->validate(['name'=>'required|string|max:120','type'=>'required|in:rss,api,partner','feed_url'=>'required|url','homepage_url'=>'nullable|url','license_status'=>'required|in:review,licensed,rss-permitted,partner,blocked','trust_score'=>'required|integer|min:0|max:100']);
  $d['enabled']=$r->boolean('enabled') && $d['license_status']!=='review' && $d['license_status']!=='blocked';
  NewsSource::create($d); return back()->with('status','Source saved.');
 }
 public function destroy(NewsSource $newsSource){$newsSource->delete(); return back()->with('status','Source removed.');}
}
