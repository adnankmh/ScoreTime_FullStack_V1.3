<?php
use Illuminate\Support\Facades\{Artisan,Schedule};
Artisan::command('scoretime:sync', function(){ $this->call('football:sync-live',['--events'=>true]); })->purpose('Synchronize live data from the configured licensed football provider');
if (env('FOOTBALL_LIVE_SCHEDULER', false)) {
    Schedule::command('football:sync-live --events')->everyMinute()->withoutOverlapping()->onOneServer();
}

Artisan::command('notifications:dispatch-campaigns', function(){
    $svc=app(\App\Services\FcmPushService::class); $count=0;
    \App\Models\NotificationCampaign::whereIn('status',['scheduled','draft'])->where(function($q){$q->whereNull('scheduled_at')->orWhere('scheduled_at','<=',now());})->chunkById(20,function($campaigns)use($svc,&$count){
        foreach($campaigns as $campaign){
            if($campaign->status==='draft' && $campaign->scheduled_at===null) continue;
            $users=\App\Models\User::query(); if($campaign->audience==='premium')$users->where('plan','premium'); elseif($campaign->audience==='free')$users->where('plan','free');
            $sent=0; $users->select('id','locale')->chunkById(200,function($batch)use($campaign,$svc,&$sent){foreach($batch as $u){$locale=$u->locale?:'en';$title=$campaign->title[$locale]??$campaign->title['en']??(array_values($campaign->title)[0]??'Update');$body=$campaign->body[$locale]??$campaign->body['en']??(array_values($campaign->body)[0]??'');\App\Models\UserNotification::create(['id'=>(string)\Illuminate\Support\Str::uuid(),'user_id'=>$u->id,'type'=>'campaign','title'=>$title,'body'=>$body,'data'=>array_merge($campaign->data??[],['campaign_id'=>$campaign->id])]);$svc->sendToUser($u->id,$title,$body,['campaign_id'=>$campaign->id]);$sent++;}});$campaign->update(['status'=>'sent','sent_at'=>now(),'sent_count'=>$sent]);$count++;
        }
    }); $this->info("Dispatched {$count} campaigns.");
})->purpose('Dispatch due no-code notification campaigns');

if (env('NOTIFICATION_CAMPAIGN_SCHEDULER', true)) {
    Schedule::command('notifications:dispatch-campaigns')->everyMinute()->withoutOverlapping()->onOneServer();
}

if (env('FOOTBALL_CATALOG_SCHEDULER', false)) {
    Schedule::command('football:sync-global catalog --season='.(int)env('FOOTBALL_CATALOG_SEASON', date('Y')))
        ->dailyAt('03:10')->withoutOverlapping()->onOneServer();
}
