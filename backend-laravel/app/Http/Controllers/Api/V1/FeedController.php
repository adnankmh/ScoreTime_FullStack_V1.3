<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\{Article,Competition};use App\Services\FootballDataService;
class FeedController extends Controller {public function __construct(private FootballDataService $football){} public function index(){return response()->json(['data'=>['live_matches'=>$this->football->live(),'today_matches'=>$this->football->matchesForDate(),'breaking'=>Article::where('is_breaking',true)->latest('published_at')->take(6)->get(),'top_news'=>Article::latest('published_at')->take(12)->get(),'competitions'=>Competition::where('is_featured',true)->orderBy('sort_order')->get()]]);}}
