@extends('layouts.app')
@section('content')
@php($blocks=count($homeBlocks??[]) ? $homeBlocks : [['type'=>'hero','enabled'=>true],['type'=>'live_matches','enabled'=>true,'limit'=>6],['type'=>'breaking_news','enabled'=>true,'limit'=>5],['type'=>'competitions','enabled'=>true,'limit'=>10],['type'=>'latest_news','enabled'=>true,'limit'=>9]])
@php($branding=$remoteDesign['branding']??[])
@foreach($blocks as $block)
 @continue(($block['enabled']??true)!==true)
 @php($limit=(int)($block['limit']??6))
 @switch($block['type']??'')
  @case('hero')
   <section class="hero"><div class="panel hero-main"><span class="eyebrow">LIVE FOOTBALL • NEWS • DATA</span><h1>{{$block['title']??(app()->getLocale()==='ar'?'كل كرة القدم في مكان واحد':'Football, unified.')}}</h1><p class="muted">{{$branding['tagline']??(app()->getLocale()==='ar'?'نتائج مباشرة، أخبار، بطولات، إحصائيات وتنبيهات مخصصة.':'Live scores, editorial coverage, competitions and personalized alerts.')}}</p><div><a class="btn primary" href="/matches">{{__('ui.matches')}}</a> <a class="btn ghost" href="/news">{{__('ui.news')}}</a></div></div><div class="metric-grid"><div class="metric"><span class="eyebrow">LIVE</span><strong>{{$live->count()}}</strong><span class="muted">{{__('ui.matches')}}</span></div><div class="metric"><span class="eyebrow">TODAY</span><strong>{{$matches->count()}}</strong><span class="muted">{{__('ui.matches')}}</span></div><div class="metric"><span class="eyebrow">NEWS</span><strong>{{$news->count()}}</strong><span class="muted">{{__('ui.news')}}</span></div><div class="metric"><span class="eyebrow">LEAGUES</span><strong>{{$competitions->count()}}</strong><span class="muted">{{__('ui.competitions')}}</span></div></div></section>
  @break
  @case('live_matches') @case('favorite_matches')
   @php($list=($block['type']==='live_matches'?$live:$matches)->take($limit))
   <div class="section-head"><div><span class="eyebrow">{{$block['type']==='live_matches'?'LIVE NOW':'MATCH CENTER'}}</span><h2>{{__('ui.matches')}}</h2></div><a class="link" href="/matches">{{__('ui.matches')}} →</a></div><div class="match-grid">@forelse($list as $m)<a class="match-card" href="{{route('match.show',$m)}}"><div class="pill">{{$m->competition?->name_ar ?? 'Football'}}</div><p class="muted">{{$m->kickoff_at?->format('H:i')}} · {{$m->round}}</p><h3>{{$m->homeTeam?->name_ar}} <span class="score">{{$m->home_score}} - {{$m->away_score}}</span> {{$m->awayTeam?->name_ar}}</h3>@if(in_array($m->status,['live','halftime']))<span class="live">● {{$m->minute}}' {{__('ui.live')}}</span>@endif</a>@empty<div class="panel">No matches right now.</div>@endforelse</div>
  @break
  @case('breaking_news')
   @php($items=$news->where('is_breaking',true)->take($limit))
   <div class="section-head"><div><span class="eyebrow">BREAKING</span><h2>{{__('ui.news')}}</h2></div></div><div class="news-grid">@foreach($items as $a)<a class="news-card" href="{{route('news.show',$a->slug)}}"><span class="live">● BREAKING</span><h3>{{$a->title}}</h3><p class="muted">{{$a->excerpt}}</p></a>@endforeach</div>
  @break
  @case('latest_news')
   <div class="section-head"><div><span class="eyebrow">EDITORIAL</span><h2>{{__('ui.news')}}</h2></div><a class="link" href="/news">{{__('ui.news')}} →</a></div><div class="news-grid">@foreach($news->take($limit) as $a)<a class="news-card" href="{{route('news.show',$a->slug)}}"><span class="pill">{{$a->category}}</span><h3>{{$a->title}}</h3><p class="muted">{{$a->excerpt}}</p></a>@endforeach</div>
  @break
  @case('competitions')
   <div id="competitions" class="section-head"><div><span class="eyebrow">COMPETITIONS</span><h2>{{__('ui.competitions')}}</h2></div></div><div class="news-grid">@foreach($competitions->take($limit) as $c)<a class="news-card" href="{{route('competition.show',$c)}}"><span class="pill">{{$c->country}}</span><h3>{{$c->name_ar}}</h3><p class="muted">{{$c->season}}</p></a>@endforeach</div>
  @break
  @case('transfers')
   <div class="section-head"><div><span class="eyebrow">MARKET</span><h2>Transfers</h2></div><a class="link" href="/transfers">Open →</a></div><div class="panel">Transfer Intelligence is enabled. Open the Transfer Center for the full feed.</div>
  @break
  @default
   <div class="panel"><span class="eyebrow">{{$block['type']??'BLOCK'}}</span><p class="muted">This block is ready for its module renderer.</p></div>
 @endswitch
@endforeach
@endsection
