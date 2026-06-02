<?php

namespace Tests\Feature;

use App\Filament\Imports\CustomerImporter;
use App\Filament\Imports\DonorImporter;
use App\Filament\Imports\RiskCategoryImporter;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Donation\Models\Donor;
use Modules\Risk\Models\RiskCategory;
use Modules\Sales\Models\Customer;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class MaturingModuleFactoriesTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_donor_factory_persists_for_tenant(): void
    {
        ['tenant' => $tenant] = $this->makeTenantContext(['core', 'donation']);

        $donor = Donor::factory()->create(['tenant_id' => $tenant->id]);

        $this->assertNotNull($donor->email);
        $this->assertFalse($donor->is_anonymous);
    }

    public function test_customer_factory_cooperative_state(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $customer = Customer::factory()
            ->cooperative('M-100', 7.5)
            ->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
            ]);

        $this->assertTrue($customer->is_cooperative_member);
        $this->assertEqualsWithDelta(7.5, (float) $customer->member_discount_percent, 0.01);
    }

    public function test_risk_category_factory_persists(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'risk']);

        $category = RiskCategory::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
        ]);

        $this->assertSame('active', $category->status);
        $this->assertNotEmpty($category->code);
    }

    public function test_ga_modules_expose_csv_import_columns(): void
    {
        $this->assertGreaterThanOrEqual(3, count(DonorImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(CustomerImporter::getColumns()));
        $this->assertGreaterThanOrEqual(3, count(RiskCategoryImporter::getColumns()));
    }
}
