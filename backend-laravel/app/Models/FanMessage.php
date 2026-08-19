<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FanMessage extends Model {
 protected $fillable=['user_id','football_match_id','body','status'];
 public function user(){return $this->belongsTo(User::class);} public function match(){return $this->belongsTo(FootballMatch::class,'football_match_id');}
}
