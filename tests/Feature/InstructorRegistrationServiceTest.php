<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Training\Exceptions\DuplicateInstructorCodeException;
use Modules\Training\Models\Instructor;
use Modules\Training\Services\InstructorRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class InstructorRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_instructor(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'training']);

        $instructor = app(InstructorRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            't-01',
            'Trainer One',
        );

        $this->assertSame('T-01', $instructor->code);
        $this->assertDatabaseHas(Instructor::class, ['id' => $instructor->id]);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'training']);

        $service = app(InstructorRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'TR1', 'Trainer A');

        $this->expectException(DuplicateInstructorCodeException::class);
        $service->register($tenant->id, $organization->id, 'tr1', 'Trainer B');
    }
}
