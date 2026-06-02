<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Marketplace\Exceptions\DuplicateSellerCodeException;
use Modules\Marketplace\Models\Seller;
use Modules\Marketplace\Services\SellerRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class SellerRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_and_verify_seller(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'marketplace']);

        $seller = app(SellerRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            'shop-1',
            'Campus Store',
        );

        $this->assertSame('SHOP-1', $seller->code);
        $this->assertSame('pending', $seller->verification_status);

        $verified = app(SellerRegistrationService::class)->verify($seller);
        $this->assertSame('verified', $verified->verification_status);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'marketplace']);

        $service = app(SellerRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'A1', 'Seller A');

        $this->expectException(DuplicateSellerCodeException::class);
        $service->register($tenant->id, $organization->id, 'a1', 'Seller B');
    }
}
