<?php

namespace Tests\Feature\Security;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class WriteEndpointSecurityConfigurationTest extends TestCase
{
    #[Test]
    public function write_api_endpoints_use_form_requests_policy_authorization_and_idempotency(): void
    {
        $this->assertStringContainsString("Route::middleware(['idempotency', 'throttle:checkout'])->group", file_get_contents(__DIR__.'/../../../routes/api.php'));

        foreach ([
            'StoreApplicantRequest' => 'Applicant::class',
            'StorePaymentRequest' => 'Payment::class',
            'StoreLeaveRequestRequest' => 'LeaveRequest::class',
            'StoreDeviceRequest' => 'Device::class',
        ] as $request => $policyTarget) {
            $contents = file_get_contents(__DIR__."/../../../app/Http/Requests/Api/V1/{$request}.php");

            $this->assertStringContainsString("->can('create', {$policyTarget})", $contents);
        }
    }

    #[Test]
    public function payment_proof_upload_is_private_and_validated(): void
    {
        $request = file_get_contents(__DIR__.'/../../../app/Http/Requests/Api/V1/StorePaymentRequest.php');
        $form = file_get_contents(__DIR__.'/../../../Modules/Finance/app/Filament/Resources/Payments/Schemas/PaymentForm.php');

        $this->assertStringContainsString('mimetypes:application/pdf,image/jpeg,image/png', $request);
        $this->assertStringContainsString('extensions:pdf,jpg,jpeg,png', $request);
        $this->assertStringContainsString('max:5120', $request);
        $this->assertStringContainsString("->visibility('private')", $form);
    }
}
