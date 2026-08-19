<?php
namespace App\Services;
use App\Models\FootballMatch;
use Illuminate\Support\Carbon;
class FootballDataService {
 public function matchesForDate(?string $date=null){
   $day=$date?Carbon::parse($date):now();
   return FootballMatch::with(['competition','homeTeam','awayTeam'])
      ->whereDate('kickoff_at',$day->toDateString())->orderBy('kickoff_at')->get();
 }
 public function live(){return FootballMatch::with(['competition','homeTeam','awayTeam'])->whereIn('status',['live','halftime'])->orderBy('kickoff_at')->get();}
}
