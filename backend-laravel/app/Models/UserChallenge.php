<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class UserChallenge extends Model{protected $fillable=['creator_id','opponent_id','title','type','status','creator_score','opponent_score','starts_at','ends_at'];protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime'];public function creator(){return $this->belongsTo(User::class,'creator_id');}public function opponent(){return $this->belongsTo(User::class,'opponent_id');}}
