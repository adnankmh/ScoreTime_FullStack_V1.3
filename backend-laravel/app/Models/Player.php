<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Player extends Model {protected $fillable=['provider_id','team_id','name','first_name','last_name','slug','position','number','nationality','birth_date','height','weight','is_injured','photo_url','rating','goals','assists','appearances','last_synced_at']; protected function casts():array{return ['birth_date'=>'date','rating'=>'float','is_injured'=>'boolean','last_synced_at'=>'datetime'];} public function team(){return $this->belongsTo(Team::class);} public function injuries(){return $this->hasMany(PlayerInjury::class)->latest();} public function seasonStats(){return $this->hasMany(PlayerSeasonStat::class)->latest('season');}}
