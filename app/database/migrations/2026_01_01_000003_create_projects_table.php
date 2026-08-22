<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('projects',function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->restrictOnDelete();$t->foreignId('customer_id')->constrained()->restrictOnDelete();$t->string('name',120);$t->string('status',20)->default('active');$t->timestamps();$t->index(['organization_id','customer_id']);});} public function down():void{Schema::dropIfExists('projects');} };
