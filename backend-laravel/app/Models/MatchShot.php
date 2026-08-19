<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class MatchShot extends Model{protected $fillable=['football_match_id','team_id','player_id','minute','x','y','xg','outcome','body_part','is_big_chance'];protected $casts=['x'=>'float','y'=>'float','xg'=>'float','is_big_chance'=>'boolean'];public function player(){return $this->belongsTo(Player::class);}public function team(){return $this->belongsTo(Team::class);}public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');}}
