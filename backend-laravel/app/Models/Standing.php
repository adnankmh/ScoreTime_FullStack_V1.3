<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Standing extends Model {
 protected $fillable=['competition_id','team_id','position','played','won','drawn','lost','goals_for','goals_against','goal_difference','points','form'];
 public function team(){return $this->belongsTo(Team::class);} public function competition(){return $this->belongsTo(Competition::class);}
}
