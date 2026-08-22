<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Organization;
use App\Services\ProvisionProject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
use Tests\TestCase;

final class ProjectTransactionTest extends TestCase
{
    use RefreshDatabase;

    public function test_failure_rolls_back_project_and_initial_ticket(): void
    {
        $organization = Organization::create(['name' => 'Atomic']);
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Customer', 'email' => 'atomic@test']);

        try {
            app(ProvisionProject::class)->handle($customer, 'Rollback me', true);
            self::fail('The controlled failure did not run.');
        } catch (RuntimeException $exception) {
            self::assertSame('Controlled lab failure after project creation.', $exception->getMessage());
        }

        $this->assertDatabaseMissing('projects', ['name' => 'Rollback me']);
        $this->assertDatabaseMissing('tickets', ['subject' => 'Kick off Rollback me']);
    }

    public function test_success_commits_both_business_records(): void
    {
        $organization = Organization::create(['name' => 'Atomic']);
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Customer', 'email' => 'commit@test']);
        $project = app(ProvisionProject::class)->handle($customer, 'Commit me');

        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('tickets', ['project_id' => $project->id, 'subject' => 'Kick off Commit me']);
    }
}
