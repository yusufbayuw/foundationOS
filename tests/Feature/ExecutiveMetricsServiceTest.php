<?php

namespace Tests\Feature;

use App\Services\ExecutiveMetricsService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Tests\TestCase;

class ExecutiveMetricsServiceTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_outstanding_ap_aggregates_unpaid_and_partial_vendor_bill_balances(): void
    {
        Cache::flush();

        $tenant = $this->makeTenant('primary');
        $otherTenant = $this->makeTenant('other');
        $vendor = $this->makeVendor($tenant);
        $otherVendor = $this->makeVendor($otherTenant);

        $this->makeVendorBill($tenant, $vendor, 'VB-001', 'unpaid', 1_000_000, 100_000);
        $this->makeVendorBill($tenant, $vendor, 'VB-002', 'partial', 750_000, 250_000);
        $this->makeVendorBill($tenant, $vendor, 'VB-003', 'paid', 500_000, 500_000);
        $this->makeVendorBill($otherTenant, $otherVendor, 'VB-004', 'unpaid', 9_000_000, 0);

        $metrics = app(ExecutiveMetricsService::class)->forTenant((int) $tenant->id);

        $this->assertEqualsWithDelta(1_400_000.0, $metrics['outstanding_ap'], 0.01);
    }

    protected function makeTenant(string $code): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'plan-'.$code,
            'name' => 'Plan '.$code,
            'included_modules' => ['core'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-'.$code,
            'name' => 'Tenant '.$code,
            'subscription_plan_id' => $plan->id,
            'status' => 'active',
        ]);
    }

    protected function makeVendor(Tenant $tenant): Vendor
    {
        return Vendor::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'code' => 'VEN-'.$tenant->code,
            'name' => 'Vendor '.$tenant->code,
        ]);
    }

    protected function makeVendorBill(
        Tenant $tenant,
        Vendor $vendor,
        string $billNumber,
        string $paymentStatus,
        int $totalAmount,
        int $amountPaid,
    ): VendorBill {
        return VendorBill::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'bill_number' => $billNumber,
            'bill_date' => now(),
            'subtotal' => $totalAmount,
            'total_amount' => $totalAmount,
            'amount_paid' => $amountPaid,
            'status' => 'posted',
            'payment_status' => $paymentStatus,
        ]);
    }
}
