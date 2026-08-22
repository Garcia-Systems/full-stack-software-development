<?php
namespace App\Http\Middleware; use Closure; use Illuminate\Http\Request; use Symfony\Component\HttpFoundation\Response;
final class LabControls { public function handle(Request $r,Closure $next):Response{if(!app()->environment('local','testing')||!env('LAB_FAULTS',false))return $next($r);$mode=$r->header('X-RelayDesk-Lab');if($mode==='delay')usleep(400000);if($mode==='server-error')throw new \RuntimeException('Controlled Part V failure');if($mode==='empty')$r->attributes->set('lab_empty',true);return $next($r);} }
