<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Organization;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class PerformanceDatasetSeeder extends Seeder
{
    public const ROWS = 20000;

    public function run(): void
    {
        $organization = Organization::firstOrCreate(
            ['name' => 'Performance Lab'],
            ['project_limit' => 3],
        );
        $customer = Customer::firstOrCreate(
            ['organization_id' => $organization->id, 'email' => 'benchmark@relaydesk.test'],
            ['name' => 'Benchmark Customer', 'is_active' => true],
        );

        DB::table('tickets')->where('organization_id', $organization->id)->delete();
        $created = '2026-01-01 12:00:00';

        for ($start = 1; $start <= self::ROWS; $start += 500) {
            $rows = [];
            for ($number = $start; $number < min($start + 500, self::ROWS + 1); $number++) {
                $rows[] = [
                    'organization_id' => $organization->id,
                    'customer_id' => $customer->id,
                    'project_id' => null,
                    'subject' => sprintf('Deterministic workload ticket %05d', $number),
                    'description' => null,
                    'status' => $number % 5 === 0 ? 'closed' : 'open',
                    'priority' => $number % 100 === 0 ? 'urgent' : ($number % 4 === 0 ? 'high' : 'normal'),
                    'version' => 1,
                    'created_at' => $created,
                    'updated_at' => $created,
                ];
            }
            DB::table('tickets')->insert($rows);
        }
    }
}
