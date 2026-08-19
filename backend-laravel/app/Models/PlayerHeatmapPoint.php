<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PlayerHeatmapPoint extends Model { protected $fillable=['football_match_id','team_id','player_id','x','y','weight','minute_from','minute_to']; protected $casts=['x'=>'float','y'=>'float','weight'=>'float']; public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');} public function player(){return $this->belongsTo(Player::class);} public function team(){return $this->belongsTo(Team::class);} }
