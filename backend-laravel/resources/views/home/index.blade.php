@extends('layouts.app')
@section('title','ScoreTime — Every Moment Counts')
@section('content')
@php($isAr=app()->getLocale()==='ar')
@php($featuredLive=$live->first() ?? $matches->first())
@php($featuredNews=$news->first())
<section class="hero-v2">
  <div class="hero-stage">
    <img src="/assets/brand/scoretime-logo-approved.png" class="hero-logo-watermark" alt="">
    <span class="eyebrow">SCORETIME • LIVE • INTELLIGENCE</span>
    <h1><span class="gradient">{{__('ui.hero_title')}}</span></h1>
    <p>{{__('ui.hero_subtitle')}}</p>
    <div class="hero-actions"><a class="btn primary" href="/matches">{{__('ui.match_center')}} →</a><a class="btn ghost" href="/news">{{__('ui.latest_news')}}</a><a class="btn ghost" href="/world">{{__('ui.world')}}</a></div>
    <div class="hero-meta"><span><b>{{$matches->count()}}</b><br>{{__('ui.today')}} {{__('ui.matches')}}</span><span><b>{{$live->count()}}</b><br>{{__('ui.live')}}</span><span><b>{{$competitions->count()}}</b><br>{{__('ui.competitions')}}</span><span><b>{{$news->count()}}</b><br>{{__('ui.news')}}</span></div>
  </div>
  <aside class="hero-live">
    <div class="live-card-head"><span class="pulse">{{$featuredLive && in_array($featuredLive->status,['live','halftime']) ? __('ui.live') : __('ui.featured')}}</span><a class="link" href="/matches">{{__('ui.view_all')}} →</a></div>
    @if($featuredLive)
    <a class="spotlight-match" href="{{route('match.show',$featuredLive)}}">
      <div class="league-line"><span>{{$isAr ? ($featuredLive->competition?->name_ar ?? $featuredLive->competition?->name_en) : ($featuredLive->competition?->name_en ?? $featuredLive->competition?->name_ar)}}</span><span>{{$featuredLive->round}}</span></div>
      <div class="teams-score">
        <div class="team-stack"><div class="team-badge">{{mb_substr($isAr ? ($featuredLive->homeTeam?->name_ar ?? 'H') : ($featuredLive->homeTeam?->name_en ?? 'H'),0,2)}}</div>{{$isAr ? ($featuredLive->homeTeam?->name_ar ?? 'Home') : ($featuredLive->homeTeam?->name_en ?? 'Home')}}</div>
        <div class="big-score">{{$featuredLive->home_score}} : {{$featuredLive->away_score}}</div>
        <div class="team-stack"><div class="team-badge">{{mb_substr($isAr ? ($featuredLive->awayTeam?->name_ar ?? 'A') : ($featuredLive->awayTeam?->name_en ?? 'A'),0,2)}}</div>{{$isAr ? ($featuredLive->awayTeam?->name_ar ?? 'Away') : ($featuredLive->awayTeam?->name_en ?? 'Away')}}</div>
      </div>
      <div class="live-footer"><span>{{$featuredLive->kickoff_at?->format('H:i')}}</span><span>•</span><span>{{$featuredLive->venue ?: 'Venue TBA'}}</span>@if($featuredLive->minute)<span>•</span><span>{{$featuredLive->minute}}'</span>@endif</div>
    </a>
    @else
    <div class="spotlight-match"><span class="muted">No featured match is loaded yet. Connect the licensed provider or use demo sync from the admin panel.</span></div>
    @endif
  </aside>
</section>

<section class="quick-grid" aria-label="ScoreTime shortcuts">
  <a class="quick-card" href="/matches"><span class="quick-icon">◉</span><span><strong>{{__('ui.live')}}</strong><small>{{__('ui.match_center')}}</small></span></a>
  <a class="quick-card" href="/world"><span class="quick-icon">◎</span><span><strong>{{__('ui.world')}}</strong><small>{{__('ui.competitions')}}</small></span></a>
  <a class="quick-card" href="/transfers"><span class="quick-icon">⇄</span><span><strong>{{__('ui.transfers')}}</strong><small>Market intelligence</small></span></a>
  <a class="quick-card" href="/leaderboard"><span class="quick-icon">◇</span><span><strong>{{__('ui.predictions')}}</strong><small>Fan leaderboard</small></span></a>
  <a class="quick-card" href="/news"><span class="quick-icon">▤</span><span><strong>{{__('ui.news')}}</strong><small>{{__('ui.breaking')}}</small></span></a>
</section>

<div class="section-head"><div><span class="eyebrow">LIVE & UPCOMING</span><h2>{{__('ui.matches')}}</h2></div><a class="link" href="/matches">{{__('ui.view_all')}} →</a></div>
<div class="match-grid">
@forelse(($live->count() ? $live : $matches)->take(6) as $m)
  <a class="match-card" href="{{route('match.show',$m)}}">
    <div class="match-top"><span>{{$isAr ? ($m->competition?->name_ar ?? $m->competition?->name_en) : ($m->competition?->name_en ?? $m->competition?->name_ar)}}</span>@if(in_array($m->status,['live','halftime']))<span class="live">● {{$m->minute}}'</span>@else<span>{{$m->kickoff_at?->format('H:i')}}</span>@endif</div>
    <div class="match-teams"><span class="match-team">{{$isAr ? ($m->homeTeam?->name_ar ?? 'Home') : ($m->homeTeam?->name_en ?? 'Home')}}</span><span class="score">{{$m->home_score}} — {{$m->away_score}}</span><span class="match-team">{{$isAr ? ($m->awayTeam?->name_ar ?? 'Away') : ($m->awayTeam?->name_en ?? 'Away')}}</span></div>
    <div class="match-top"><span>{{$m->round}}</span><span>{{$m->venue}}</span></div>
  </a>
@empty <div class="panel"><strong>Match feed ready.</strong><p class="muted">Connect the licensed provider from the admin Global Football Data section.</p></div>@endforelse
</div>

<div class="section-head"><div><span class="eyebrow">SCORETIME EDITORIAL</span><h2>{{__('ui.latest_news')}}</h2></div><a class="link" href="/news">{{__('ui.view_all')}} →</a></div>
<div class="news-layout">
  @if($featuredNews)<a class="news-feature" href="{{route('news.show',$featuredNews->slug)}}"><span class="pill">{{$featuredNews->category}}</span>@if($featuredNews->is_breaking)<span class="live">● {{__('ui.breaking')}}</span>@endif<h3>{{$featuredNews->title}}</h3><p class="muted">{{$featuredNews->excerpt}}</p></a>@else<div class="news-feature"><span class="eyebrow">NEWSROOM</span><h3>Editorial feed ready.</h3><p class="muted">Add licensed sources or original ScoreTime coverage from the admin newsroom.</p></div>@endif
  <div class="news-list">@foreach($news->skip(1)->take(5) as $a)<a class="news-card" href="{{route('news.show',$a->slug)}}"><span class="pill">{{$a->category}}</span><h3>{{$a->title}}</h3><small class="muted">{{$a->published_at?->diffForHumans()}}</small></a>@endforeach</div>
</div>

<div class="section-head"><div><span class="eyebrow">GLOBAL FOOTBALL</span><h2>{{__('ui.competitions')}}</h2></div><a class="link" href="/world">{{__('ui.world')}} →</a></div>
<div class="competition-rail">@foreach($competitions->take(10) as $c)<a class="competition-card" href="{{route('competition.show',$c)}}"><span class="pill">{{$c->country}}</span><strong>{{$isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar)}}</strong><small class="muted">{{$c->season}}</small></a>@endforeach</div>
@endsection
