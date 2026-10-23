<?php

namespace Tests\Feature\Security;

use App\Models\User as AppUser;
use Modules\Core\Models\User as CoreUser;
use Tests\TestCase;

class MfaSecretSerializationTest extends TestCase
{
    public function test_core_and_api_user_models_do_not_serialize_mfa_secrets(): void
    {
        foreach ([new CoreUser, new AppUser] as $user) {
            $user->forceFill([
                'name' => 'MFA User',
                'email' => 'mfa@example.com',
                'password' => 'hashed-password-secret',
                'remember_token' => 'remember-me-secret',
                'app_authentication_secret' => 'top-secret-totp-seed',
                'app_authentication_recovery_codes' => 'recovery-codes-secret',
            ]);

            $data = $user->toArray();
            $json = $user->toJson();

            $this->assertSame('MFA User', $data['name']);

            foreach ([
                'password',
                'remember_token',
                'app_authentication_secret',
                'app_authentication_recovery_codes',
            ] as $field) {
                $this->assertArrayNotHasKey($field, $data);
                $this->assertStringNotContainsString($field, $json);
            }

            $this->assertStringNotContainsString('top-secret-totp-seed', $json);
            $this->assertStringNotContainsString('recovery-codes-secret', $json);
        }
    }
}
