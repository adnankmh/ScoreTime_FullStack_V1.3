<?php
namespace App\Services;
use App\Models\FootballMatch;
class RealtimeMatchService {
 public function snapshot(FootballMatch $match, int $afterCommentaryId=0): array {
  $match->load(['homeTeam:id,name_ar,name_en,logo_url','awayTeam:id,name_ar,name_en,logo_url','competition:id,name_ar,name_en']);
  $comments=$match->commentaries()->with(['team:id,name_ar,name_en,logo_url','player:id,name,photo_url'])->when($afterCommentaryId>0,fn($q)=>$q->where('id','>',$afterCommentaryId))->limit(100)->get();
  return ['match'=>$match,'commentary'=>$comments,'revision'=>(int)$match->revision,'heartbeat'=>$match->realtime_heartbeat_at,'state'=>$match->realtime_state,'server_time'=>now()->toIso8601String()];
 }
}
