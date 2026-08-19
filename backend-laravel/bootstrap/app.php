<?php
use App\Http\Middleware\AdminOnly;
use App\Http\Middleware\SetLocale;
use App\Http\Middleware\RequireAdminTwoFactor;
use App\Http\Middleware\ProductionSecurityHeaders;
use App\Http\Middleware\AdminIpAllowlist;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
return Application::configure(basePath:dirname(__DIR__))
 ->withRouting(web:__DIR__.'/../routes/web.php',api:__DIR__.'/../routes/api.php',commands:__DIR__.'/../routes/console.php',health:'/up')
 ->withMiddleware(function(Middleware $m){$m->web(append:[SetLocale::class,ProductionSecurityHeaders::class]);$m->api(append:[ProductionSecurityHeaders::class]);$m->alias(['admin'=>AdminOnly::class,'admin.2fa'=>RequireAdminTwoFactor::class,'admin.ip'=>AdminIpAllowlist::class]);})
 ->withExceptions(function(Exceptions $e){})
 ->create();
