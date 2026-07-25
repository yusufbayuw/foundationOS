<?php

namespace Modules\Donation\Contracts;

use Modules\Donation\Models\Donation;

interface PaymentGateway
{
    /**
     * @param  array<string, mixed>  $options
     * @return array{reference: string, token?: string|null, redirect_url?: string|null, raw?: array<string, mixed>}
     */
    public function createDonationTransaction(Donation $donation, array $options): array;
}
