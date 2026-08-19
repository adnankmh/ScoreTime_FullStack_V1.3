@extends('layouts.app')
@section('title','ScoreTime — '.__('ui.every_moment_counts'))
@section('description',__('ui.hero_subtitle'))
@section('content')
@php($isAr=app()->getLocale()==='ar')
@php($featuredLive=$live->first() ?? $matches->first())
@php($featuredNews=$news->first())

<section class="st-hero">
  <div class="st-hero-copy">
    <div class="st-kicker"><span class="st-live-dot"></span>SCORETIME • {{strtoupper(__('ui.world'))}}</div>
    <h1>{{__('ui.every_moment_counts')}}.<br><span>{{__('ui.live')}}. {{__('ui.world')}}.</span></h1>
    <p>{{__('ui.hero_subtitle')}}</p>
    <div class="st-hero-actions">
      <a class="btn primary" href="/matches">{{__('ui.explore')}} {{__('ui.matches')}} →</a>
      <a class="btn ghost" href="/world">{{__('ui.explore')}} {{__('ui.world')}}</a>
    </div>
    <div class="st-hero-stats">
      <div><strong>{{$live->count()}}</strong><span>{{__('ui.live')}}</span></div>
      <div><strong>{{$matches->count()}}</strong><span>{{__('ui.today')}}</span></div>
      <div><strong>{{$competitions->count()}}</strong><span>{{__('ui.competitions')}}</span></div>
      <div><strong>{{$news->count()}}</strong><span>{{__('ui.news')}}</span></div>
    </div>
  </div>
  <div class="st-hero-console">
    @if($featuredLive)
      <div class="st-console-head"><span>{{$isAr ? ($featuredLive->competition?->name_ar ?? $featuredLive->competition?->name_en) : ($featuredLive->competition?->name_en ?? $featuredLive->competition?->name_ar)}}</span><b class="st-live-pill">{{$featuredLive->status==='live' ? 'LIVE '.$featuredLive->minute."'" : strtoupper($featuredLive->status)}}</b></div>
      <a href="{{route('match.show',$featuredLive)}}" class="st-score-stage">
        <div class="st-team"><i>{{mb_substr($isAr ? ($featuredLive->homeTeam?->name_ar ?? 'H') : ($featuredLive->homeTeam?->name_en ?? 'H'),0,2)}}</i><span>{{$isAr ? ($featuredLive->homeTeam?->name_ar ?? 'Home') : ($featuredLive->homeTeam?->name_en ?? 'Home')}}</span></div>
        <strong>{{$featuredLive->status==='scheduled' ? ($featuredLive->kickoff_at?->format('H:i') ?? '—') : ($featuredLive->home_score.' : '.$featuredLive->away_score)}}</strong>
        <div class="st-team"><i>{{mb_substr($isAr ? ($featuredLive->awayTeam?->name_ar ?? 'A') : ($featuredLive->awayTeam?->name_en ?? 'A'),0,2)}}</i><span>{{$isAr ? ($featuredLive->awayTeam?->name_ar ?? 'Away') : ($featuredLive->awayTeam?->name_en ?? 'Away')}}</span></div>
      </a>
    @else
      <div class="st-console-empty">{{__('ui.data_pending')}}</div>
    @endif
    <p class="muted">{{__('ui.analytics_verified_only')}}</p>
  </div>
</section>

<section class="st-leagues">
@forelse($competitions->take(8) as $c)
  <a href="{{route('competition.show',$c)}}"><i>◈</i><span>{{$isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar)}}</span></a>
@empty
  <span><i>◈</i>{{__('ui.data_pending')}}</span>
@endforelse
</section>

<section class="st-dashboard-grid">
  <div class="st-panel">
    <div class="st-panel-head"><div><span>LIVE MATCHES</span><h2>Match Pulse</h2></div><a href="/matches">View all</a></div>
    <div class="st-live-list">
      @forelse(($live->count() ? $live : $matches)->take(6) as $m)
      <a href="{{route('match.show',$m)}}" class="st-match-row">
        <time>{{in_array($m->status,['live','halftime']) ? ($m->minute."'") : ($m->kickoff_at?->format('H:i') ?? '--')}}</time>
        <div><span>{{$isAr ? ($m->homeTeam?->name_ar ?? 'Home') : ($m->homeTeam?->name_en ?? 'Home')}}</span><span>{{$isAr ? ($m->awayTeam?->name_ar ?? 'Away') : ($m->awayTeam?->name_en ?? 'Away')}}</span></div>
        <strong><b>{{$m->home_score}}</b><b>{{$m->away_score}}</b></strong>
      </a>
      @empty<div class="muted">No live matches loaded.</div>@endforelse
    </div>
  </div>

  <div class="st-panel">
    <div class="st-panel-head"><div><span>TOP NEWS</span><h2>What matters now</h2></div><a href="/news">View all</a></div>
    <div class="st-news-list">
      @forelse($news->take(5) as $i=>$n)
      <a href="{{route('news.show',$n)}}" class="st-news-row {{$i===0?'feature':''}}"><i>{{$n->is_breaking?'⚡':'ST'}}</i><div><small>{{$n->category ?: 'Football'}}</small><strong>{{$n->title}}</strong><span>{{$n->published_at?->diffForHumans()}}</span></div></a>
      @empty<div class="muted">No published stories yet.</div>@endforelse
    </div>
  </div>

  <div class="st-panel">
    <div class="st-panel-head"><div><span>PLAYER RADAR</span><h2>Top performers</h2></div><a href="/world/players">Explore</a></div>
    <div class="st-ranking">
      @forelse($topPlayers as $i=>$player)
      <div><b>{{$i+1}}</b><i>{{mb_substr($player->name,0,2)}}</i><span><strong>{{$player->name}}</strong><small>{{$player->team?->name_en ?? $player->nationality}}</small></span><em>{{number_format($player->rating,1)}}</em></div>
      @empty<div class="muted">{{__('ui.data_pending')}}</div>@endforelse
    </div>
  </div>
</section>

<div class="section-head"><div><span class="eyebrow">SCORETIME INTELLIGENCE</span><h2>Read the match, not just the score</h2></div><a class="link" href="/matches">Open Match Center →</a></div>
<section class="st-intelligence">
  @foreach([__('ui.shot_map'),__('ui.momentum'),__('ui.predictions')] as $feature)
  <div class="st-panel"><div class="st-panel-head"><div><span>SCORETIME</span><h2>{{$feature}}</h2></div></div><p class="muted">{{__('ui.analytics_verified_only')}}</p><a class="btn ghost compact" href="/matches">{{__('ui.matches')}}</a></div>
  @endforeach
</section>

<div class="section-head"><div><span class="eyebrow">YOUR FOOTBALL</span><h2>Personalized, connected, global</h2></div></div>
<section class="st-personal-grid">
  <div class="st-panel"><h3>{{__('ui.favorites')}}</h3><p class="muted">{{__('ui.sign_in_customize')}}</p><a class="btn ghost compact" href="/login">{{__('ui.login')}}</a></div>
  <div class="st-panel"><h3>{{__('ui.tv_guide')}}</h3><p class="muted">{{__('ui.broadcast_verified_only')}}</p></div>
  <div class="st-panel"><h3>{{__('ui.predictions')}}</h3><p class="muted">{{__('ui.sign_in_customize')}}</p><div class="st-fan-metrics"><b>—<small>XP</small></b><b>—<small>{{__('ui.predictions')}}</small></b><b>—<small>{{__('ui.challenges')}}</small></b></div></div>
  <div class="st-panel"><h3>Transfer Intelligence</h3><p class="muted">Confidence, verification state, source attribution and movement tracking.</p><a class="btn ghost compact" href="/transfers">Open Transfer Desk</a></div>
</section>

<section class="st-capabilities">
  @foreach([['⚡','Real-Time Data','Lightning-fast scores and events'],['◫','Deep Statistics','xG-ready data architecture'],['⌁','Tactical Insights','Heatmaps, maps and momentum'],['◎','Personalized','Teams, players and alerts'],['◉','Global Coverage','World football catalog'],['◆','Secure & Reliable','Hardened Laravel infrastructure']] as $x)
  <div><i>{{$x[0]}}</i><span><strong>{{$x[1]}}</strong><small>{{$x[2]}}</small></span></div>
  @endforeach
</section>
@endsection
