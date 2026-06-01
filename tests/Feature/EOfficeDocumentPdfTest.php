<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\EOffice\Models\Letter;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class EOfficeDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use RefreshDatabase;

    public function test_guest_cannot_download_letter_pdf(): void
    {
        $letter = $this->makePrintableLetter();

        $this->getJson(route('eoffice.letters.pdf', $letter))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_letter_pdf(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $letter = $this->makePrintableLetter();

        $this->actingAs($user)
            ->get(route('eoffice.letters.pdf', $letter))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_inactive_letter_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $letter = $this->makePrintableLetter(status: 'inactive');

        $this->actingAs($user)
            ->get(route('eoffice.letters.pdf', $letter))
            ->assertForbidden();
    }

    public function test_letter_without_number_pdf_is_forbidden(): void
    {
        [, , $user] = $this->makePdfTenantContext();
        $letter = $this->makePrintableLetter(letterNumber: null, code: null);

        $this->actingAs($user)
            ->get(route('eoffice.letters.pdf', $letter))
            ->assertForbidden();
    }

    private function makePrintableLetter(
        string $status = 'active',
        ?string $letterNumber = '0001/OUT/05/2026',
        ?string $code = 'LTR-001',
    ): Letter {
        $plan = SubscriptionPlan::create([
            'code' => 'eoffice-pdf',
            'name' => 'EOffice PDF',
            'included_modules' => ['core', 'eoffice'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'eoffice-pdf-tenant',
            'name' => 'EOffice PDF Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'main',
            'name' => 'Main Organization',
            'is_main' => true,
        ]);

        return Letter::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => $code,
            'name' => 'Surat Keputusan',
            'letter_number' => $letterNumber,
            'direction' => 'OUT',
            'verification_token' => (string) Str::uuid(),
            'status' => $status,
            'description' => 'Isi surat resmi untuk keperluan verifikasi PDF.',
        ]);
    }
}
