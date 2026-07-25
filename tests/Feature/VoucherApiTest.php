<?php

namespace Tests\Feature;

use App\Models\PersonalAccessToken;
use App\Support\CurrentTenant;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Voucher\Models\Voucher;
use Modules\Voucher\Models\VoucherClaim;
use Tests\TestCase;

class VoucherApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    private User $user;

    private Tenant $tenant;

    private string $token;

    protected function setUp(): void
    {
        parent::setUp();

        $plan = SubscriptionPlan::query()->create([
            'code' => 'voucher-api-plan',
            'name' => 'Voucher API Plan',
            'included_modules' => ['core', 'voucher'],
        ]);

        $this->user = User::query()->create([
            'name' => 'Voucher API User',
            'email' => 'voucher-api@example.com',
            'password' => bcrypt('password'),
        ]);

        $this->tenant = Tenant::query()->create([
            'uuid' => (string) Str::uuid(),
            'code' => 'tenant-voucher',
            'name' => 'Voucher Tenant',
            'subscription_plan_id' => $plan->id,
            'created_by' => $this->user->id,
        ]);

        app(CurrentTenant::class)->set($this->tenant);

        $created = $this->user->createToken('voucher-token');
        PersonalAccessToken::query()
            ->findOrFail($created->accessToken->id)
            ->update(['tenant_id' => $this->tenant->id]);

        $this->token = $created->plainTextToken;
    }

    protected function tearDown(): void
    {
        app(CurrentTenant::class)->forget();

        parent::tearDown();
    }

    public function test_user_can_claim_active_voucher(): void
    {
        $voucher = $this->makeVoucher(['quota' => 2]);

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/{$voucher->id}/claim");

        $response->assertCreated()
            ->assertJsonPath('data.voucher_id', $voucher->id)
            ->assertJsonPath('data.user_id', $this->user->id)
            ->assertJsonPath('data.status', 'claimed');

        $this->assertDatabaseHas('voucher_claims', [
            'voucher_id' => $voucher->id,
            'user_id' => $this->user->id,
            'status' => 'claimed',
        ]);
        $this->assertSame(1, $voucher->refresh()->claimed_count);
    }

    public function test_user_cannot_claim_same_voucher_twice(): void
    {
        $voucher = $this->makeVoucher(['quota' => 2]);

        $this->withToken($this->token)->postJson("/api/v1/app/vouchers/{$voucher->id}/claim")->assertCreated();

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/{$voucher->id}/claim");

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'duplicate_claim');

        $this->assertSame(1, VoucherClaim::query()->where('voucher_id', $voucher->id)->count());
        $this->assertSame(1, $voucher->refresh()->claimed_count);
    }

    public function test_quota_exhausted_voucher_cannot_be_claimed(): void
    {
        $voucher = $this->makeVoucher([
            'quota' => 1,
            'claimed_count' => 1,
        ]);

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/{$voucher->id}/claim");

        $response->assertStatus(409)
            ->assertJsonPath('error.code', 'quota_exhausted');
    }

    public function test_expired_voucher_cannot_be_claimed(): void
    {
        $voucher = $this->makeVoucher([
            'start_at' => now()->subDays(3),
            'end_at' => now()->subDay(),
        ]);

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/{$voucher->id}/claim");

        $response->assertStatus(422)
            ->assertJsonPath('error.code', 'voucher_unavailable');
    }

    public function test_user_can_redeem_valid_claim(): void
    {
        $voucher = $this->makeVoucher();
        $claim = VoucherClaim::query()->create([
            'voucher_id' => $voucher->id,
            'user_id' => $this->user->id,
            'claim_code' => 'CLM-VALID01',
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/claims/{$claim->id}/redeem", [
                'claim_code' => 'CLM-VALID01',
            ]);

        $response->assertOk()
            ->assertJsonPath('data.status', 'used');

        $this->assertDatabaseHas('voucher_claims', [
            'id' => $claim->id,
            'status' => 'used',
        ]);
        $this->assertNotNull($claim->refresh()->used_at);
    }

    public function test_redeem_rejects_invalid_claim_code(): void
    {
        $voucher = $this->makeVoucher();
        $claim = VoucherClaim::query()->create([
            'voucher_id' => $voucher->id,
            'user_id' => $this->user->id,
            'claim_code' => 'CLM-VALID02',
            'status' => 'claimed',
            'claimed_at' => now(),
        ]);

        $response = $this->withToken($this->token)
            ->postJson("/api/v1/app/vouchers/claims/{$claim->id}/redeem", [
                'claim_code' => 'CLM-WRONG02',
            ]);

        $response->assertStatus(403)
            ->assertJsonPath('error.code', 'invalid_claim_code');

        $this->assertSame('claimed', $claim->refresh()->status);
    }

    /**
     * @param  array<string, mixed>  $attributes
     */
    private function makeVoucher(array $attributes = []): Voucher
    {
        return Voucher::query()->create(array_merge([
            'tenant_id' => $this->tenant->id,
            'title' => 'Diskon SPP',
            'description' => 'Voucher potongan pembayaran.',
            'code' => 'DISC-SPP',
            'quota' => 10,
            'claimed_count' => 0,
            'start_at' => now()->subDay(),
            'end_at' => now()->addWeek(),
            'status' => 'active',
            'terms' => 'Berlaku satu kali.',
            'redemption_method' => 'manual',
        ], $attributes));
    }
}
