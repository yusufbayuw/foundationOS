<?php

namespace Modules\Donation\Providers;

use Modules\Donation\Contracts\PaymentGateway;
use Modules\Donation\Services\PaymentGateways\MidtransPaymentGateway;
use Nwidart\Modules\Support\ModuleServiceProvider;

class DonationServiceProvider extends ModuleServiceProvider
{
    public function register(): void
    {
        parent::register();

        $this->app->bind(PaymentGateway::class, function (): PaymentGateway {
            return match ((string) config('donation.payment_gateway', 'midtrans')) {
                'midtrans' => $this->app->make(MidtransPaymentGateway::class),
                default => $this->app->make(MidtransPaymentGateway::class),
            };
        });
    }

    protected string $name = 'Donation';

    protected string $nameLower = 'donation';

    protected array $providers = [
        EventServiceProvider::class,
        RouteServiceProvider::class,
    ];
}
