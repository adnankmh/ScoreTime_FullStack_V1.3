@extends('layouts.app')
@section('title','ScoreTime — The Next Generation Football Platform')
@section('description','ScoreTime brings live football, tactical intelligence, trusted news, transfers, rankings, TV schedules and fan experiences into one premium platform.')
@section('content')
@php($isAr=app()->getLocale()==='ar')
@php($featuredLive=$live->first() ?? $matches->first())
@php($featuredNews=$news->first())

<section class="st-hero">
  <div class="st-hero-copy">
    <div class="st-kicker"><span class="st-live-dot"></span>SCORETIME • NEXT-GEN FOOTBALL</div>
    <h1>Unlimited Football.<br><span>Real-Time. Everywhere.</span></h1>
    <p>Live scores, match intelligence, trusted stories, transfers, TV schedules and your personalized football universe in one premium experience.</p>
    <div class="st-hero-actions">
      <a class="btn primary" href="/matches">Explore Live Matches →</a>
      <a class="btn ghost" href="/world">Explore World Football</a>
    </div>
    <div class="st-hero-stats">
      <div><strong>{{$live->count()}}</strong><span>Live now</span></div>
      <div><strong>{{$matches->count()}}</strong><span>Today</span></div>
      <div><strong>{{$competitions->count()}}</strong><span>Competitions</span></div>
      <div><strong>{{$news->count()}}</strong><span>Stories</span></div>
    </div>
  </div>
  <div class="st-hero-console">
    @if($featuredLive)
      <div class="st-console-head"><span>{{$isAr ? ($featuredLive->competition?->name_ar ?? $featuredLive->competition?->name_en) : ($featuredLive->competition?->name_en ?? $featuredLive->competition?->name_ar)}}</span><b class="st-live-pill">{{$featuredLive->status==='live' ? 'LIVE '.$featuredLive->minute."'" : strtoupper($featuredLive->status)}}</b></div>
      <a href="{{route('match.show',$featuredLive)}}" class="st-score-stage">
        <div class="st-team"><i>{{mb_substr($isAr ? ($featuredLive->homeTeam?->name_ar ?? 'H') : ($featuredLive->homeTeam?->name_en ?? 'H'),0,2)}}</i><span>{{$isAr ? ($featuredLive->homeTeam?->name_ar ?? 'Home') : ($featuredLive->homeTeam?->name_en ?? 'Home')}}</span></div>
        <strong>{{$featuredLive->home_score}} : {{$featuredLive->away_score}}</strong>
        <div class="st-team"><i>{{mb_substr($isAr ? ($featuredLive->awayTeam?->name_ar ?? 'A') : ($featuredLive->awayTeam?->name_en ?? 'A'),0,2)}}</i><span>{{$isAr ? ($featuredLive->awayTeam?->name_ar ?? 'Away') : ($featuredLive->awayTeam?->name_en ?? 'Away')}}</span></div>
      </a>
    @else
      <div class="st-console-empty">Connect a licensed football data provider to activate the live command center.</div>
    @endif
    <div class="st-momentum"><svg viewBox="0 0 500 90" preserveAspectRatio="none"><defs><linearGradient id="stline" x1="0" x2="1"><stop stop-color="#8b5cf6"/><stop offset="1" stop-color="#19d8ff"/></linearGradient></defs><path d="M0,65 L30,62 L55,70 L85,47 L110,54 L145,31 L170,45 L200,21 L230,42 L260,26 L285,54 L315,17 L350,36 L380,14 L410,27 L440,20 L470,42 L500,22" fill="none" stroke="url(#stline)" stroke-width="4"/></svg></div>
    <div class="st-probs"><span><small>HOME</small><b>58%</b></span><span><small>DRAW</small><b>24%</b></span><span><small>AWAY</small><b>18%</b></span></div>
  </div>
</section>

<section class="st-leagues">
@forelse($competitions->take(8) as $c)
  <a href="{{route('competition.show',$c)}}"><i>◈</i><span>{{$isAr ? ($c->name_ar ?? $c->name_en) : ($c->name_en ?? $c->name_ar)}}</span></a>
@empty
  @foreach(['Champions League','Premier League','LaLiga','Serie A','Bundesliga','Ligue 1'] as $name)<span><i>◈</i>{{$name}}</span>@endforeach
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
      @foreach(['Impact Leader','Finisher','Creator','Progressor','Defender'] as $i=>$role)
      <div><b>{{$i+1}}</b><i>{{chr(65+$i)}}P</i><span><strong>{{$role}}</strong><small>ScoreTime Index</small></span><em>{{number_format(8.8-($i*.2),1)}}</em></div>
      @endforeach
    </div>
  </div>
</section>

<div class="section-head"><div><span class="eyebrow">SCORETIME INTELLIGENCE</span><h2>Read the match, not just the score</h2></div><a class="link" href="/matches">Open Match Center →</a></div>
<section class="st-intelligence">
  <div class="st-panel st-pitch-panel"><div class="st-panel-head"><div><span>TACTICAL LENS</span><h2>Pressure & activity map</h2></div></div><div class="st-pitch"><i class="hot h1"></i><i class="hot h2"></i><i class="hot h3"></i><b class="shot s1"></b><b class="shot s2"></b><b class="shot s3"></b></div></div>
  <div class="st-panel"><div class="st-panel-head"><div><span>MATCH MOMENTUM</span><h2>Territorial pressure</h2></div></div><div class="st-big-chart"><svg viewBox="0 0 500 220" preserveAspectRatio="none"><path d="M0,170 L35,150 L65,170 L95,105 L125,130 L160,75 L195,120 L230,55 L260,112 L300,82 L330,145 L365,50 L400,92 L430,38 L465,80 L500,48" fill="none" stroke="#19d8ff" stroke-width="4"/></svg></div></div>
  <div class="st-panel"><div class="st-panel-head"><div><span>ATTRIBUTE OVERVIEW</span><h2>Player radar</h2></div></div><div class="st-radar"><span>PACE</span><span>PASS</span><span>SHOT</span><span>DEF</span><span>PHYS</span><span>CREATIVE</span><i></i></div></div>
</section>

<div class="section-head"><div><span class="eyebrow">YOUR FOOTBALL</span><h2>Personalized, connected, global</h2></div></div>
<section class="st-personal-grid">
  <div class="st-panel"><h3>Follow Your Teams</h3><p class="muted">Smart alerts, news and match reminders for the clubs that matter to you.</p><div class="st-team-chips">@foreach(['RM','BAR','LIV','CITY','PSG','MIL'] as $x)<span>{{$x}}</span>@endforeach</div></div>
  <div class="st-panel"><h3>TV Guide</h3><p class="muted">A single schedule for live broadcasts and match windows.</p><div class="st-guide"><span>18:00 <b>Champions League</b><em>LIVE</em></span><span>20:30 <b>Premier League</b><em>LIVE</em></span><span>22:45 <b>LaLiga</b><em>TONIGHT</em></span></div></div>
  <div class="st-panel"><h3>Fan Zone</h3><p class="muted">Predictions, friends, mini leagues, XP, challenges and achievements.</p><div class="st-fan-metrics"><b>12<small>Picks</small></b><b>84%<small>Form</small></b><b>2.4K<small>XP</small></b></div></div>
  <div class="st-panel"><h3>Transfer Intelligence</h3><p class="muted">Confidence, verification state, source attribution and movement tracking.</p><a class="btn ghost compact" href="/transfers">Open Transfer Desk</a></div>
</section>

<section class="st-capabilities">
  @foreach([['⚡','Real-Time Data','Lightning-fast scores and events'],['◫','Deep Statistics','xG-ready data architecture'],['⌁','Tactical Insights','Heatmaps, maps and momentum'],['◎','Personalized','Teams, players and alerts'],['◉','Global Coverage','World football catalog'],['◆','Secure & Reliable','Hardened Laravel infrastructure']] as $x)
  <div><i>{{$x[0]}}</i><span><strong>{{$x[1]}}</strong><small>{{$x[2]}}</small></span></div>
  @endforeach
</section>
@endsection
