<?php

namespace Tests\Feature;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class TrustedProxyIngressTest extends TestCase
{
    public function test_untrusted_remote_address_cannot_spoof_client_ip_or_https(): void
    {
        Route::middleware('web')->get('/__testing/ingress/peer', function (Request $request) {
            return response()->json([
                'ip' => $request->ip(),
                'secure' => $request->isSecure(),
            ]);
        });

        $this->withServerVariables([
            'REMOTE_ADDR' => '198.51.100.42',
            'HTTP_X_FORWARDED_FOR' => '203.0.113.5',
            'HTTP_X_FORWARDED_PROTO' => 'https',
        ])->getJson('/__testing/ingress/peer')
            ->assertOk()
            ->assertJsonPath('ip', '198.51.100.42')
            ->assertJsonPath('secure', false);
    }

    public function test_default_ingress_settings_do_not_trust_all_proxies(): void
    {
        $this->assertNotContains('*', config('trustedproxy.proxies'));
    }
}
