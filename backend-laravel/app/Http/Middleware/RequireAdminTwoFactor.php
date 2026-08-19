<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class RequireAdminTwoFactor {
 public function handle(Request $request,Closure $next):Response {
  $u=$request->user(); if(!$u?->is_admin)return $next($request);
  $required=filter_var(env('ADMIN_REQUIRE_MFA',app()->environment('production')),FILTER_VALIDATE_BOOL);
  if($required&&!$u->two_factor_confirmed_at&&!$request->routeIs('admin.2fa.setup','admin.2fa.confirm'))return redirect()->route('admin.2fa.setup')->with('warning','Two-factor authentication is required for administration.');
  if($u->two_factor_confirmed_at&&!$request->session()->get('admin_2fa_passed')&&!$request->routeIs('admin.2fa.challenge','admin.2fa.verify'))return redirect()->route('admin.2fa.challenge');
  return $next($request);
 }
}
