<?php

namespace Tests\Feature;

use Filament\Actions\Exports\Models\Export;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\User;
use Modules\Sales\Filament\Exports\VoucherClaimExporter;
use Modules\Sales\Models\Voucher;
use Modules\Sales\Models\VoucherClaim;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

class VoucherClaimExportTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_voucher_claim_export_includes_claims_across_statuses(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'sales']);

        $voucher = Voucher::factory()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'title' => 'Back to School Discount',
            'code' => 'BTS-2026',
        ]);

        $statuses = ['claimed', 'used', 'expired'];

        foreach ($statuses as $status) {
            $user = User::factory()->create([
                'name' => $status === 'used' ? 'Siti Aminah' : str($status)->headline()->toString().' User',
                'email' => $status === 'used' ? 'siti@example.test' : $status.'@example.test',
            ]);

            VoucherClaim::factory()->create([
                'tenant_id' => $tenant->id,
                'organization_id' => $organization->id,
                'voucher_id' => $voucher->id,
                'user_id' => $user->id,
                'claim_code' => 'CLAIM-'.strtoupper($status),
                'status' => $status,
                'claimed_at' => now()->subDays(3),
                'used_at' => $status === 'used' ? now()->subDay() : null,
            ]);
        }

        $claims = VoucherClaim::query()
            ->with(['voucher', 'user'])
            ->whereBelongsTo($voucher)
            ->orderBy('claim_code')
            ->get();

        $this->assertCount(3, $claims);
        $this->assertEqualsCanonicalizing($statuses, $claims->pluck('status')->all());

        $exportColumnNames = collect(VoucherClaimExporter::getColumns())
            ->map(fn (object $column): string => $column->getName())
            ->all();

        $expectedColumnNames = [
            'voucher.title',
            'voucher.code',
            'user.name',
            'user.email',
            'claim_code',
            'status',
            'claimed_at',
            'used_at',
        ];

        $this->assertSame($expectedColumnNames, $exportColumnNames);

        $exporter = new VoucherClaimExporter(new Export, array_combine($expectedColumnNames, $expectedColumnNames), []);

        $usedClaim = $claims->firstWhere('status', 'used');
        $this->assertNotNull($usedClaim);
        $this->assertSame(
            [
                'Back to School Discount',
                'BTS-2026',
                'Siti Aminah',
                'siti@example.test',
                'CLAIM-USED',
                'used',
                $usedClaim->claimed_at->format('Y-m-d H:i:s'),
                $usedClaim->used_at->format('Y-m-d H:i:s'),
            ],
            $exporter($usedClaim),
        );
    }
}
