<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\{FanMessage,Prediction,Transfer}; use Illuminate\Http\Request;
class GlobalFeaturesController extends Controller {public function index(){return view('admin.global-features',['transfers'=>Transfer::with(['player','fromTeam','toTeam'])->latest()->limit(20)->get(),'predictions'=>Prediction::count(),'fanMessages'=>FanMessage::latest()->limit(20)->get()]);}public function moderate(FanMessage $fanMessage){$fanMessage->update(['status'=>$fanMessage->status==='visible'?'hidden':'visible']);return back()->with('ok','Fan message status updated.');}}
