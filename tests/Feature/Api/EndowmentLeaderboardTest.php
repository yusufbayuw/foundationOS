<?php

namespace Tests\Feature\Api;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Tests\TestCase;

class EndowmentLeaderboardTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_leaderboard_sums_paid_endowment_donations_only(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $campaign = $this->createCampaign($tenant, 'endowment');
        $regularCampaign = $this->createCampaign($tenant, 'general');
        $donor = $this->createDonor($tenant, 'Siti Aminah');

        $this->createDonation($tenant, $campaign, $donor, '100.00', 'paid');
        $this->createDonation($tenant, $campaign, $donor, '250.00', 'paid');
        $this->createDonation($tenant, $campaign, $donor, '999.00', 'pending');
        $this->createDonation($tenant, $regularCampaign, $donor, '777.00', 'paid');

        $this->withToken($this->createTenantToken($user, $tenant))
            ->getJson('/api/v1/app/endowments/leaderboard')
            ->assertOk()
            ->assertJsonPath('data.0.donor_name', 'Siti Aminah')
            ->assertJsonPath('data.0.total_amount', '350.00')
            ->assertJsonPath('data.0.donation_count', 2)
            ->assertJsonPath('meta.period', 'all-time')
            ->assertJsonCount(1, 'data');
    }

    public function test_anonymous_donor_is_displayed_as_anonymous(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $campaign = $this->createCampaign($tenant, 'endowment');
        $donor = $this->createDonor($tenant, 'Hidden Benefactor', isAnonymous: true);

        $this->createDonation($tenant, $campaign, $donor, '125.00', 'paid');

        $this->withToken($this->createTenantToken($user, $tenant))
            ->getJson('/api/v1/app/endowments/leaderboard')
            ->assertOk()
            ->assertJsonPath('data.0.donor_name', 'Anonymous')
            ->assertJsonPath('data.0.is_anonymous', true);
    }

    public function test_leaderboard_orders_by_total_amount_descending(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $campaign = $this->createCampaign($tenant, 'endowment');
        $smallerDonor = $this->createDonor($tenant, 'Budi');
        $largerDonor = $this->createDonor($tenant, 'Ani');

        $this->createDonation($tenant, $campaign, $smallerDonor, '50.00', 'paid');
        $this->createDonation($tenant, $campaign, $largerDonor, '150.00', 'paid');

        $response = $this->withToken($this->createTenantToken($user, $tenant))
            ->getJson('/api/v1/app/endowments/leaderboard')
            ->assertOk();

        $this->assertSame(['Ani', 'Budi'], collect($response->json('data'))->pluck('donor_name')->all());
    }

    public function test_leaderboard_is_isolated_to_token_tenant(): void
    {
        $tenantA = Tenant::factory()->create();
        $tenantB = Tenant::factory()->create();
        $user = User::factory()->create();
        $campaignA = $this->createCampaign($tenantA, 'endowment');
        $campaignB = $this->createCampaign($tenantB, 'endowment');
        $donorA = $this->createDonor($tenantA, 'Tenant A Donor');
        $donorB = $this->createDonor($tenantB, 'Tenant B Donor');

        $this->createDonation($tenantA, $campaignA, $donorA, '100.00', 'paid');
        $this->createDonation($tenantB, $campaignB, $donorB, '999.00', 'paid');

        $this->withToken($this->createTenantToken($user, $tenantA))
            ->getJson('/api/v1/app/endowments/leaderboard')
            ->assertOk()
            ->assertJsonPath('data.0.donor_name', 'Tenant A Donor')
            ->assertJsonCount(1, 'data');
    }

    public function test_leaderboard_can_be_filtered_by_period(): void
    {
        $tenant = Tenant::factory()->create();
        $user = User::factory()->create();
        $campaign = $this->createCampaign($tenant, 'endowment');
        $donor = $this->createDonor($tenant, 'Period Donor');

        $this->createDonation($tenant, $campaign, $donor, '100.00', 'paid', now());
        $this->createDonation($tenant, $campaign, $donor, '200.00', 'paid', now()->subYear());

        $this->withToken($this->createTenantToken($user, $tenant))
            ->getJson('/api/v1/app/endowments/leaderboard?period=monthly')
            ->assertOk()
            ->assertJsonPath('data.0.total_amount', '100.00')
            ->assertJsonPath('meta.period', 'monthly');
    }

    private function createCampaign(Tenant $tenant, string $category): Campaign
    {
        return Campaign::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'code' => fake()->unique()->bothify('CMP-####'),
            'name' => fake()->words(3, true),
            'category' => $category,
            'status' => 'active',
        ]);
    }

    private function createDonor(Tenant $tenant, string $name, bool $isAnonymous = false): Donor
    {
        return Donor::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'name' => $name,
            'email' => $isAnonymous ? null : fake()->unique()->safeEmail(),
            'is_anonymous' => $isAnonymous,
        ]);
    }

    private function createDonation(
        Tenant $tenant,
        Campaign $campaign,
        Donor $donor,
        string $amount,
        string $paymentStatus,
        mixed $paidAt = null,
    ): Donation {
        return Donation::withoutTenantScope()->create([
            'tenant_id' => $tenant->getKey(),
            'campaign_id' => $campaign->getKey(),
            'donor_id' => $donor->getKey(),
            'donation_number' => fake()->unique()->bothify('DON-####'),
            'amount' => $amount,
            'payment_status' => $paymentStatus,
            'paid_at' => $paymentStatus === 'paid' ? ($paidAt ?? now()) : null,
        ]);
    }

    private function createTenantToken(User $user, Tenant $tenant): string
    {
        $token = $user->createToken('endowment-leaderboard-token');

        PersonalAccessToken::query()
            ->whereKey($token->accessToken->getKey())
            ->update(['tenant_id' => $tenant->getKey()]);

        return $token->plainTextToken;
    }
}
