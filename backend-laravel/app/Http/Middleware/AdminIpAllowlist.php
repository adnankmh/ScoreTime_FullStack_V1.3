<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class AdminIpAllowlist {public function handle(Request $request,Closure $next):Response{$raw=trim((string)env('ADMIN_ALLOWED_IPS',''));if($raw==='')return $next($request);$allowed=array_filter(array_map('trim',explode(',',$raw)));abort_unless(in_array($request->ip(),$allowed,true),403,'Admin network not allowed.');return $next($request);}}
