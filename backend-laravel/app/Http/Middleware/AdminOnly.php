<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class AdminOnly {
 public function handle(Request $request,Closure $next):Response {
  $user=$request->user(); abort_unless($user?->is_admin,403);
  if(filter_var(env('ADMIN_SUPERUSER_ONLY',true),FILTER_VALIDATE_BOOL)) abort_unless(hash_equals((string)env('ADMIN_PRIMARY_USERNAME','Adnan'),(string)$user->username),403);
  return $next($request);
 }
}
