<?php

namespace Tests\Feature;

use App\Exceptions\StaleTicket;
use App\Models\Customer;
use App\Models\Organization;
use App\Models\Ticket;
use App\Services\UpdateTicketOptimistically;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class OptimisticConcurrencyTest extends TestCase
{
    use RefreshDatabase;

    public function test_stale_snapshot_cannot_overwrite_first_writer(): void
    {
        $organization = Organization::create(['name' => 'Concurrency']);
        $customer = Customer::create(['organization_id' => $organization->id, 'name' => 'Customer', 'email' => 'race@test']);
        $ticket = Ticket::create(['organization_id' => $organization->id, 'customer_id' => $customer->id, 'subject' => 'Original']);
        $versionSeenByAAndB = $ticket->refresh()->version;
        $service = app(UpdateTicketOptimistically::class);

        $service->subject($ticket->id, $versionSeenByAAndB, 'Writer A');

        $this->expectException(StaleTicket::class);
        try {
            $service->subject($ticket->id, $versionSeenByAAndB, 'Writer B');
        } finally {
            self::assertSame('Writer A', $ticket->fresh()->subject);
            self::assertSame(2, $ticket->fresh()->version);
        }
    }
}
