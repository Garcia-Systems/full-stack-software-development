<?php

namespace App\Services;

use App\Exceptions\StaleTicket;
use App\Models\Ticket;

final class UpdateTicketOptimistically
{
    public function subject(int $ticketId, int $expectedVersion, string $subject): Ticket
    {
        $changed = Ticket::query()
            ->whereKey($ticketId)
            ->where('version', $expectedVersion)
            ->update([
                'subject' => $subject,
                'version' => $expectedVersion + 1,
                'updated_at' => now(),
            ]);

        if ($changed !== 1) {
            throw new StaleTicket;
        }

        return Ticket::findOrFail($ticketId);
    }
}
