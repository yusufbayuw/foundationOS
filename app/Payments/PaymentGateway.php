<?php

namespace App\Payments;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $options
     * @return array{reference: string, status?: string, redirect_url?: string|null, payload?: array<string, mixed>}
     */
    public function createOrderTransaction($order, array $options): array;
}
