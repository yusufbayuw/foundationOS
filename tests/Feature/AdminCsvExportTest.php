<?php

namespace Tests\Feature;

use App\Models\Role;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Gate;
use Modules\Core\Models\User;
use Modules\Donation\Filament\Exports\DonationExporter;
use Modules\Donation\Filament\Exports\DonorExporter;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Modules\Donation\Policies\DonationPolicy;
use Modules\Donation\Policies\DonorPolicy;
use Modules\Marketplace\Filament\Exports\MarketplaceOrderExporter;
use Modules\Marketplace\Models\MarketplaceOrder;
use Modules\Marketplace\Policies\MarketplaceOrderPolicy;
use Modules\MerchOrder\Filament\Exports\MerchOrderExporter;
use Modules\MerchOrder\Models\MerchOrder;
use Modules\MerchOrder\Policies\MerchOrderPolicy;
use Modules\Voucher\Filament\Exports\VoucherClaimExporter;
use Modules\Voucher\Models\VoucherClaim;
use Modules\Voucher\Policies\VoucherClaimPolicy;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class AdminCsvExportTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_exporters_define_required_csv_columns(): void
    {
        $this->assertExporterColumns(DonorExporter::class, [
            'name',
            'email',
            'phone',
        ]);

        $this->assertExporterColumns(DonationExporter::class, [
            'donor.name',
            'donor.email',
            'donor.phone',
            'campaign.name',
            'amount',
            'payment_status',
            'paid_at',
        ]);

        $this->assertExporterColumns(MarketplaceOrderExporter::class, [
            'code',
            'customer_name',
            'total',
            'status',
            'pickup_status',
            'pickup_date',
        ]);

        $this->assertExporterColumns(MerchOrderExporter::class, [
            'code',
            'customer_name',
            'total',
            'status',
            'pickup_status',
            'pickup_date',
        ]);

        $this->assertExporterColumns(VoucherClaimExporter::class, [
            'voucher.name',
            'user.name',
            'claim_code',
            'status',
            'claimed_at',
            'used_at',
        ]);
    }

    public function test_order_export_attributes_are_read_from_order_metadata(): void
    {
        $marketplaceOrder = new MarketplaceOrder([
            'name' => 'Fallback Customer',
            'meta' => [
                'customer_name' => 'Marketplace Customer',
                'total' => '125000.00',
                'pickup_status' => 'ready',
                'pickup_date' => '2026-07-14',
            ],
        ]);

        $merchOrder = new MerchOrder([
            'name' => 'Fallback Student',
            'meta' => [
                'user_name' => 'Merch Customer',
                'total_amount' => '99000.00',
                'pickup_status' => 'picked_up',
                'picked_up_at' => '2026-07-15 09:00:00',
            ],
        ]);

        $this->assertSame('Marketplace Customer', $marketplaceOrder->customer_name);
        $this->assertSame('125000.00', $marketplaceOrder->total);
        $this->assertSame('ready', $marketplaceOrder->pickup_status);
        $this->assertSame('2026-07-14', $marketplaceOrder->pickup_date);

        $this->assertSame('Merch Customer', $merchOrder->customer_name);
        $this->assertSame('99000.00', $merchOrder->total);
        $this->assertSame('picked_up', $merchOrder->pickup_status);
        $this->assertSame('2026-07-15 09:00:00', $merchOrder->pickup_date);
    }

    public function test_export_policies_require_explicit_admin_export_permissions(): void
    {
        $this->registerExportPolicies();

        $plainUser = User::factory()->create();
        $adminUser = User::factory()->create();

        $this->assignExportPermissions($adminUser, [
            'Export:Donor',
            'Export:Donation',
            'Export:MarketplaceOrder',
            'Export:MerchOrder',
            'Export:VoucherClaim',
        ]);

        foreach ([Donor::class, Donation::class, MarketplaceOrder::class, MerchOrder::class, VoucherClaim::class] as $model) {
            $this->assertFalse($plainUser->can('export', $model));
            $this->assertTrue($adminUser->can('export', $model));
        }
    }

    /**
     * @param  class-string  $exporter
     * @param  list<string>  $expectedColumns
     */
    private function assertExporterColumns(string $exporter, array $expectedColumns): void
    {
        $actualColumns = collect($exporter::getColumns())
            ->map(fn (object $column): string => method_exists($column, 'getName') ? $column->getName() : $column->getName)
            ->all();

        $this->assertSame($expectedColumns, $actualColumns);
    }

    private function registerExportPolicies(): void
    {
        Gate::policy(Donor::class, DonorPolicy::class);
        Gate::policy(Donation::class, DonationPolicy::class);
        Gate::policy(MarketplaceOrder::class, MarketplaceOrderPolicy::class);
        Gate::policy(MerchOrder::class, MerchOrderPolicy::class);
        Gate::policy(VoucherClaim::class, VoucherClaimPolicy::class);
    }

    /**
     * @param  list<string>  $permissions
     */
    private function assignExportPermissions(User $user, array $permissions): void
    {
        setPermissionsTeamId(0);

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $role = Role::firstOrCreate(['name' => 'export_admin', 'guard_name' => 'web']);
        $role->syncPermissions($permissions);

        $user->roles()->syncWithoutDetaching([
            $role->id => [
                'model_type' => $user->getMorphClass(),
                'tenant_id' => 0,
            ],
        ]);

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        setPermissionsTeamId(0);
    }
}
