<?php

namespace Modules\Donation\Services;

use Modules\Donation\Models\Donation;
use Spatie\Activitylog\Enums\ActivityEvent;

class DonationPaymentService
{
    public function __construct(
        private readonly DonationJournalService $journalService,
    ) {}

    /**
     * Handle payment webhook (success/fail/duplicate).
     *
     * @param  array<string, mixed>  $payload
     */
    public function handleWebhook(array $payload): Donation
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $status = (string) ($payload['transaction_status'] ?? $payload['status'] ?? '');

        $donation = Donation::query()
            ->where('donation_number', $orderId)
            ->orWhere('payment_reference', $orderId)
            ->firstOrFail();

        if ($donation->payment_status === 'paid') {
            return $donation;
        }

        if (in_array($status, ['capture', 'settlement', 'paid', 'success'], true)) {
            $donation->forceFill([
                'payment_status' => 'paid',
                'paid_at' => now(),
                'payment_reference' => $payload['transaction_id'] ?? $orderId,
            ])->save();

            $freshDonation = $donation->fresh();

            activity()
                ->performedOn($freshDonation)
                ->event(ActivityEvent::Updated)
                ->withProperties(['payment_status' => 'paid', 'webhook_order_id' => $orderId])
                ->log('Donation payment webhook marked donation as paid');

            $this->journalService->postForPaidDonation($freshDonation);
        } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure', 'failed'], true)) {
            $donation->forceFill(['payment_status' => 'failed'])->save();

            activity()
                ->performedOn($donation->fresh())
                ->event(ActivityEvent::Updated)
                ->withProperties(['payment_status' => 'failed', 'webhook_order_id' => $orderId])
                ->log('Donation payment webhook marked donation as failed');
        }

        return $donation->fresh();
    }
}
