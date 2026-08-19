<?php
namespace App\Services;
use App\Models\{Article,FootballMatch,TeamFollow};
use App\Models\User;
class PersonalizedFeedService{
 public function for(?User $user):array{
  if(!$user)return ['news'=>Article::whereNotNull('published_at')->latest('published_at')->limit(15)->get(),'matches'=>FootballMatch::with(['homeTeam','awayTeam','competition'])->whereBetween('kickoff_at',[now()->subHours(3),now()->addDays(2)])->orderBy('kickoff_at')->limit(20)->get()];
  $teamIds=TeamFollow::where('user_id',$user->id)->pluck('team_id');
  $matches=FootballMatch::with(['homeTeam','awayTeam','competition'])->where(fn($q)=>$q->whereIn('home_team_id',$teamIds)->orWhereIn('away_team_id',$teamIds))->where('kickoff_at','>=',now()->subHours(3))->orderBy('kickoff_at')->limit(30)->get();
  return ['news'=>Article::whereNotNull('published_at')->latest('published_at')->limit(20)->get(),'matches'=>$matches,'followed_team_ids'=>$teamIds];
 }
}
