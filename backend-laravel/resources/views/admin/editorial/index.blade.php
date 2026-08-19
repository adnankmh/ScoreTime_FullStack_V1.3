@extends('layouts.app')
@section('content')
<div class="container"><h1>ScoreTime Newsroom</h1><p>Editorial review, attribution and approval workflow.</p>
@foreach($items as $item)<article class="card" style="margin-bottom:16px;padding:16px">
<strong>{{ $item->source?->name ?? 'Source' }}</strong> · {{ $item->status }}
<h3>{{ $item->source_title }}</h3>
@if($item->source_url)<a href="{{ $item->source_url }}" target="_blank" rel="noopener noreferrer">Open original source</a>@endif
<form method="POST" action="{{ route('admin.editorial.review',$item) }}">@csrf
<textarea name="editorial_summary" rows="5" style="width:100%">{{ $item->editorial_summary }}</textarea>
<button name="action" value="save">Save draft</button><button name="action" value="approve">Approve</button><button name="action" value="reject">Reject</button>
</form></article>@endforeach
{{ $items->links() }}</div>
@endsection
