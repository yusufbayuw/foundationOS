<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Modules\Core\Models\Tenant;

#[Signature('fos:billing:generate-invoices {--tenant= : Specific tenant ID}')]
#[Description('Generate monthly subscription invoices for all active tenants')]
class GenerateMonthlyBillingCommand extends Command
{
    public function handle(BillingService $billingService): int
    {
        $query = Tenant::query()
            ->whereIn('status', ['active', 'trial'])
            ->whereNotNull('subscription_plan_id')
            ->with('subscriptionPlan');

        if ($tenantId = $this->option('tenant')) {
            $query->where('id', $tenantId);
        }

        $tenants = $query->get();

        if ($tenants->isEmpty()) {
            $this->info('No tenants to bill.');

            return self::SUCCESS;
        }

        $generated = 0;
        $skipped = 0;

        foreach ($tenants as $tenant) {
            $alreadyBilled = $tenant->subscriptionLogs()
                ->where('action', 'monthly_invoice')
                ->whereYear('period_start', now()->year)
                ->whereMonth('period_start', now()->month)
                ->exists();

            if ($alreadyBilled) {
                $this->line("  Skipped [{$tenant->code}] — already billed this month.");
                $skipped++;

                continue;
            }

            $amounts = $billingService->calculateMonthlyAmount($tenant);

            if ($amounts['total'] <= 0) {
                $this->line("  Skipped [{$tenant->code}] — zero amount (free plan).");
                $skipped++;

                continue;
            }

            $invoice = $billingService->generateInvoice($tenant);

            $tenant->update([
                'status' => $tenant->status === 'trial' ? 'active' : $tenant->status,
                'subscription_expires_at' => now()->endOfMonth(),
            ]);

            $this->info("  Generated invoice [{$invoice->invoice_number}] for [{$tenant->code}] — IDR ".number_format($amounts['total']));
            $generated++;
        }

        $this->line('');
        $this->info("Done. Generated: {$generated}, Skipped: {$skipped}");

        return self::SUCCESS;
    }
}
