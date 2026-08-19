<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
class ArticleAdminController extends Controller {
 public function index(){return view('admin.articles.index',['articles'=>Article::latest()->paginate(20)]);}
 public function create(){return view('admin.articles.form',['article'=>new Article]);}
 public function store(Request $r){$a=Article::create($this->data($r));$this->audit($r,'article.created',$a->id);return redirect()->route('admin.articles.index')->with('ok',__('ui.saved'));}
 public function edit(Article $article){return view('admin.articles.form',compact('article'));}
 public function update(Request $r,Article $article){$article->update($this->data($r));$this->audit($r,'article.updated',$article->id);return redirect()->route('admin.articles.index')->with('ok',__('ui.saved'));}
 public function destroy(Request $r,Article $article){$id=$article->id;$article->delete();$this->audit($r,'article.deleted',$id);return back()->with('ok',__('ui.deleted'));}
 private function data(Request $r){$d=$r->validate(['title'=>'required|string|max:180','excerpt'=>'nullable|string|max:500','body'=>'required|string','category'=>'required|string|max:80','image_url'=>'nullable|url|max:1000','is_breaking'=>'nullable|boolean','is_published'=>'nullable|boolean']);$d['slug']=Str::slug($d['title']).'-'.Str::lower(Str::random(6));$d['is_breaking']=$r->boolean('is_breaking');$publish=$r->boolean('is_published');unset($d['is_published']);$d['published_at']=$publish?($r->route('article')?->published_at ?? now()):null;return $d;}
 private function audit(Request $r,$action,$id){AuditLog::create(['user_id'=>$r->user()->id,'action'=>$action,'entity_type'=>'Article','entity_id'=>$id,'ip'=>$r->ip(),'user_agent'=>substr((string)$r->userAgent(),0,500),'payload'=>[],'created_at'=>now()]);}
}
