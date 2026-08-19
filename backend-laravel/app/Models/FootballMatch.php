<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class FootballMatch extends Model {
 protected $table='football_matches';
 protected $fillable=['competition_id','home_team_id','away_team_id','kickoff_at','status','minute','home_score','away_score','venue','round','tv_channels','stats','events','lineups','provider_id','home_xg','away_xg','shot_map','momentum','referee','attendance','revision','last_synced_at','broadcast_meta','realtime_state','realtime_heartbeat_at'];
 protected $casts=['kickoff_at'=>'datetime','tv_channels'=>'array','stats'=>'array','events'=>'array','lineups'=>'array','shot_map'=>'array','momentum'=>'array','home_xg'=>'float','away_xg'=>'float','last_synced_at'=>'datetime','broadcast_meta'=>'array','realtime_heartbeat_at'=>'datetime'];
 public function competition(){return $this->belongsTo(Competition::class);} public function homeTeam(){return $this->belongsTo(Team::class,'home_team_id');} public function awayTeam(){return $this->belongsTo(Team::class,'away_team_id');}
 public function matchEvents(){return $this->hasMany(MatchEvent::class)->orderBy('minute');} public function predictions(){return $this->hasMany(Prediction::class);} public function fanMessages(){return $this->hasMany(FanMessage::class)->latest();}
 public function lineupEntries(){return $this->hasMany(MatchLineupEntry::class)->with(['player','team']);}
 public function shots(){return $this->hasMany(MatchShot::class)->orderBy('minute');} public function momentumPoints(){return $this->hasMany(MatchMomentumPoint::class)->orderBy('minute');} public function commentaries(){return $this->hasMany(LiveCommentary::class)->orderBy('id');} public function heatmapPoints(){return $this->hasMany(PlayerHeatmapPoint::class);}
}