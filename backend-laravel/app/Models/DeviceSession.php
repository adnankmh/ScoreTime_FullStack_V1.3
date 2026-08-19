<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class DeviceSession extends Model {
 protected $fillable=['user_id','token_hash','device_name','platform','ip_address','user_agent','last_seen_at','revoked_at'];
 protected $hidden=['token_hash']; protected $casts=['last_seen_at'=>'datetime','revoked_at'=>'datetime'];
 public function user(){return $this->belongsTo(User::class);}
}
