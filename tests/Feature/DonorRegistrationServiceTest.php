<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Donation\Exceptions\DuplicateDonorEmailException;
use Modules\Donation\Services\DonorRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class DonorRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_donor_with_normalized_email(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'donation']);

        $donor = app(DonorRegistrationService::class)->register(
            tenantId: $tenant->id,
            name: 'Jane Donor',
            email: ' Jane@Example.COM ',
            phone: '08123456789',
            tags: ['alumni'],
        );

        $this->assertSame('jane@example.com', $donor->email);
        $this->assertFalse($donor->is_anonymous);
        $this->assertSame(['alumni'], $donor->tags);
    }

    public function test_anonymous_donor_omits_email_even_when_provided(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'donation']);

        $donor = app(DonorRegistrationService::class)->register(
            tenantId: $tenant->id,
            name: 'Anonymous',
            email: 'hidden@example.com',
            isAnonymous: true,
        );

        $this->assertTrue($donor->is_anonymous);
        $this->assertNull($donor->email);
    }

    public function test_register_rejects_duplicate_email(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'donation']);

        $service = app(DonorRegistrationService::class);
        $service->register($tenant->id, 'First', 'dup@example.com');

        $this->expectException(DuplicateDonorEmailException::class);
        $service->register($tenant->id, 'Second', 'DUP@example.com');
    }
}
