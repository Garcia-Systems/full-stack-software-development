<?php
namespace Tests\Feature;
use App\Models\Customer; use App\Models\Organization; use App\Models\Ticket; use Illuminate\Foundation\Testing\RefreshDatabase; use Tests\TestCase;
final class BusinessErrorsTest extends TestCase { use RefreshDatabase;
 public function test_inactive_customer_and_invalid_transition_are_conflicts():void{$org=Organization::create(['name'=>'Test']);$customer=Customer::create(['organization_id'=>$org->id,'name'=>'Dormant','email'=>'d@test','is_active'=>false]);$this->postJson("/api/customers/{$customer->id}/projects",['name'=>'No'])->assertConflict()->assertJsonPath('type','business_rule');$ticket=Ticket::create(['organization_id'=>$org->id,'customer_id'=>$customer->id,'subject'=>'Closed','status'=>'closed']);$this->patchJson("/api/tickets/{$ticket->id}/status",['status'=>'open'])->assertConflict();}
 public function test_not_found_validation_and_unexpected_are_not_the_same():void{$this->getJson('/api/tickets/999')->assertNotFound();$this->getJson('/api/lab/fail')->assertStatus(500)->assertJsonPath('type','unexpected_error');}
}
