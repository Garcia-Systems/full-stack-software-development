<?php
namespace App\Services; use App\Models\Ticket; use Illuminate\Support\Facades\Cache; use Illuminate\Support\Facades\Log;
final class DashboardSummary {
 public static function key(int $organizationId):string{return "organization:$organizationId:dashboard:v1";}
 public function get(int $organizationId):array{$key=self::key($organizationId);$started=microtime(true);$hit=Cache::has($key);$value=Cache::remember($key,now()->addSeconds(60),fn()=>['active'=>Ticket::where('organization_id',$organizationId)->where('status','!=','closed')->count(),'highAttention'=>Ticket::where('organization_id',$organizationId)->whereIn('priority',['high','urgent'])->count()]);Log::info('cache.lookup',['operation'=>'dashboard.summary','organization_id'=>$organizationId,'cache_key'=>$key,'cache_result'=>$hit?'hit':'miss','duration_ms'=>round((microtime(true)-$started)*1000,2)]);return $value+['cache'=>$hit?'hit':'miss'];}
 public function forget(int $organizationId):void{Cache::forget(self::key($organizationId));Log::info('cache.invalidated',['organization_id'=>$organizationId,'cache_key'=>self::key($organizationId)]);}
}
