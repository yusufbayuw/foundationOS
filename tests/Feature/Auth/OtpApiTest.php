<?php

namespace Tests\Feature\Auth;

use App\Models\OtpCode;
use App\Models\User;
use App\Services\Auth\OtpMessenger;
use App\Services\Auth\OtpPurpose;
use App\Services\Auth\OtpService;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class OtpApiTest extends TestCase
{
    use LazilyRefreshDatabase;

    protected FakeOtpMessenger $messenger;

    protected function setUp(): void
    {
        parent::setUp();

        RateLimiter::clear('otp@example.com');
        RateLimiter::clear('otp@example.com|127.0.0.1');

        $this->messenger = new FakeOtpMessenger;
        $this->app->instance(OtpMessenger::class, $this->messenger);
    }

    public function test_successful_consume_marks_otp_as_consumed(): void
    {
        $this->postJson('/api/v1/auth/otp/request', [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Registration->value,
        ])->assertCreated();

        $code = $this->messenger->lastCode;

        $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Registration->value,
            'code' => $code,
        ])->assertOk()->assertJsonPath('data.status', 'valid');

        $result = app(OtpService::class)->consume('otp@example.com', OtpPurpose::Registration, $code);

        $this->assertSame('valid', $result->value);
        $this->assertNotNull(OtpCode::query()->first()?->consumed_at);
    }

    public function test_expired_otp_is_rejected(): void
    {
        OtpCode::query()->create([
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->subMinute(),
        ]);

        $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code' => '123456',
        ])->assertStatus(422)->assertJsonPath('error.code', 'expired');
    }

    public function test_wrong_code_increments_attempts(): void
    {
        OtpCode::query()->create([
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code' => '654321',
        ])->assertStatus(422)->assertJsonPath('error.code', 'invalid');

        $this->assertSame(1, OtpCode::query()->first()?->attempts);
    }

    public function test_max_attempts_blocks_further_verification(): void
    {
        OtpCode::query()->create([
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code_hash' => Hash::make('123456'),
            'expires_at' => now()->addMinutes(5),
            'attempts' => OtpService::MAX_ATTEMPTS,
        ]);

        $this->postJson('/api/v1/auth/otp/verify', [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Login->value,
            'code' => '123456',
        ])->assertStatus(422)->assertJsonPath('error.code', 'max_attempts_exceeded');
    }

    public function test_resend_is_throttled(): void
    {
        $payload = [
            'identifier' => 'otp@example.com',
            'purpose' => OtpPurpose::Registration->value,
        ];

        $this->postJson('/api/v1/auth/otp/request', $payload)->assertCreated();
        $this->postJson('/api/v1/auth/otp/request', $payload)->assertTooManyRequests();
    }

    public function test_password_can_be_reset_with_otp(): void
    {
        User::query()->create([
            'name' => 'OTP User',
            'email' => 'otp@example.com',
            'password' => Hash::make('old-password'),
        ]);

        $this->postJson('/api/v1/auth/password/forgot', [
            'identifier' => 'otp@example.com',
        ])->assertOk();

        $this->postJson('/api/v1/auth/password/reset-with-otp', [
            'identifier' => 'otp@example.com',
            'code' => $this->messenger->lastCode,
            'password' => 'new-strong-password',
            'password_confirmation' => 'new-strong-password',
        ])->assertOk()->assertJsonPath('data.status', 'password_reset');

        $this->assertTrue(Hash::check('new-strong-password', User::query()->first()?->password));
        $this->assertNotNull(OtpCode::query()->first()?->consumed_at);
    }
}

class FakeOtpMessenger implements OtpMessenger
{
    public ?string $lastIdentifier = null;

    public ?string $lastPurpose = null;

    public ?string $lastCode = null;

    public function send(string $identifier, string $purpose, string $code): void
    {
        $this->lastIdentifier = $identifier;
        $this->lastPurpose = $purpose;
        $this->lastCode = $code;
    }
}
