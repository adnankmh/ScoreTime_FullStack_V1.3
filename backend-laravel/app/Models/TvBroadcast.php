<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TvBroadcast extends Model { protected $guarded=[]; public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');} }
