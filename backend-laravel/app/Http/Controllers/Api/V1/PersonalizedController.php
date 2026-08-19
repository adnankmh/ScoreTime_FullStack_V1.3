<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Services\PersonalizedFeedService;use Illuminate\Http\Request;
class PersonalizedController extends Controller{public function feed(Request $r,PersonalizedFeedService $s){return response()->json(['data'=>$s->for($r->user())]);}}
