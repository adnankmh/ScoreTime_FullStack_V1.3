<?php
namespace App\Providers;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider{
 public function register():void{}
 public function boot():void{
  RateLimiter::for('login',fn(Request $r)=>Limit::perMinute(5)->by(strtolower((string)$r->input('login')).'|'.$r->ip()));
  RateLimiter::for('api',fn(Request $r)=>$r->user()?Limit::perMinute(120)->by((string)$r->user()->id):Limit::perMinute(60)->by($r->ip()));
 }
}
