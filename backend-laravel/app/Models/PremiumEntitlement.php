<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PremiumEntitlement extends Model{protected $fillable=['user_id','feature','starts_at','ends_at','meta'];protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime','meta'=>'array'];public function user(){return $this->belongsTo(User::class);}}
