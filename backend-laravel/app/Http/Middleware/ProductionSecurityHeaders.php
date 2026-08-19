<?php
namespace App\Http\Middleware;
use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
class ProductionSecurityHeaders {
 public function handle(Request $request,Closure $next):Response{$response=$next($request);$response->headers->set('X-Content-Type-Options','nosniff');$response->headers->set('X-Frame-Options','SAMEORIGIN');$response->headers->set('Referrer-Policy','strict-origin-when-cross-origin');$response->headers->set('Permissions-Policy','camera=(), microphone=(), geolocation=(self)');$response->headers->set('Cross-Origin-Opener-Policy','same-origin');$response->headers->set('Cross-Origin-Resource-Policy','same-site');if(app()->environment('production')){$response->headers->set('Strict-Transport-Security','max-age=31536000; includeSubDomains; preload');$response->headers->set('Content-Security-Policy',"default-src 'self'; img-src 'self' https: data:; style-src 'self' 'unsafe-inline'; script-src 'self'; connect-src 'self' https: wss:; frame-ancestors 'self'; base-uri 'self'; form-action 'self'");}return $response;}
}
