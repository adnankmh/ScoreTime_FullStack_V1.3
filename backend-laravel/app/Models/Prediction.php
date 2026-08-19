<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Prediction extends Model {
 protected $fillable=['user_id','football_match_id','home_score','away_score','points','locked_at'];
 protected $casts=['locked_at'=>'datetime'];
 public function user(){return $this->belongsTo(User::class);} public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');}
}
