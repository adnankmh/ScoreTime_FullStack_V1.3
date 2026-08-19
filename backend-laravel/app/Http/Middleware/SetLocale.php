<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
class SetLocale {
 public function handle(Request $request, Closure $next): Response {
  $supported=['en','ar','fr','es','de','tr'];
  $locale=$request->session()->get('locale', config('app.locale','en'));
  if(!in_array($locale,$supported,true)) $locale='en';
  app()->setLocale($locale);
  return $next($request);
 }
}
