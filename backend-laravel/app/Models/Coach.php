<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Coach extends Model {protected $fillable=['provider_id','team_id','name','nationality','birth_date','photo_url','last_synced_at'];protected $casts=['birth_date'=>'date','last_synced_at'=>'datetime'];public function team(){return $this->belongsTo(Team::class);}}
