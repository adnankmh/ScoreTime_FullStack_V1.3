<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class MatchMomentumPoint extends Model{protected $fillable=['football_match_id','minute','value'];public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');}}
