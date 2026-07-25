<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Donation\Contracts\PaymentGateway;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Modules\Donation\Services\DonationPaymentService;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\JournalEntry;
use RuntimeException;
use Tests\TestCase;

class DonationCheckoutPaymentGatewayTest extends TestCase
{
    use LazilyRefreshDatabase;

    public function test_successful_checkout_creates_donor_pending_donation_and_reference(): void
    {
        $this->app->instance(PaymentGateway::class, new SuccessfulDonationGateway);
        $tenant = $this->makeTenant();
        $campaign = $this->makeCampaign($tenant);

        $response = $this->postJson(route('donation.checkout'), [
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'amount' => 150000,
            'donor' => [
                'name' => 'Siti Donor',
                'email' => 'SITI@example.com',
                'phone' => '08123456789',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('payment_status', 'pending')
            ->assertJsonPath('payment_reference', 'fake-reference')
            ->assertJsonPath('token', 'fake-token')
            ->assertJsonPath('redirect_url', 'https://payments.example.test/fake-token');

        $this->assertDatabaseHas('donors', [
            'tenant_id' => $tenant->getKey(),
            'name' => 'Siti Donor',
            'email' => 'siti@example.com',
        ]);

        $this->assertDatabaseHas('donations', [
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'amount' => 150000,
            'payment_status' => 'pending',
            'payment_reference' => 'fake-reference',
        ]);
    }

    public function test_failed_gateway_marks_created_donation_failed(): void
    {
        $this->app->instance(PaymentGateway::class, new FailingDonationGateway);
        $tenant = $this->makeTenant();
        $campaign = $this->makeCampaign($tenant);

        $response = $this->postJson(route('donation.checkout'), [
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'amount' => 75000,
            'donor' => ['name' => 'Gateway Failure'],
        ]);

        $response->assertStatus(502)
            ->assertJsonPath('message', 'Unable to create donation payment transaction.');

        $this->assertDatabaseHas('donations', [
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'amount' => 75000,
            'payment_status' => 'failed',
            'payment_reference' => null,
        ]);
    }

    public function test_duplicate_webhook_is_idempotent_and_signature_verified(): void
    {
        config(['midtrans.server_key' => 'donation-secret']);
        $tenant = $this->makeTenant();
        $organization = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $organization);
        $campaign = $this->makeCampaign($tenant);
        $donor = $this->makeDonor($tenant);

        Donation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'donation_number' => 'DON-WEBHOOK-001',
            'amount' => 125000,
            'payment_status' => 'pending',
        ]);

        $payload = $this->webhookPayload('DON-WEBHOOK-001', '125000.00');

        $this->postJson(route('donation.webhook'), $payload)->assertOk();
        $this->postJson(route('donation.webhook'), $payload)->assertOk();

        $this->assertSame(
            1,
            JournalEntry::withoutTenantScope()->where('entry_number', 'DON-DON-WEBHOOK-001')->count(),
        );
        $this->assertSame(125000.0, (float) $campaign->fresh()->raised_amount);

        $invalidPayload = array_merge($payload, ['signature_key' => 'invalid']);
        $this->postJson(route('donation.webhook'), $invalidPayload)
            ->assertStatus(400)
            ->assertJsonPath('message', 'Invalid webhook notification.');
    }

    public function test_paid_webhook_posts_balanced_journal(): void
    {
        config(['midtrans.server_key' => 'donation-secret']);
        $tenant = $this->makeTenant();
        $organization = $this->makeOrganization($tenant);
        $this->seedCoa($tenant, $organization);
        $campaign = $this->makeCampaign($tenant);
        $donor = $this->makeDonor($tenant);

        $donation = Donation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'donation_number' => 'DON-PAID-001',
            'amount' => 200000,
            'payment_status' => 'pending',
        ]);

        app(DonationPaymentService::class)->handleWebhook($this->webhookPayload('DON-PAID-001', '200000.00'));

        $donation->refresh();
        $journal = JournalEntry::withoutTenantScope()->find($donation->journal_entry_id);

        $this->assertSame('paid', $donation->payment_status);
        $this->assertNotNull($journal);
        $this->assertTrue((bool) $journal->is_balanced);
        $this->assertEqualsWithDelta((float) $journal->total_debit, (float) $journal->total_credit, 0.01);
    }

    private function makeTenant(): Tenant
    {
        $plan = SubscriptionPlan::query()->create([
            'code' => 'donation-plan-'.Str::random(4),
            'name' => 'Donation Plan',
            'included_modules' => ['core', 'donation'],
        ]);

        return Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'donation-tenant-'.Str::random(6),
            'name' => 'Donation Tenant',
            'currency' => 'IDR',
            'subscription_plan_id' => $plan->getKey(),
        ]);
    }

    private function makeOrganization(Tenant $tenant): Organization
    {
        return Organization::query()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'ORG-DON',
            'name' => 'Donation Org',
            'type' => 'foundation',
            'is_active' => true,
        ]);
    }

    private function makeCampaign(Tenant $tenant): Campaign
    {
        return Campaign::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => 'CAMP-'.Str::upper(Str::random(4)),
            'name' => 'Beasiswa Donasi',
        ]);
    }

    private function makeDonor(Tenant $tenant): Donor
    {
        return Donor::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => 'Webhook Donor',
        ]);
    }

    private function seedCoa(Tenant $tenant, Organization $organization): void
    {
        ChartOfAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'code' => '1101',
            'name' => 'Kas Bank',
            'type' => 'asset',
            'normal_balance' => 'debit',
            'is_active' => true,
        ]);

        ChartOfAccount::query()->create([
            'tenant_id' => $tenant->getKey(),
            'organization_id' => $organization->getKey(),
            'code' => '4101',
            'name' => 'Pendapatan Donasi',
            'type' => 'revenue',
            'normal_balance' => 'credit',
            'is_active' => true,
        ]);
    }

    /**
     * @return array<string, mixed>
     */
    private function webhookPayload(string $orderId, string $grossAmount): array
    {
        $payload = [
            'order_id' => $orderId,
            'status_code' => '200',
            'gross_amount' => $grossAmount,
            'currency' => 'IDR',
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'bank_transfer',
            'transaction_id' => 'txn-'.$orderId,
        ];
        $payload['signature_key'] = hash('sha512', $orderId.'200'.$grossAmount.config('midtrans.server_key'));

        return $payload;
    }
}

class SuccessfulDonationGateway implements PaymentGateway
{
    public function createDonationTransaction(Donation $donation, array $options): array
    {
        return [
            'reference' => 'fake-reference',
            'token' => 'fake-token',
            'redirect_url' => 'https://payments.example.test/fake-token',
            'raw' => ['donation_number' => $donation->donation_number],
        ];
    }
}

class FailingDonationGateway implements PaymentGateway
{
    public function createDonationTransaction(Donation $donation, array $options): array
    {
        throw new RuntimeException('Gateway unavailable.');
    }
}
