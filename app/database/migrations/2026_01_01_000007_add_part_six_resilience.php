<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void {
 Schema::create('cache',fn(Blueprint $t)=>[$t->string('key')->primary(),$t->mediumText('value'),$t->integer('expiration')->index()]);
 Schema::create('cache_locks',fn(Blueprint $t)=>[$t->string('key')->primary(),$t->string('owner'),$t->integer('expiration')->index()]);
 Schema::create('jobs',fn(Blueprint $t)=>[$t->id(),$t->string('queue')->index(),$t->longText('payload'),$t->unsignedTinyInteger('attempts'),$t->unsignedInteger('reserved_at')->nullable(),$t->unsignedInteger('available_at'),$t->unsignedInteger('created_at')]);
 Schema::create('failed_jobs',fn(Blueprint $t)=>[$t->id(),$t->string('uuid')->unique(),$t->text('connection'),$t->text('queue'),$t->longText('payload'),$t->longText('exception'),$t->timestamp('failed_at')->useCurrent()]);
 Schema::create('idempotency_requests',function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->string('key',100);$t->string('request_hash',64);$t->unsignedBigInteger('ticket_id')->nullable();$t->json('response')->nullable();$t->unsignedSmallInteger('status')->default(201);$t->timestamps();$t->unique(['organization_id','key']);});
 Schema::create('integration_deliveries',function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->cascadeOnDelete();$t->foreignId('ticket_id')->constrained()->cascadeOnDelete();$t->uuid('job_id')->unique();$t->string('correlation_id',64);$t->string('status',20)->default('pending');$t->unsignedTinyInteger('attempts')->default(0);$t->string('provider_id')->nullable();$t->string('error_category')->nullable();$t->timestamps();});
 } public function down():void{foreach(['integration_deliveries','idempotency_requests','failed_jobs','jobs','cache_locks','cache'] as $table)Schema::dropIfExists($table);} };
