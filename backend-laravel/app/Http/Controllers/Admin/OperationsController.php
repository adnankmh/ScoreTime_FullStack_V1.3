<?php
namespace App\Http\Controllers\Admin;
use App\Http\Controllers\Controller;use App\Models\{AdCampaign,DataProviderSyncLog,MediaAsset,PushDevice,Sponsor,UserNotification};use App\Services\FootballProviderManager;
class OperationsController extends Controller { public function index(FootballProviderManager $m){return view('admin.operations.index',['provider'=>$m->health(),'syncLogs'=>DataProviderSyncLog::latest()->limit(20)->get(),'devices'=>PushDevice::where('enabled',true)->count(),'notifications'=>UserNotification::count(),'media'=>MediaAsset::count(),'sponsors'=>Sponsor::where('is_active',true)->count(),'campaigns'=>AdCampaign::where('is_active',true)->count()]);} }
