<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\Article;
class NewsController extends Controller {public function index(){return response()->json(Article::latest('published_at')->paginate(20));} public function show(Article $article){return response()->json(['data'=>$article]);}}
