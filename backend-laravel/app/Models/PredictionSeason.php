<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PredictionSeason extends Model { protected $fillable=['name','slug','starts_at','ends_at','is_active','scoring_rules']; protected $casts=['starts_at'=>'datetime','ends_at'=>'datetime','is_active'=>'boolean','scoring_rules'=>'array']; public function scores(){return $this->hasMany(PredictionSeasonScore::class);} }
