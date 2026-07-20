<?php

namespace Tests\Feature\Donation;

use App\Models\PersonalAccessToken;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Tests\TestCase;

class DonationApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private Tenant $tenant;

    private User $user;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::query()->create([
            'code' => 'donation-api-plan',
            'name' => 'Donation API Plan',
            'included_modules' => ['core', 'donation'],
        ]);

        $this->user = User::query()->create([
            'name' => 'Donation User',
            'email' => 'donation-user@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'donation-api-tenant',
            'name' => 'Donation API Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        $token = $this->user->createToken('donation-api-token');
        PersonalAccessToken::query()
            ->findOrFail($token->accessToken->id)
            ->update(['tenant_id' => $this->tenant->id]);

        $this->token = $token->plainTextToken;
    }

    public function test_lists_active_public_campaigns_only(): void
    {
        $publicCampaign = $this->campaign(['code' => 'PUBLIC', 'name' => 'Public Campaign']);
        $this->campaign(['code' => 'PRIVATE', 'name' => 'Private Campaign', 'is_public' => false]);
        $this->campaign(['code' => 'DRAFT', 'name' => 'Draft Campaign', 'status' => 'draft']);

        $response = $this->withToken($this->token)->getJson('/api/v1/donation/campaigns');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $publicCampaign->id)
            ->assertJsonMissing(['code' => 'PRIVATE'])
            ->assertJsonMissing(['code' => 'DRAFT']);
    }

    public function test_shows_campaign_detail_with_target_progress(): void
    {
        $campaign = $this->campaign([
            'goal_amount' => 1000,
            'raised_amount' => 250,
        ]);

        $response = $this->withToken($this->token)->getJson("/api/v1/donation/campaigns/{$campaign->id}");

        $response->assertOk()
            ->assertJsonPath('data.id', $campaign->id)
            ->assertJsonPath('data.raised_amount', 250)
            ->assertJsonPath('data.goal_amount', 1000)
            ->assertJsonPath('data.progress_percentage', 25);
    }

    public function test_creates_donation_checkout(): void
    {
        $campaign = $this->campaign();

        $response = $this->withToken($this->token)->postJson('/api/v1/donation/checkout', [
            'campaign_id' => $campaign->id,
            'amount' => 150000,
            'donor' => [
                'name' => 'API Donor',
                'email' => 'api-donor@example.com',
            ],
        ]);

        $response->assertCreated()
            ->assertJsonPath('data.amount', 150000)
            ->assertJsonPath('data.payment_status', 'pending')
            ->assertJsonPath('data.campaign.id', $campaign->id)
            ->assertJsonPath('data.donor.user_id', $this->user->id);

        $this->assertDatabaseHas('donations', [
            'campaign_id' => $campaign->id,
            'amount' => 150000,
            'payment_status' => 'pending',
        ]);
    }

    public function test_lists_my_donations_by_donor_user_id_only(): void
    {
        $campaign = $this->campaign();
        $ownedDonor = $this->donor($this->user);
        $otherDonor = $this->donor($this->user(['email' => 'other-donor@example.com']));
        $ownedDonation = $this->donation($campaign, $ownedDonor, ['donation_number' => 'DON-MINE']);
        $this->donation($campaign, $otherDonor, ['donation_number' => 'DON-OTHER']);

        $response = $this->withToken($this->token)->getJson('/api/v1/donation/my-donations');

        $response->assertOk()
            ->assertJsonPath('data.0.id', $ownedDonation->id)
            ->assertJsonMissing(['donation_number' => 'DON-OTHER']);
    }

    public function test_pdf_receipt_is_forbidden_for_other_users_donation(): void
    {
        $campaign = $this->campaign();
        $otherDonor = $this->donor($this->user(['email' => 'pdf-owner@example.com']));
        $donation = $this->donation($campaign, $otherDonor, [
            'donation_number' => 'DON-PDF-OTHER',
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $response = $this->withToken($this->token)->get("/api/v1/donation/donations/{$donation->id}/receipt.pdf");

        $response->assertForbidden();
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function campaign(array $attributes = []): Campaign
    {
        return Campaign::query()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'code' => 'CAM-'.Str::upper(Str::random(8)),
            'name' => 'Campaign '.Str::random(5),
            'category' => 'zakat',
            'goal_amount' => 1000000,
            'raised_amount' => 0,
            'status' => 'active',
            'is_public' => true,
            'description' => 'Donation campaign.',
        ], $attributes));
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function user(array $attributes = []): User
    {
        return User::query()->create(array_merge([
            'name' => 'Other User',
            'email' => 'other-'.Str::random(8).'@example.com',
            'password' => bcrypt('password'),
        ], $attributes));
    }

    private function donor(User $user): Donor
    {
        return Donor::query()->create([
            'tenant_id' => $this->tenant->id,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'phone' => null,
            'is_anonymous' => false,
            'tags' => [],
        ]);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function donation(Campaign $campaign, Donor $donor, array $attributes = []): Donation
    {
        return Donation::query()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'campaign_id' => $campaign->id,
            'donor_id' => $donor->id,
            'donation_number' => 'DON-'.Str::upper(Str::random(8)),
            'amount' => 100000,
            'payment_status' => 'pending',
            'payment_reference' => 'REF-'.Str::upper(Str::random(8)),
            'certificate_token' => Str::uuid()->toString(),
        ], $attributes));
    }
}
