@extends('layouts.app')
@section('title','ScoreTime Control Room')
@section('content')
<div class="admin-head"><div><span class="eyebrow">SCORETIME OPERATIONS</span><h1>Adnan Control Room</h1><p class="muted">Football data, editorial, design, security, monetization and live operations from one console.</p></div><span class="pill">SUPER ADMIN</span></div>
<div class="metric-grid">@foreach($stats as $k=>$v)<div class="metric"><span class="eyebrow">{{strtoupper(str_replace('_',' ',$k))}}</span><strong>{{$v}}</strong></div>@endforeach</div>
<div class="section-head"><div><span class="eyebrow">CORE SYSTEMS</span><h2>Platform management</h2></div></div>
<div class="match-grid">
<a class="news-card" href="{{route('admin.world-data.index')}}"><span class="eyebrow">GLOBAL DATA</span><h3>World Football Data</h3><p class="muted">Countries, competitions, clubs, national teams, squads, fixtures and standings.</p></a>
<a class="news-card" href="{{route('admin.no-code-studio.index')}}"><span class="eyebrow">EXPERIENCE OS</span><h3>No-Code Experience Studio</h3><p class="muted">Pages, menus, campaigns, A/B experiments, scheduled design and white-label brands.</p></a>
<a class="news-card" href="{{route('admin.design-studio.index')}}"><span class="eyebrow">VISUAL SYSTEM</span><h3>Design Studio</h3><p class="muted">Branding, colors, layouts, navigation and feature switches for Web + Flutter.</p></a>
<a class="news-card" href="{{route('admin.articles.index')}}"><span class="eyebrow">EDITORIAL</span><h3>{{__('ui.newsroom')}}</h3><p class="muted">Create and publish original or licensed-attributed football coverage.</p></a>
<a class="news-card" href="{{route('admin.matches.index')}}"><span class="eyebrow">LIVE OPS</span><h3>Match Operations</h3><p class="muted">Score, minute, state and realtime match controls.</p></a>
<a class="news-card" href="{{route('admin.security')}}"><span class="eyebrow">SECURITY</span><h3>Security Center</h3><p class="muted">Audit logs, access policy, 2FA readiness and privileged activity.</p></a>
<a class="news-card" href="{{route('admin.users.index')}}"><span class="eyebrow">IDENTITY</span><h3>{{__('ui.users')}}</h3><p class="muted">Users, access status and account operations.</p></a>
<a class="news-card" href="{{route('admin.media.index')}}"><span class="eyebrow">MEDIA</span><h3>Media Library</h3><p class="muted">Images, video, credits and licensed media assets.</p></a>
<a class="news-card" href="{{route('admin.ads.index')}}"><span class="eyebrow">MONETIZATION</span><h3>Ads & Sponsors</h3><p class="muted">Campaigns, placements, sponsors and performance controls.</p></a>
<a class="news-card" href="{{route('admin.account.edit')}}"><span class="eyebrow">ADMIN IDENTITY</span><h3>Adnan Account</h3><p class="muted">Change administrator name, email and password.</p></a>
<a class="news-card" href="{{route('admin.v06')}}"><span class="eyebrow">INTELLIGENCE</span><h3>Football Intelligence Ops</h3><p class="muted">Visual match data, discovery, premium and fan telemetry.</p></a>
<a class="news-card" href="{{route('admin.v07')}}"><span class="eyebrow">REALTIME</span><h3>Realtime & Community Ops</h3><p class="muted">Live delivery, transfer intelligence, community and engagement health.</p></a>
</div>
@endsection
