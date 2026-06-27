<?php

namespace Modules\Donation\Services;

use App\Support\TypedValue;
use Modules\Donation\Models\Donation;

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
        $orderId = TypedValue::string($payload['order_id'] ?? '');
        $status = TypedValue::string($payload['transaction_status'] ?? $payload['status'] ?? '');

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

            $freshDonation = TypedValue::model($donation->fresh());
            $this->journalService->postForPaidDonation($freshDonation);
        } elseif (in_array($status, ['deny', 'cancel', 'expire', 'failure', 'failed'], true)) {
            $donation->forceFill(['payment_status' => 'failed'])->save();
        }

        return TypedValue::model($donation->fresh());
    }
}
