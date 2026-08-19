<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class CompetitionBracketNode extends Model {
 protected $guarded=[];
 public function footballMatch(){ return $this->belongsTo(FootballMatch::class,'football_match_id'); }
 public function competition(){ return $this->belongsTo(Competition::class); }
}
