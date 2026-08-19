<?php
namespace App\Services;
use App\Models\{Article,ArticleEngagementSignal,TeamFollow}; use App\Models\User;
class NewsPersonalizationService {
 public function feed(?User $user): array {
  $base=Article::whereNotNull('published_at')->latest('published_at'); if(!$user)return $base->limit(30)->get()->all();
  $categories=ArticleEngagementSignal::where('user_id',$user->id)->join('articles','articles.id','=','article_engagement_signals.article_id')->selectRaw('articles.category, SUM(article_engagement_signals.weight) as score')->groupBy('articles.category')->orderByDesc('score')->limit(5)->pluck('category')->filter()->values();
  $items=(clone $base)->when($categories->isNotEmpty(),fn($q)=>$q->orderByRaw('CASE WHEN category IN ('.implode(',',array_fill(0,$categories->count(),'?')).') THEN 0 ELSE 1 END',$categories->all()))->limit(30)->get();
  return $items->all();
 }
}
