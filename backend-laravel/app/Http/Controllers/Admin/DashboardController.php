<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\{Article,Competition,FootballMatch,Team};
class DashboardController extends Controller {public function index(){return view('admin.dashboard',['stats'=>['matches'=>FootballMatch::count(),'live'=>FootballMatch::whereIn('status',['live','halftime'])->count(),'articles'=>Article::count(),'teams'=>Team::count(),'competitions'=>Competition::count()]]);}}
