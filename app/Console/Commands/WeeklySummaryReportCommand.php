<?php

namespace App\Console\Commands;

use App\Services\CrossModuleReportService;
use App\Services\ExecutiveMetricsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\TenantSetting;
use Modules\Core\Models\User;

class WeeklySummaryReportCommand extends Command
{
    protected $signature = 'reports:weekly-summary {--tenant=}';

    protected $description = 'Email weekly cross-module summary PDF to tenant admins';

    public function handle(CrossModuleReportService $reports, ExecutiveMetricsService $executive): int
    {
        $tenantId = $this->option('tenant');
        $tenants = $tenantId
            ? Tenant::query()->whereKey($tenantId)->get()
            : Tenant::query()->where('status', 'active')->get();

        foreach ($tenants as $tenant) {
            if (! $this->isOptedIn((int) $tenant->id)) {
                continue;
            }

            $recipients = $this->recipients((int) $tenant->id);
            if ($recipients === []) {
                $this->warn("Tenant {$tenant->id}: no recipients configured.");

                continue;
            }

            $from = now()->subDays(7)->startOfDay();
            $to = now()->endOfDay();
            $metrics = $executive->forTenant((int) $tenant->id);
            $cost = $reports->costEfficiency((int) $tenant->id, $from, $to);

            $pdf = Pdf::loadView('reports.weekly-summary', [
                'tenant' => $tenant,
                'metrics' => $metrics,
                'cost' => $cost,
                'period' => ['from' => $from->toDateString(), 'to' => $to->toDateString()],
            ]);

            foreach ($recipients as $email) {
                Mail::raw(
                    "Weekly summary for {$tenant->name} ({$from->toDateString()} – {$to->toDateString()})",
                    function ($message) use ($email, $tenant, $pdf): void {
                        $message->to($email)
                            ->subject("FoundationOS Weekly Summary — {$tenant->name}")
                            ->attachData($pdf->output(), 'weekly-summary.pdf', ['mime' => 'application/pdf']);
                    },
                );
            }

            $this->info("Sent weekly summary for tenant {$tenant->id} to ".count($recipients).' recipient(s).');
        }

        return self::SUCCESS;
    }

    protected function isOptedIn(int $tenantId): bool
    {
        return TenantSetting::query()
            ->where('tenant_id', $tenantId)
            ->where('group', 'reports')
            ->where('key', 'weekly_summary_enabled')
            ->value('value') === '1';
    }

    /**
     * @return array<int, string>
     */
    protected function recipients(int $tenantId): array
    {
        $raw = TenantSetting::query()
            ->where('tenant_id', $tenantId)
            ->where('group', 'reports')
            ->where('key', 'weekly_summary_recipients')
            ->value('value');

        if (! $raw) {
            return User::query()
                ->where('is_super_admin', true)
                ->pluck('email')
                ->filter()
                ->all();
        }

        return array_values(array_filter(array_map('trim', explode(',', $raw))));
    }
}
