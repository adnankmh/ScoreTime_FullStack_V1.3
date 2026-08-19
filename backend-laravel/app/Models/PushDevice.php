<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PushDevice extends Model { protected $fillable=['user_id','token','platform','device_name','locale','enabled','last_seen_at']; protected $casts=['enabled'=>'boolean','last_seen_at'=>'datetime']; public function user(){return $this->belongsTo(User::class);} }
