<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up():void{Schema::create('customers',function(Blueprint $t){$t->id();$t->foreignId('organization_id')->constrained()->restrictOnDelete();$t->string('name',120);$t->string('email',254);$t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['organization_id','email']);});} public function down():void{Schema::dropIfExists('customers');} };
