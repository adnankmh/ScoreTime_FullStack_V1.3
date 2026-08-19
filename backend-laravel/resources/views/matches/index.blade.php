@extends('layouts.app')
@section('title',__('ui.matches').' — ScoreTime')
@section('content')
@php($isAr=app()->getLocale()==='ar')
<div class="admin-head"><div><span class="eyebrow">SCORETIME LIVE CENTER</span><h1>{{__('ui.matches')}}</h1></div><form><input type="date" name="date" value="{{request('date',now()->toDateString())}}" onchange="this.form.submit()"></form></div>
<div class="match-grid">@forelse($matches as $m)<a class="match-card" href="{{route('match.show',$m)}}"><div class="match-top"><span>{{$isAr ? ($m->competition?->name_ar ?? $m->competition?->name_en) : ($m->competition?->name_en ?? $m->competition?->name_ar)}}</span>@if(in_array($m->status,['live','halftime']))<span class="live">● {{$m->minute}}' {{__('ui.live')}}</span>@else<span>{{$m->kickoff_at?->format('H:i')}}</span>@endif</div><div class="match-teams"><span class="match-team">{{$isAr ? ($m->homeTeam?->name_ar ?? 'Home') : ($m->homeTeam?->name_en ?? 'Home')}}</span><span class="score">{{$m->home_score}} — {{$m->away_score}}</span><span class="match-team">{{$isAr ? ($m->awayTeam?->name_ar ?? 'Away') : ($m->awayTeam?->name_en ?? 'Away')}}</span></div><div class="match-top"><span>{{$m->round}}</span><span>{{$m->venue}}</span></div></a>@empty<div class="panel">No matches for this date.</div>@endforelse</div>
@endsection
