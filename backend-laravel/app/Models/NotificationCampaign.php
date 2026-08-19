<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationCampaign extends Model { protected $fillable=['name','title','body','audience','audience_rules','data','status','scheduled_at','sent_at','sent_count','created_by']; protected $casts=['title'=>'array','body'=>'array','audience_rules'=>'array','data'=>'array','scheduled_at'=>'datetime','sent_at'=>'datetime']; }
