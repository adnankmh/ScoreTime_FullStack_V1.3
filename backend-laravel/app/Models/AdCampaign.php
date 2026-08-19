<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdCampaign extends Model { protected $fillable=['sponsor_id','ad_slot_id','title','image_url','target_url','impressions','clicks','starts_at','ends_at','is_active']; protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime','is_active'=>'boolean']; public function sponsor(){return $this->belongsTo(Sponsor::class);} public function slot(){return $this->belongsTo(AdSlot::class,'ad_slot_id');} }
