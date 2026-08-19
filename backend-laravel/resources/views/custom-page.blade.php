@extends('layouts.app')
@section('title',$page->seo['title']??($page->title[app()->getLocale()]??$page->title['en']??$page->slug))
@section('content')
@php($blocks=$page->blocks??[])
<div class="custom-page-head"><span class="eyebrow">CUSTOM EXPERIENCE</span><h1>{{$page->title[app()->getLocale()]??$page->title['en']??$page->slug}}</h1></div>
@foreach($blocks as $b) @continue(($b['enabled']??true)===false) @php($cfg=$b['config']??$b)
 @if(($b['type']??'')==='hero')<section class="dynamic-hero" @if(!empty($cfg['image'])) style="background-image:linear-gradient(90deg,rgba(5,10,18,.92),rgba(5,10,18,.3)),url('{{ $cfg['image'] }}')" @endif><span class="eyebrow">FEATURED</span><h2>{{$cfg['headline']??'Football, your way.'}}</h2><p>{{$cfg['subheadline']??''}}</p>@if(!empty($cfg['cta']))<a class="btn primary" href="{{route('matches')}}">{{$cfg['cta']}}</a>@endif</section>
 @elseif(($b['type']??'')==='rich_text')<section class="panel dynamic-copy"><p>{{$cfg['text']??''}}</p></section>
 @elseif(($b['type']??'')==='live_matches')<section class="panel"><h2>Live Matches</h2><div class="dynamic-list">@forelse(($b['data']??[]) as $m)<a class="dynamic-row" href="{{route('match.show',$m['id'])}}"><span><b>{{$m['home']}}</b> vs <b>{{$m['away']}}</b><small>{{$m['competition']??''}}</small></span><strong>{{$m['home_score']}}–{{$m['away_score']}}</strong></a>@empty<p class="muted">No live matches now.</p>@endforelse</div><a class="btn ghost" href="{{route('matches')}}">Open Match Center</a></section>
 @elseif(($b['type']??'')==='latest_news'||($b['type']??'')==='breaking_news')<section class="panel"><h2>{{($b['type']??'')==='breaking_news'?'Breaking News':'Latest News'}}</h2><div class="dynamic-list">@foreach(($b['data']??[]) as $a)<a class="dynamic-row" href="{{route('news.show',$a['slug'])}}"><span><b>{{$a['title']}}</b><small>{{$a['excerpt']??''}}</small></span></a>@endforeach</div><a class="btn ghost" href="{{route('news')}}">Open Newsroom</a></section>
 @elseif(($b['type']??'')==='transfers')<section class="panel"><h2>Transfer Intelligence</h2><div class="dynamic-list">@foreach(($b['data']??[]) as $t)<div class="dynamic-row"><span><b>{{$t['player']}}</b><small>{{$t['from']}} → {{$t['to']}}</small></span><strong>{{$t['status']}}</strong></div>@endforeach</div><a class="btn ghost" href="{{route('transfers')}}">Transfers</a></section>
 @else<section class="panel"><span class="eyebrow">{{strtoupper(str_replace('_',' ',$b['type']??'block'))}}</span><p class="muted">Configured block.</p></section>@endif
@endforeach
@endsection
