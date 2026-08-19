<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class LiveCommentary extends Model { protected $fillable=['football_match_id','team_id','player_id','provider_event_id','minute','stoppage','type','text','importance','payload']; protected $casts=['payload'=>'array']; public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');} public function team(){return $this->belongsTo(Team::class);} public function player(){return $this->belongsTo(Player::class);} }
