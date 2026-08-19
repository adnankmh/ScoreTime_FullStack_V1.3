<?php
namespace App\Models; use Illuminate\Database\Eloquent\Model;
class PredictionSeasonScore extends Model { protected $fillable=['prediction_season_id','user_id','points','exact_scores','correct_outcomes','current_streak','best_streak']; public function season(){return $this->belongsTo(PredictionSeason::class,'prediction_season_id');} public function user(){return $this->belongsTo(User::class);} }
