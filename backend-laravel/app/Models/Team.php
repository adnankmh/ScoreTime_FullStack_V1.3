<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Team extends Model {
 protected $casts=['social_links'=>'array','last_synced_at'=>'datetime'];
 protected $fillable=['provider_id','name_ar','name_en','slug','short_name','country','logo_url','stadium','founded_year','primary_color','coach_name','website_url','city','social_links','team_type','national_code','venue_name','venue_city','venue_capacity','venue_image_url','last_synced_at'];
 public function homeMatches(){return $this->hasMany(FootballMatch::class,'home_team_id');}
 public function awayMatches(){return $this->hasMany(FootballMatch::class,'away_team_id');}
 public function players(){return $this->hasMany(Player::class);}
 public function coaches(){return $this->hasMany(Coach::class);}
 public function competitions(){return $this->belongsToMany(Competition::class)->withPivot('season')->withTimestamps();}
}
