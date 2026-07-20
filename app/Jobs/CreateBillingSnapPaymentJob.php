<?php

namespace App\Jobs;

use App\Services\BillingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Modules\Core\Models\SubscriptionLog;
use Modules\Core\Models\Tenant;
use Throwable;

class CreateBillingSnapPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public int $timeout = 30;

    public function __construct(
        public readonly int $tenantId,
        public readonly int $subscriptionLogId,
        public readonly ?int $actorId,
    ) {}

    public function handle(BillingService $billingService): void
    {
        $invoice = SubscriptionLog::query()
            ->whereKey($this->subscriptionLogId)
            ->where('tenant_id', $this->tenantId)
            ->first();

        $tenant = Tenant::query()->find($this->tenantId);

        if (! $invoice || ! $tenant) {
            return;
        }

        if ($this->hasPreparedPaymentSession($invoice)) {
            return;
        }

        try {
            $billingService->createSnapPayment($tenant, $invoice, $this->actorId);
        } catch (Throwable $exception) {
            $this->markPaymentSessionFailed($invoice, $exception);

            throw $exception;
        }
    }

    /**
     * @return array<int, int>
     */
    public function backoff(): array
    {
        return [10, 30, 60];
    }

    private function hasPreparedPaymentSession(SubscriptionLog $invoice): bool
    {
        return (bool) ($invoice->invoice_url && (($invoice->metadata ?? [])['snap_token'] ?? null));
    }

    private function markPaymentSessionFailed(SubscriptionLog $invoice, Throwable $exception): void
    {
        DB::transaction(function () use ($invoice, $exception): void {
            $lockedInvoice = SubscriptionLog::query()
                ->whereKey($invoice->getKey())
                ->lockForUpdate()
                ->first();

            if (! $lockedInvoice) {
                return;
            }

            $lockedInvoice->update([
                'metadata' => array_merge($lockedInvoice->metadata ?? [], [
                    'payment_session_status' => 'failed',
                    'payment_session_failed_at' => now()->toISOString(),
                    'payment_session_error' => mb_substr($exception->getMessage(), 0, 500),
                ]),
            ]);
        });
    }
}
