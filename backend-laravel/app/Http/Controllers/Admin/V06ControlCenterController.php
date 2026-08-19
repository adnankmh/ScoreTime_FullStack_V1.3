<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\{AdCampaign,Article,MatchShot,MatchMomentumPoint,PremiumEntitlement,PushDevice,SearchTrend,UserChallenge,UserLevel};
class V06ControlCenterController extends Controller{public function index(){return view('admin.v06.index',['metrics'=>['shots'=>MatchShot::count(),'momentum_points'=>MatchMomentumPoint::count(),'challenges'=>UserChallenge::count(),'leveled_users'=>UserLevel::count(),'push_devices'=>PushDevice::where('enabled',true)->count(),'premium_entitlements'=>PremiumEntitlement::count(),'active_campaigns'=>AdCampaign::where('is_active',true)->count(),'published_articles'=>Article::whereNotNull('published_at')->count()],'trending'=>SearchTrend::orderByDesc('score')->limit(12)->get()]);}}
