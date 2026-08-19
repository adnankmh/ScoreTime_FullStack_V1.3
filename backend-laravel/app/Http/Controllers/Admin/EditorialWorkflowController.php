<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;
use App\Models\{EditorialItem,EditorialRevision};
use Illuminate\Http\Request;

class EditorialWorkflowController extends Controller {
 public function index(){return view('admin.editorial.index',['items'=>EditorialItem::with('source')->latest()->paginate(40)]);}
 public function review(Request $r, EditorialItem $editorialItem){
  $d=$r->validate(['editorial_summary'=>'required|string|max:4000','action'=>'required|in:save,approve,reject']);
  EditorialRevision::create(['editorial_item_id'=>$editorialItem->id,'user_id'=>$r->user()->id,'summary'=>$d['editorial_summary'],'action'=>$d['action']]);
  $status=match($d['action']){'approve'=>'approved','reject'=>'rejected',default=>'review'};
  $editorialItem->update(['editorial_summary'=>$d['editorial_summary'],'status'=>$status,'reviewed_by'=>$r->user()->id,'reviewed_at'=>now()]);
  return back()->with('status','Editorial item updated.');
 }
}
