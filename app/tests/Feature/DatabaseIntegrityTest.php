<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Organization;
use App\Models\Project;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_identity_is_unique_inside_organization_customer_scope(): void
    {
        $organization = Organization::create(['name' => 'Integrity']);
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Customer', 'email' => 'unique@test']);
        Project::create(['organization_id' => $organization->id, 'customer_id' => $customer->id, 'name' => 'Same']);

        $this->expectException(QueryException::class);
        Project::create(['organization_id' => $organization->id, 'customer_id' => $customer->id, 'name' => 'Same']);
    }

    public function test_mysql_rejects_cross_tenant_customer_even_outside_validation(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            $this->markTestSkipped('Composite foreign-key behavior is verified against the reference MySQL service.');
        }
        $one = Organization::create(['name' => 'One']);
        $two = Organization::create(['name' => 'Two']);
        $customer = Customer::create(['organization_id' => $two->id, 'name' => 'Other', 'email' => 'other@integrity.test']);

        $this->expectException(QueryException::class);
        DB::table('tickets')->insert([
            'organization_id' => $one->id,
            'customer_id' => $customer->id,
            'subject' => 'Impossible relationship',
            'status' => 'open',
            'priority' => 'normal',
            'version' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
