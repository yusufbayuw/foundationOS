<?php

namespace App\Payments;

use Illuminate\Support\Str;

class NullPaymentGateway implements PaymentGateway
{
    /**
     * @param  array<string, mixed>  $options
     * @return array{reference: string, status: string, redirect_url: null, payload: array<string, mixed>}
     */
    public function createOrderTransaction($order, array $options): array
    {
        return [
            'reference' => 'checkout_'.Str::uuid()->toString(),
            'status' => 'pending',
            'redirect_url' => null,
            'payload' => ['provider' => 'null', 'options' => $options],
        ];
    }
}
