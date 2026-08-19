<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class TeamOfWeekEntry extends Model {
 protected $guarded=[];
 public function player(){ return $this->belongsTo(Player::class); }
 public function team(){ return $this->belongsTo(Team::class); }
 public function competition(){ return $this->belongsTo(Competition::class); }
}
