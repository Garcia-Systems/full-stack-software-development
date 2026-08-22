<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table): void {
            $table->unique(['organization_id', 'id'], 'customers_organization_id_id_unique');
        });
        Schema::table('projects', function (Blueprint $table): void {
            $table->unique(['organization_id', 'id'], 'projects_organization_id_id_unique');
            $table->unique(['organization_id', 'customer_id', 'name'], 'projects_scope_name_unique');
        });
        Schema::table('tickets', function (Blueprint $table): void {
            $table->index(['organization_id', 'priority', 'created_at'], 'tickets_workload_lookup');
        });

        if (DB::getDriverName() !== 'mysql') {
            return;
        }

        DB::statement('ALTER TABLE projects DROP FOREIGN KEY projects_customer_id_foreign');
        DB::statement('ALTER TABLE projects ADD CONSTRAINT projects_customer_tenant_foreign FOREIGN KEY (organization_id, customer_id) REFERENCES customers (organization_id, id) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE tickets DROP FOREIGN KEY tickets_customer_id_foreign');
        DB::statement('ALTER TABLE tickets DROP FOREIGN KEY tickets_project_id_foreign');
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_customer_tenant_foreign FOREIGN KEY (organization_id, customer_id) REFERENCES customers (organization_id, id) ON DELETE RESTRICT');
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_project_tenant_foreign FOREIGN KEY (organization_id, project_id) REFERENCES projects (organization_id, id) ON DELETE RESTRICT');
        DB::statement("ALTER TABLE projects ADD CONSTRAINT projects_status_check CHECK (status IN ('active', 'completed', 'cancelled'))");
        DB::statement("ALTER TABLE tickets ADD CONSTRAINT tickets_status_check CHECK (status IN ('open', 'in_progress', 'closed'))");
        DB::statement("ALTER TABLE tickets ADD CONSTRAINT tickets_priority_check CHECK (priority IN ('low', 'normal', 'high', 'urgent'))");
        DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_version_check CHECK (version >= 1)');
    }

    public function down(): void
    {
        if (DB::getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE tickets DROP CHECK tickets_version_check, DROP CHECK tickets_priority_check, DROP CHECK tickets_status_check');
            DB::statement('ALTER TABLE projects DROP CHECK projects_status_check');
            DB::statement('ALTER TABLE tickets DROP FOREIGN KEY tickets_project_tenant_foreign, DROP FOREIGN KEY tickets_customer_tenant_foreign');
            DB::statement('ALTER TABLE projects DROP FOREIGN KEY projects_customer_tenant_foreign');
            DB::statement('ALTER TABLE projects ADD CONSTRAINT projects_customer_id_foreign FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE RESTRICT');
            DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_customer_id_foreign FOREIGN KEY (customer_id) REFERENCES customers (id) ON DELETE RESTRICT');
            DB::statement('ALTER TABLE tickets ADD CONSTRAINT tickets_project_id_foreign FOREIGN KEY (project_id) REFERENCES projects (id) ON DELETE SET NULL');
        }

        Schema::table('tickets', fn (Blueprint $table) => $table->dropIndex('tickets_workload_lookup'));
        Schema::table('projects', function (Blueprint $table): void {
            $table->dropUnique('projects_scope_name_unique');
            $table->dropUnique('projects_organization_id_id_unique');
        });
        Schema::table('customers', fn (Blueprint $table) => $table->dropUnique('customers_organization_id_id_unique'));
    }
};
