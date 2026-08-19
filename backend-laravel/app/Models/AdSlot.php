<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class AdSlot extends Model { protected $fillable=['key','name','placement','is_active','rules']; protected $casts=['is_active'=>'boolean','rules'=>'array']; public function campaigns(){return $this->hasMany(AdCampaign::class);} }
