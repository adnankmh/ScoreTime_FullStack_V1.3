@extends('layouts.app')
@section('title','Global Intelligence Center')
@section('content')
<div class="hero"><span class="eyebrow">SCORETIME CONTROL ROOM • INTELLIGENCE</span><h1>Global Football Operations</h1><p>Realtime visual intelligence, discovery, growth, premium and fan engagement telemetry.</p></div>
<div class="stat-grid">@foreach($metrics as $key=>$value)<div class="stat-card"><b>{{number_format($value)}}</b><span>{{str_replace('_',' ',ucwords($key,'_'))}}</span></div>@endforeach</div>
<div class="grid-2"><section class="panel"><h2>Trending Searches</h2><table class="data-table"><thead><tr><th>Query</th><th>Score</th></tr></thead><tbody>@forelse($trending as $trend)<tr><td>{{$trend->query}}</td><td>{{$trend->score}}</td></tr>@empty<tr><td colspan="2">No search telemetry yet.</td></tr>@endforelse</tbody></table></section><section class="panel"><h2>Intelligence Systems</h2><div class="feature-list"><div>✓ Tactical lineups & visual match data</div><div>✓ Shot map and momentum APIs</div><div>✓ Team hub: squad, injuries, transfers</div><div>✓ Challenges, levels and XP</div><div>✓ Search suggestions & trending</div><div>✓ Personalized feed & premium status</div><div>✓ PWA manifest, SEO metadata and sitemap-ready routes</div></div></section></div>
@endsection
