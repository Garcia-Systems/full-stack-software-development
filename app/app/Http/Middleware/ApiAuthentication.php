<?php
namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
final class ApiAuthentication { public function handle(Request $r,Closure $next):Response{if(!$r->user())return response()->json(['error'=>['type'=>'unauthenticated','message'=>'Authentication is required.'],'request_id'=>$r->attributes->get('request_id')],401);return $next($r);} }
