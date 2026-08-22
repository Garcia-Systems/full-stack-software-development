<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Project;
use App\Models\Ticket;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class ProvisionProject
{
    /** Create the project and its first work item as one business outcome. */
    public function handle(Customer $customer, string $name, bool $failAfterProject = false): Project
    {
        return DB::transaction(function () use ($customer, $name, $failAfterProject): Project {
            $project = Project::create([
                'organization_id' => $customer->organization_id,
                'customer_id' => $customer->id,
                'name' => $name,
            ]);

            if ($failAfterProject) {
                throw new RuntimeException('Controlled lab failure after project creation.');
            }

            Ticket::create([
                'organization_id' => $customer->organization_id,
                'customer_id' => $customer->id,
                'project_id' => $project->id,
                'subject' => "Kick off {$name}",
            ]);

            return $project->load('tickets');
        });
    }

    /** Deliberately unsafe implementation; available only to the explicit CLI lab. */
    public function unsafeForLab(Customer $customer, string $name, bool $failAfterProject): Project
    {
        $project = Project::create([
            'organization_id' => $customer->organization_id,
            'customer_id' => $customer->id,
            'name' => $name,
        ]);

        if ($failAfterProject) {
            throw new RuntimeException('Controlled lab failure after project creation.');
        }

        Ticket::create([
            'organization_id' => $customer->organization_id,
            'customer_id' => $customer->id,
            'project_id' => $project->id,
            'subject' => "Kick off {$name}",
        ]);

        return $project;
    }
}
