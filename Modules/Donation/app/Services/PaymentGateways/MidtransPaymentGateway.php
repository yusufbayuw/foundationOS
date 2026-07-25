<?php

namespace Modules\Donation\Services\PaymentGateways;

use Midtrans\Config as MidtransConfig;
use Midtrans\Snap;
use Modules\Donation\Contracts\PaymentGateway;
use Modules\Donation\Models\Donation;

class MidtransPaymentGateway implements PaymentGateway
{
    public function __construct()
    {
        MidtransConfig::$serverKey = (string) config('donation.gateways.midtrans.server_key', config('midtrans.server_key'));
        MidtransConfig::$isProduction = (bool) config('donation.gateways.midtrans.is_production', config('midtrans.is_production'));
        MidtransConfig::$isSanitized = (bool) config('donation.gateways.midtrans.is_sanitized', config('midtrans.is_sanitized'));
        MidtransConfig::$is3ds = (bool) config('donation.gateways.midtrans.is_3ds', config('midtrans.is_3ds'));
    }

    public function createDonationTransaction(Donation $donation, array $options): array
    {
        $donation->loadMissing(['campaign', 'donor']);

        $params = [
            'transaction_details' => [
                'order_id' => $donation->donation_number,
                'gross_amount' => (int) round((float) $donation->amount),
            ],
            'customer_details' => [
                'first_name' => $donation->donor?->name ?? 'Donor',
                'email' => $donation->donor?->email,
                'phone' => $donation->donor?->phone,
            ],
            'item_details' => [[
                'id' => $donation->campaign?->code ?? 'donation',
                'price' => (int) round((float) $donation->amount),
                'quantity' => 1,
                'name' => 'Donation '.$donation->donation_number,
            ]],
            'callbacks' => array_filter([
                'finish' => $options['finish_url'] ?? config('donation.gateways.midtrans.finish_url'),
                'error' => $options['error_url'] ?? config('donation.gateways.midtrans.error_url'),
            ]),
        ];

        $token = Snap::getSnapToken($params);
        $baseUrl = (bool) config('donation.gateways.midtrans.is_production', config('midtrans.is_production'))
            ? 'https://app.midtrans.com/snap/v2/vtweb/'
            : 'https://app.sandbox.midtrans.com/snap/v2/vtweb/';

        return [
            'reference' => $token,
            'token' => $token,
            'redirect_url' => $baseUrl.$token,
            'raw' => ['params' => $params],
        ];
    }
}
