<?php

namespace Modules\Donation\Services;

use App\Services\Billing\MidtransWebhookVerifier;
use Illuminate\Support\Facades\DB;
use Modules\Donation\Models\Donation;

class DonationPaymentService
{
    public function __construct(
        private readonly DonationJournalService $journalService,
        private readonly MidtransWebhookVerifier $webhookVerifier,
    ) {}

    /**
     * Handle payment webhook (success/fail/duplicate).
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload): Donation
    {
        $this->webhookVerifier->verifySignature($payload);

        $orderId = (string) ($payload['order_id'] ?? '');
        $status = (string) ($payload['transaction_status'] ?? $payload['status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? '');

        return DB::transaction(function () use ($payload, $orderId, $status, $fraudStatus): Donation {
            $donation = Donation::withoutTenantScope()
                ->where('donation_number', $orderId)
                ->orWhere('payment_reference', $orderId)
                ->lockForUpdate()
                ->firstOrFail();

            if ($donation->payment_status === 'paid') {
                if (! $donation->journal_entry_id) {
                    $this->journalService->postForPaidDonation($donation->fresh());
                }

                return $donation->fresh();
            }

            if ($this->isPaidStatus($status, $fraudStatus)) {
                $donation->forceFill([
                    'payment_status' => 'paid',
                    'paid_at' => now(),
                    'payment_reference' => $payload['transaction_id'] ?? $orderId,
                ])->save();

                $this->journalService->postForPaidDonation($donation->fresh());
            } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure', 'failed'], true)) {
                $donation->forceFill(['payment_status' => 'failed'])->save();
            }

            return $donation->fresh();
        });
    }

    private function isPaidStatus(string $status, string $fraudStatus): bool
    {
        return $status === 'settlement'
            || $status === 'paid'
            || $status === 'success'
            || ($status === 'capture' && ($fraudStatus === '' || $fraudStatus === 'accept'));
    }
}
