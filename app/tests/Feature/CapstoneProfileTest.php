<?php

namespace Tests\Feature;

use App\Jobs\DeliverTicketNotification;
use App\Models\Customer;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

final class CapstoneProfileTest extends TestCase
{
    use RefreshDatabase;

    protected function tearDown(): void
    {
        putenv('LAB_PROFILE');
        parent::tearDown();
    }

    public function test_debug_capstone_profile_reproduces_the_documented_symptoms(): void
    {
        putenv('LAB_PROFILE=debug-capstone');
        Queue::fake();
        [$user, $organization] = $this->member();
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Profile Co', 'email' => 'profile@test']);
        $body = ['organization_id' => $organization->id, 'customerId' => $customer->id, 'subject' => 'Profile evidence', 'priority' => 'high'];

        $this->actingAs($user)->getJson("/api/v1/dashboard?organization_id={$organization->id}")->assertJsonPath('data.active', 0);
        $this->actingAs($user)->withHeader('Idempotency-Key', 'profile-evidence-key')->postJson('/api/v1/tickets', $body)->assertCreated();
        $this->actingAs($user)->withHeader('Idempotency-Key', 'profile-evidence-key')->postJson('/api/v1/tickets', $body)->assertCreated();

        $this->assertDatabaseCount('tickets', 2);
        $this->assertDatabaseCount('integration_deliveries', 2);
        Queue::assertNothingPushed();
        $this->actingAs($user)->getJson("/api/v1/dashboard?organization_id={$organization->id}")
            ->assertJsonPath('data.cache', 'hit')->assertJsonPath('data.active', 0);
    }

    public function test_normal_profile_preserves_duplicate_and_background_work_regressions(): void
    {
        Queue::fake();
        [$user, $organization] = $this->member();
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Evidence Co', 'email' => 'evidence@test']);
        $body = ['organization_id' => $organization->id, 'customerId' => $customer->id, 'subject' => 'Regression evidence', 'priority' => 'high'];

        $first = $this->actingAs($user)->withHeader('Idempotency-Key', 'capstone-regression-key')->postJson('/api/v1/tickets', $body)->assertCreated();
        $this->actingAs($user)->withHeader('Idempotency-Key', 'capstone-regression-key')->postJson('/api/v1/tickets', $body)
            ->assertCreated()->assertHeader('Idempotency-Replayed', 'true')->assertJsonPath('data.id', $first->json('data.id'));

        $this->assertDatabaseCount('tickets', 1);
        Queue::assertPushed(DeliverTicketNotification::class, 1);
    }
}
