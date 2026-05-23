<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Property\Models\LeaseAgreement;
use Modules\Property\Models\LeaseInvoice;

class PropertyGenerateMonthlyLeaseInvoiceCommand extends Command
{
    protected $signature = 'property:generate-monthly-lease-invoice';

    protected $description = 'Generate monthly lease invoices for active agreements';

    public function handle(): int
    {
        $period = now()->format('Y-m');
        $agreements = LeaseAgreement::withoutTenantScope()
            ->where('status', 'active')
            ->get();

        $created = 0;

        foreach ($agreements as $agreement) {
            $number = 'LEASE-'.$agreement->id.'-'.$period;

            if (LeaseInvoice::withoutTenantScope()->where('invoice_number', $number)->exists()) {
                continue;
            }

            LeaseInvoice::withoutTenantScope()->create([
                'tenant_id' => $agreement->tenant_id,
                'organization_id' => $agreement->organization_id,
                'code' => $number,
                'name' => 'Lease '.$period,
                'status' => 'issued',
                'description' => json_encode([
                    'lease_agreement_id' => $agreement->id,
                    'period' => $period,
                    'amount' => $agreement->meta['monthly_rent'] ?? 0,
                ]),
                'meta' => ['invoice_number' => $number, 'idempotency_key' => $number],
            ]);
            $created++;
        }

        $this->info("Created {$created} lease invoices for {$period}.");

        return self::SUCCESS;
    }
}
