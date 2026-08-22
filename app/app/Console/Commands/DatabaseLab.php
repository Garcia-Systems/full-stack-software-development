<?php

namespace App\Console\Commands;

use App\Exceptions\StaleTicket;
use App\Models\Customer;
use App\Models\Project;
use App\Models\Ticket;
use App\Services\ProvisionProject;
use App\Services\UpdateTicketOptimistically;
use Database\Seeders\PerformanceDatasetSeeder;
use Illuminate\Console\Command;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\DB;
use RuntimeException;

final class DatabaseLab extends Command
{
    protected $signature = 'lab:database
        {experiment : sql, relationships, seed-performance, index, plan, transaction, concurrency, integrity, or debug}
        {--without-index : Temporarily remove the workload index for index/plan evidence}
        {--unsafe : Run the deliberately non-transactional transaction example}
        {--fail : Inject the documented failure after the first write}';

    protected $description = 'Run controlled Part III database evidence experiments';

    public function handle(ProvisionProject $provision, UpdateTicketOptimistically $optimistic): int
    {
        return match ($this->argument('experiment')) {
            'sql' => $this->sql(),
            'relationships' => $this->relationships(),
            'seed-performance' => $this->seedPerformance(),
            'index' => $this->indexEvidence(),
            'plan' => $this->plan(),
            'transaction' => $this->transaction($provision),
            'concurrency' => $this->concurrency($optimistic),
            'integrity' => $this->integrity(),
            'debug' => $this->debug(),
            default => $this->invalidExperiment(),
        };
    }

    private function sql(): int
    {
        $events = [];
        DB::listen(function (QueryExecuted $query) use (&$events): void {
            $events[] = ['sql' => $query->sql, 'bindings' => $query->bindings, 'time_ms' => $query->time];
        });

        try {
            DB::transaction(function (): never {
                $ticket = Ticket::query()->where('status', 'open')->with('customer')->firstOrFail();
                $created = Ticket::create([
                    'organization_id' => $ticket->organization_id,
                    'customer_id' => $ticket->customer_id,
                    'subject' => 'Chapter 18 temporary row',
                ]);
                $created->update(['priority' => 'high']);
                $created->delete();
                throw new RuntimeException('inspection complete');
            });
        } catch (RuntimeException $exception) {
            if ($exception->getMessage() !== 'inspection complete') throw $exception;
        }

        foreach ($events as $number => $event) {
            $this->line(json_encode(['number' => $number + 1] + $event, JSON_UNESCAPED_SLASHES));
        }
        $this->info('The transaction rolled back: inspection did not retain the temporary row.');

        return self::SUCCESS;
    }

    private function relationships(): int
    {
        $phase = 'lazy';
        $counts = ['lazy' => 0, 'eager' => 0];
        DB::listen(function () use (&$counts, &$phase): void { $counts[$phase]++; });
        $customers = Customer::query()->where('organization_id', 1)->orderBy('id')->get();
        foreach ($customers as $customer) {
            $customer->tickets->count(); // Deliberate N+1 observation.
        }
        $phase = 'eager';
        Customer::query()->where('organization_id', 1)->with('tickets')->orderBy('id')->get();
        $observedCounts = $counts;

        $joined = DB::table('customers')
            ->leftJoin('tickets', function ($join): void {
                $join->on('tickets.customer_id', '=', 'customers.id')
                    ->on('tickets.organization_id', '=', 'customers.organization_id');
            })
            ->where('customers.organization_id', 1)
            ->selectRaw('customers.id, customers.name, COUNT(tickets.id) AS ticket_count')
            ->groupBy('customers.id', 'customers.name')
            ->orderBy('customers.id')
            ->get();

        $this->table(['loading', 'queries'], [['lazy', $observedCounts['lazy']], ['eager', $observedCounts['eager']]]);
        $this->line($joined->toJson());

        return self::SUCCESS;
    }

    private function seedPerformance(): int
    {
        $this->call('db:seed', ['--class' => PerformanceDatasetSeeder::class, '--force' => true]);
        $this->info('Seeded exactly '.PerformanceDatasetSeeder::ROWS.' repeatable workload tickets.');
        return self::SUCCESS;
    }

    private function indexEvidence(): int
    {
        $this->requireMysql();
        $this->setWorkloadIndex(!$this->option('without-index'));
        $start = hrtime(true);
        $count = DB::table('tickets')->where('organization_id', $this->performanceOrganizationId())
            ->where('priority', 'urgent')->where('created_at', '>=', '2026-01-01')->count();
        $milliseconds = (hrtime(true) - $start) / 1_000_000;
        $this->line(json_encode(['rows_returned' => $count, 'wall_ms' => round($milliseconds, 3), 'index_present' => !$this->option('without-index')]));
        $this->info('Compare repeated before/after runs and plans; absolute timing depends on the machine and cache.');
        return self::SUCCESS;
    }

    private function plan(): int
    {
        $this->requireMysql();
        $this->setWorkloadIndex(!$this->option('without-index'));
        $sql = "SELECT id, subject FROM tickets WHERE organization_id = ? AND priority = ? AND created_at >= ?";
        $plan = DB::select('EXPLAIN ANALYZE '.$sql, [$this->performanceOrganizationId(), 'urgent', '2026-01-01']);
        foreach ($plan as $row) {
            $this->line((string) array_values((array) $row)[0]);
        }
        return self::SUCCESS;
    }

    private function transaction(ProvisionProject $provision): int
    {
        $customer = Customer::where('is_active', true)->firstOrFail();
        $name = 'Transaction lab '.now()->format('Hisv');
        try {
            $this->option('unsafe')
                ? $provision->unsafeForLab($customer, $name, (bool) $this->option('fail'))
                : $provision->handle($customer, $name, (bool) $this->option('fail'));
        } catch (RuntimeException $exception) {
            $this->warn($exception->getMessage());
        }
        $project = Project::where('name', $name)->first();
        $this->line(json_encode(['project_exists' => $project !== null, 'initial_ticket_exists' => $project?->tickets()->exists() ?? false]));
        return self::SUCCESS;
    }

    private function concurrency(UpdateTicketOptimistically $optimistic): int
    {
        $ticket = Ticket::firstOrFail();
        $original = ['subject' => $ticket->subject, 'version' => $ticket->version];
        $snapshotA = Ticket::findOrFail($ticket->id)->replicate();
        $snapshotB = Ticket::findOrFail($ticket->id)->replicate();
        $snapshotA->setAttribute('id', $ticket->id)->exists = true;
        $snapshotB->setAttribute('id', $ticket->id)->exists = true;
        $snapshotA->subject = 'Writer A result';
        $snapshotA->save();
        $snapshotB->subject = 'Writer B stale overwrite';
        $snapshotB->save();
        $lost = Ticket::findOrFail($ticket->id);

        $ticket->update(['subject' => $original['subject'], 'version' => $original['version']]);
        $readVersion = $ticket->fresh()->version;
        $optimistic->subject($ticket->id, $readVersion, 'Writer A protected');
        $rejected = false;
        try {
            $optimistic->subject($ticket->id, $readVersion, 'Writer B protected');
        } catch (StaleTicket) {
            $rejected = true;
        }

        $this->line(json_encode([
            'unprotected_final' => $lost->subject,
            'protected_final' => $ticket->fresh()->subject,
            'stale_writer_rejected' => $rejected,
        ]));
        return self::SUCCESS;
    }

    private function integrity(): int
    {
        $this->requireMysql();
        $checks = DB::select("SELECT CONSTRAINT_NAME, CONSTRAINT_TYPE FROM information_schema.TABLE_CONSTRAINTS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME IN ('customers','projects','tickets') ORDER BY TABLE_NAME, CONSTRAINT_NAME");
        foreach ($checks as $check) {
            $values = array_values((array) $check);
            $this->line($values[0].' | '.$values[1]);
        }
        return self::SUCCESS;
    }

    private function debug(): int
    {
        $this->warn('Unknown incident: gather evidence; do not map symptoms to causes by guessing.');
        $this->call('lab:database', ['experiment' => 'relationships']);
        $this->line('Next inspect SHOW INDEX, EXPLAIN ANALYZE, transaction failure state, version interleaving, and constraints using Chapters 20–24.');
        return self::SUCCESS;
    }

    private function setWorkloadIndex(bool $present): void
    {
        $exists = count(DB::select("SHOW INDEX FROM tickets WHERE Key_name = 'tickets_workload_lookup'")) > 0;
        if ($present && !$exists) DB::statement('CREATE INDEX tickets_workload_lookup ON tickets (organization_id, priority, created_at)');
        if (!$present && $exists) DB::statement('DROP INDEX tickets_workload_lookup ON tickets');
    }

    private function performanceOrganizationId(): int
    {
        return (int) DB::table('organizations')->where('name', 'Performance Lab')->value('id');
    }

    private function requireMysql(): void
    {
        if (DB::getDriverName() !== 'mysql') {
            throw new RuntimeException('This evidence lab requires the repository MySQL 8.4 service.');
        }
    }

    private function invalidExperiment(): int
    {
        $this->error('Unknown experiment. See php artisan help lab:database.');
        return self::INVALID;
    }
}
