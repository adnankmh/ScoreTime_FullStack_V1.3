<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class MatchEvent extends Model { protected $fillable=['football_match_id','team_id','player_id','type','minute','extra_minute','title','meta']; protected function casts():array{return ['meta'=>'array'];} public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');} }
