<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class ProductionHealthTest extends TestCase
{
    use RefreshDatabase;

    public function test_liveness_identifies_the_running_version_without_dependencies(): void
    {
        config(['app.version' => 'test-sha']);

        $this->getJson('/api/health/live')
            ->assertOk()
            ->assertExactJson(['status' => 'alive', 'version' => 'test-sha']);
    }

    public function test_readiness_proves_database_and_cache_round_trips(): void
    {
        config(['app.version' => 'test-sha']);

        $this->getJson('/api/health/ready')
            ->assertOk()
            ->assertJsonPath('status', 'ready')
            ->assertJsonPath('dependencies.database', 'available')
            ->assertJsonPath('dependencies.cache', 'available');
    }
}
