<!doctype html>
@php($locale=app()->getLocale())
@php($rtl=$locale==='ar')
@php($theme=session('theme',auth()->user()?->theme ?? 'stadium'))
@php($fontScale=session('font_scale',auth()->user()?->font_scale ?? 1.0))
@php($designBoot=\Illuminate\Support\Facades\Schema::hasTable('design_profiles') ? app(\App\Services\DesignStudioService::class)->bootstrap('web',$locale) : ['design'=>null,'navigation'=>[]])
@php($remoteDesign=$designBoot['design']??null)
@php($dt=$remoteDesign['tokens']??[])
<html lang="{{$locale}}" dir="{{$rtl?'rtl':'ltr'}}" data-theme="{{$theme}}" data-density="{{$dt['density']??'comfortable'}}" style="--font-scale:{{$fontScale}};--remote-accent:{{$dt['accent']??'#0B8CFF'}};--remote-accent2:{{$dt['accent2']??'#16D7FF'}};--remote-radius:{{(int)($dt['radius']??24)}}px">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
  <meta name="csrf-token" content="{{csrf_token()}}">
  <meta name="color-scheme" content="dark light">
  <meta name="description" content="@yield('description','ScoreTime — live football scores, trusted news, match intelligence, transfers and fan experiences.')">
  <meta name="theme-color" content="#06122A">
  <link rel="manifest" href="/manifest.webmanifest">
  <link rel="canonical" href="{{url()->current()}}">
  <link rel="icon" type="image/png" sizes="32x32" href="/assets/brand/scoretime-32.png">
  <link rel="apple-touch-icon" href="/assets/brand/scoretime-192.png">
  <title>@yield('title','ScoreTime')</title>
  <link rel="stylesheet" href="/css/app.css">
</head>
<body>
<div class="ambient ambient-a"></div><div class="ambient ambient-b"></div>
<header class="topbar">
  <div class="shell navrow">
    <a class="brand" href="/" aria-label="ScoreTime home">
      <img src="/assets/brand/scoretime-128.png" alt="ScoreTime" class="brand-icon">
      <span class="brand-copy"><strong>Score<span>Time</span></strong><small>{{__('ui.every_moment_counts')}}</small></span>
    </a>
    <nav class="desktop-nav">
      @forelse(collect($designBoot['navigation']??[])->where('location','primary')->take(7) as $navItem)
        <a href="{{$navItem['target']}}">{{$navItem['label']}}</a>
      @empty
        <a href="/">{{__('ui.home')}}</a><a href="/matches">{{__('ui.live')}}</a><a href="/news">{{__('ui.news')}}</a><a href="/transfers">{{__('ui.transfers')}}</a><a href="/world">{{__('ui.world')}}</a><a href="/leaderboard">{{__('ui.predictions')}}</a>
      @endforelse
    </nav>
    <form class="nav-search" action="/explore" method="get"><span>⌕</span><input name="q" value="{{request('q')}}" placeholder="{{__('ui.search')}}"></form>
    <div class="nav-actions">
      <div class="lang-menu"><button class="icon-btn" data-popover="languages" aria-label="{{__('ui.language')}}">{{$locale}}</button>
        <div id="languages" class="popover">@foreach(['en'=>'English','ar'=>'العربية','fr'=>'Français','es'=>'Español','de'=>'Deutsch','tr'=>'Türkçe'] as $code=>$label)<a href="{{route('locale',$code)}}">{{$label}}</a>@endforeach</div>
      </div>
      <button class="icon-btn" data-popover="appearance" aria-label="{{__('ui.appearance')}}">Aa</button>
      <div id="appearance" class="popover appearance-pop"><form method="post" action="{{route('appearance')}}">@csrf<label>{{__('ui.theme')}}<select name="theme"><option value="stadium" @selected($theme==='stadium')>ScoreTime</option><option value="midnight" @selected($theme==='midnight')>Midnight</option><option value="light" @selected($theme==='light')>Light</option></select></label><label>{{__('ui.font_size')}}<input type="range" name="font_scale" min=".85" max="1.30" step=".05" value="{{$fontScale}}"></label><button class="btn primary">{{__('ui.save')}}</button></form></div>
      @auth
        @if(auth()->user()->is_admin)<a class="btn admin" href="{{route('admin.dashboard')}}">{{__('ui.admin')}}</a>@endif
        <form method="post" action="{{route('logout')}}">@csrf<button class="icon-btn" title="{{__('ui.logout')}}">↗</button></form>
      @else
        <a class="btn ghost compact" href="{{route('login')}}">{{__('ui.login')}}</a><a class="btn primary compact" href="{{route('register')}}">{{__('ui.sign_up')}}</a>
      @endauth
    </div>
  </div>
</header>
@if(session('ok'))<div class="toast">{{session('ok')}}</div>@endif
<main class="shell page">@yield('content')</main>
<footer class="footer"><div class="shell footer-grid"><div class="footer-brand"><img src="/assets/brand/scoretime-128.png" alt=""><div><strong>ScoreTime</strong><p>{{__('ui.hero_subtitle')}}</p></div></div><div><b>{{__('ui.explore')}}</b><a href="/matches">{{__('ui.matches')}}</a><a href="/news">{{__('ui.news')}}</a><a href="/transfers">{{__('ui.transfers')}}</a><a href="/world">{{__('ui.world')}}</a></div><div><b>{{__('ui.appearance')}}</b><a href="/sitemap.xml">Sitemap</a>@guest<a href="{{route('login')}}">{{__('ui.login')}}</a><a href="{{route('register')}}">{{__('ui.register')}}</a>@endguest</div></div><div class="shell footer-bottom"><span>© {{date('Y')}} ScoreTime</span><span>Every moment counts.</span></div></footer>
<script src="/js/app.js" defer></script>
<script>if('serviceWorker' in navigator){window.addEventListener('load',()=>navigator.serviceWorker.register('/sw.js').catch(()=>{}));}</script>
</body></html>
