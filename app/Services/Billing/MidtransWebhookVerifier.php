<?php

namespace App\Services\Billing;

class MidtransWebhookVerifier
{
    /**
     * @param  array<string, mixed>  $payload
     */
    public function verifySignature(array $payload): void
    {
        foreach (['order_id', 'status_code', 'gross_amount', 'signature_key'] as $field) {
            if (! array_key_exists($field, $payload) || blank($payload[$field])) {
                throw MidtransWebhookException::missingField($field);
            }
        }

        $expectedSignature = $this->signature(
            (string) $payload['order_id'],
            (string) $payload['status_code'],
            (string) $payload['gross_amount'],
        );

        if (! hash_equals($expectedSignature, (string) $payload['signature_key'])) {
            throw MidtransWebhookException::invalidSignature();
        }
    }

    public function signature(string $orderId, string $statusCode, string $grossAmount): string
    {
        return hash('sha512', $orderId.$statusCode.$grossAmount.(string) config('midtrans.server_key'));
    }
}
