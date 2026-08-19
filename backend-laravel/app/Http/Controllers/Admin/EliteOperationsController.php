<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller; use App\Models\{Achievement,AdCampaign,MediaAsset,PlayerInjury,Sponsor,PushDevice,Friendship,MatchSubscription,TeamFollow};
class EliteOperationsController extends Controller {public function index(){return view('admin.elite.index',['stats'=>['injuries'=>PlayerInjury::count(),'media'=>MediaAsset::count(),'campaigns'=>AdCampaign::where('is_active',true)->count(),'sponsors'=>Sponsor::where('is_active',true)->count(),'push_devices'=>PushDevice::where('enabled',true)->count(),'friendships'=>Friendship::where('status','accepted')->count(),'subscriptions'=>MatchSubscription::count(),'team_follows'=>TeamFollow::count()],'achievements'=>Achievement::orderBy('points')->get()]);}}
