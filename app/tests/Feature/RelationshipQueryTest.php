<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Organization;
use App\Models\Ticket;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

final class RelationshipQueryTest extends TestCase
{
    use RefreshDatabase;

    public function test_eager_loading_has_a_fixed_query_count(): void
    {
        $organization = Organization::create(['name' => 'Query Count']);
        foreach (['One', 'Two', 'Three'] as $number => $name) {
            $customer = Customer::create([
                'organization_id' => $organization->id,
                'name' => $name,
                'email' => strtolower($name).'@test',
            ]);
            Ticket::create([
                'organization_id' => $organization->id,
                'customer_id' => $customer->id,
                'subject' => "Ticket {$number}",
            ]);
        }

        $queries = 0;
        DB::listen(function (QueryExecuted $event) use (&$queries): void { $queries++; });
        $customers = Customer::where('organization_id', $organization->id)->with('tickets')->get();
        foreach ($customers as $customer) {
            self::assertCount(1, $customer->tickets);
        }

        self::assertSame(2, $queries, 'One customer query plus one eager relationship query is the regression contract.');
    }
}
