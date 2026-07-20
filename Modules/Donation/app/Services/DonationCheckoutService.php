<?php

namespace Modules\Donation\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Donation\Contracts\PaymentGateway;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Throwable;

class DonationCheckoutService
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway,
    ) {}

    /**
     * @param  array{tenant_id: int, campaign_id: int, amount: numeric, donor?: array{name?: string, email?: string|null, phone?: string|null, is_anonymous?: bool}, options?: array<string, mixed>}  $data
     * @return array{donation: Donation, gateway: array<string, mixed>}
     *
     * @throws Throwable
     */
    public function checkout(array $data): array
    {
        $donor = $this->resolveDonor($data);

        $donation = Donation::withoutTenantScope()->create([
            'tenant_id' => $data['tenant_id'],
            'campaign_id' => $data['campaign_id'],
            'donor_id' => $donor->getKey(),
            'donation_number' => $this->donationNumber(),
            'amount' => $data['amount'],
            'payment_status' => 'pending',
        ]);

        try {
            $gateway = $this->paymentGateway->createDonationTransaction(
                $donation->fresh(['campaign', 'donor']),
                $data['options'] ?? [],
            );
        } catch (Throwable $throwable) {
            $donation->forceFill(['payment_status' => 'failed'])->save();

            throw $throwable;
        }

        $donation->forceFill([
            'payment_reference' => $gateway['reference'],
        ])->save();

        return [
            'donation' => $donation->fresh(['campaign', 'donor']),
            'gateway' => $gateway,
        ];
    }

    /**
     * @param  array{tenant_id: int, donor?: array{name?: string, email?: string|null, phone?: string|null, is_anonymous?: bool}}  $data
     */
    private function resolveDonor(array $data): Donor
    {
        $donorData = $data['donor'] ?? [];
        $isAnonymous = (bool) ($donorData['is_anonymous'] ?? false);
        $email = $isAnonymous ? null : $this->normalizeEmail($donorData['email'] ?? null);

        if ($email !== null) {
            $existingDonor = Donor::withoutTenantScope()
                ->where('tenant_id', $data['tenant_id'])
                ->where('is_anonymous', false)
                ->whereRaw('LOWER(email) = ?', [$email])
                ->first();

            if ($existingDonor) {
                return $existingDonor;
            }
        }

        return Donor::withoutTenantScope()->create([
            'tenant_id' => $data['tenant_id'],
            'name' => $isAnonymous ? 'Anonymous Donor' : trim((string) ($donorData['name'] ?? 'Donor')),
            'email' => $email,
            'phone' => isset($donorData['phone']) ? trim((string) $donorData['phone']) : null,
            'is_anonymous' => $isAnonymous,
            'tags' => ['checkout'],
        ]);
    }

    private function normalizeEmail(?string $email): ?string
    {
        $normalized = $email !== null ? strtolower(trim($email)) : null;

        return $normalized !== '' ? $normalized : null;
    }

    private function donationNumber(): string
    {
        return DB::transaction(function (): string {
            do {
                $number = 'DON-'.now()->format('Ymd').'-'.Str::upper(Str::random(8));
            } while (Donation::withoutTenantScope()->where('donation_number', $number)->exists());

            return $number;
        });
    }
}
