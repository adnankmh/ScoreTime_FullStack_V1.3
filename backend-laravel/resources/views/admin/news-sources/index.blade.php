@extends('layouts.app')
@section('content')
<div class="container"><h1>ScoreTime News Source Manager</h1>
<p>Only add licensed, partner, or explicitly permitted RSS/API sources. Attribution remains mandatory.</p>
<form method="POST" action="{{ route('admin.news-sources.store') }}">@csrf
<div class="grid">
<input name="name" placeholder="Source name" required>
<select name="type"><option>rss</option><option>api</option><option>partner</option></select>
<input name="feed_url" placeholder="Feed/API URL" required>
<input name="homepage_url" placeholder="Homepage URL">
<select name="license_status"><option value="review">Needs review</option><option value="licensed">Licensed</option><option value="rss-permitted">RSS permitted</option><option value="partner">Partner</option><option value="blocked">Blocked</option></select>
<input name="trust_score" type="number" min="0" max="100" value="70">
<label><input type="checkbox" name="enabled" value="1"> Enabled</label>
<button type="submit">Save source</button></div></form>
<table><thead><tr><th>Name</th><th>Type</th><th>License</th><th>Enabled</th><th>Trust</th><th></th></tr></thead><tbody>
@foreach($sources as $s)<tr><td>{{ $s->name }}</td><td>{{ $s->type }}</td><td>{{ $s->license_status }}</td><td>{{ $s->enabled?'Yes':'No' }}</td><td>{{ $s->trust_score }}</td><td><form method="POST" action="{{ route('admin.news-sources.destroy',$s) }}">@csrf @method('DELETE')<button>Delete</button></form></td></tr>@endforeach
</tbody></table>{{ $sources->links() }}</div>
@endsection
