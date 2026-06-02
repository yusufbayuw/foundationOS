<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Sales\Exceptions\DuplicateCustomerCodeException;
use Modules\Sales\Models\Customer;
use Modules\Sales\Services\CustomerRegistrationService;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class CustomerRegistrationServiceTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_register_creates_active_customer_with_normalized_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $customer = app(CustomerRegistrationService::class)->register(
            tenantId: $tenant->id,
            organizationId: $organization->id,
            code: ' coop-01 ',
            name: 'Koperasi Sejahtera',
            email: 'koperasi@example.com',
            isCooperativeMember: true,
            memberNumber: 'M-001',
            memberDiscountPercent: 5,
        );

        $this->assertSame('COOP-01', $customer->code);
        $this->assertTrue($customer->is_active);
        $this->assertTrue($customer->is_cooperative_member);
        $this->assertEqualsWithDelta(5.0, (float) $customer->member_discount_percent, 0.01);
        $this->assertDatabaseHas(Customer::class, [
            'id' => $customer->id,
            'code' => 'COOP-01',
        ]);
    }

    public function test_register_rejects_duplicate_code(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $service = app(CustomerRegistrationService::class);
        $service->register($tenant->id, $organization->id, 'RET-1', 'Retail A');

        $this->expectException(DuplicateCustomerCodeException::class);
        $service->register($tenant->id, $organization->id, 'ret-1', 'Retail B');
    }

    public function test_deactivate_marks_customer_inactive(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $customer = app(CustomerRegistrationService::class)->register(
            $tenant->id,
            $organization->id,
            'TMP',
            'Temporary',
        );

        app(CustomerRegistrationService::class)->deactivate($customer);

        $this->assertFalse($customer->fresh()->is_active);
    }
}
