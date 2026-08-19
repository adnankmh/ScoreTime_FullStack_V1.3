<?php
namespace App\Http\Controllers\Api\V1;
use App\Http\Controllers\Controller;use App\Models\FootballMatch;use App\Services\MatchVisualService;
class VisualMatchController extends Controller{public function show(FootballMatch $footballMatch,MatchVisualService $visual){return response()->json(['data'=>$visual->package($footballMatch)])->header('Cache-Control','no-store');}}
