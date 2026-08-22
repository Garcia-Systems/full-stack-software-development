<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
final class RequestId {
 public function handle(Request $request, Closure $next): Response {
  $candidate=$request->header('X-Request-ID'); $id=is_string($candidate)&&preg_match('/^[A-Za-z0-9._-]{1,64}$/',$candidate)?$candidate:bin2hex(random_bytes(8));
  $started=microtime(true);$request->attributes->set('request_id',$id); Log::withContext(['request_id'=>$id]);
  $response=$next($request); $response->headers->set('X-Request-ID',$id);
  Log::info('request.complete',['operation'=>'http.request','method'=>$request->method(),'route'=>$request->path(),'status'=>$response->getStatusCode(),'user_id'=>$request->user()?->id,'duration_ms'=>round((microtime(true)-$started)*1000,2)]); return $response;
 }
}
