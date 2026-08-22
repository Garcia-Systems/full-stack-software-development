<?php
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
Route::get('/tickets', [TicketController::class, 'index']);
Route::post('/tickets', [TicketController::class, 'store']);
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->whereNumber('ticket');
Route::patch('/tickets/{ticket}', [TicketController::class, 'update'])->whereNumber('ticket');
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->whereNumber('ticket');
Route::get('/customers', [CustomerController::class, 'index']);
Route::post('/customers/{customer}/projects', [ProjectController::class, 'store'])->whereNumber('customer');
Route::patch('/tickets/{ticket}/status', [TicketController::class, 'transition'])->whereNumber('ticket');
Route::get('/lab/fail', [TicketController::class, 'controlledFailure']);

// Part V's versioned external contract; earlier unversioned routes remain as chapter checkpoints.
Route::options('/v1/{path}', fn()=>response('',204))->where('path','.*');
Route::post('/v1/session', [\App\Http\Controllers\AuthController::class,'login']);
Route::middleware('api.auth')->prefix('v1')->group(function():void {
 Route::get('/session',[\App\Http\Controllers\AuthController::class,'me']); Route::delete('/session',[\App\Http\Controllers\AuthController::class,'logout']);
 Route::get('/tickets',[\App\Http\Controllers\ApiTicketController::class,'index']); Route::post('/tickets',[\App\Http\Controllers\ApiTicketController::class,'store']); Route::get('/tickets/{ticket}',[\App\Http\Controllers\ApiTicketController::class,'show'])->whereNumber('ticket'); Route::delete('/tickets/{ticket}',[\App\Http\Controllers\ApiTicketController::class,'destroy'])->whereNumber('ticket');
 Route::get('/dashboard',\App\Http\Controllers\DashboardController::class);
 Route::get('/deliveries/{jobId}',function(\Illuminate\Http\Request $r,string $jobId){$delivery=\App\Models\IntegrationDelivery::where('job_id',$jobId)->firstOrFail();abort_unless(\App\Models\Membership::where(['user_id'=>$r->user()->id,'organization_id'=>$delivery->organization_id,'status'=>'active'])->exists(),403);return response()->json(['data'=>$delivery->only(['job_id','status','attempts','provider_id','error_category']),'requestId'=>$r->attributes->get('request_id')]);});
 Route::get('/customers',function(\Illuminate\Http\Request $r){$allowed=\App\Models\Membership::where(['user_id'=>$r->user()->id,'organization_id'=>$r->integer('organization_id'),'status'=>'active'])->exists();abort_unless($allowed,403);$items=\App\Models\Customer::where('organization_id',$r->integer('organization_id'))->orderBy('id')->get();return response()->json(['data'=>\App\Http\Resources\CustomerResource::collection($items)->resolve(),'requestId'=>$r->attributes->get('request_id')]);});
});
