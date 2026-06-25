<?php

namespace Tests\Feature\Characterization;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Printing\Services\PrintTemplateRegistrationService;
use Modules\Printing\Services\PrintTemplateResolver;
use Tests\Concerns\CreatesTenantForTests;
use Tests\TestCase;

/**
 * Characterization tests for Printing module template registry behavior.
 */
class PrintingModuleCharacterizationTest extends TestCase
{
    use CreatesTenantForTests;
    use LazilyRefreshDatabase;

    public function test_registered_template_codes_snapshot(): void
    {
        $codes = app(PrintTemplateResolver::class)->registeredCodes();

        $this->assertSame([
            'finance_student_invoice',
            'finance_customer_invoice',
            'finance_payment_receipt',
            'campus_study_plan',
            'campus_study_result',
            'campus_thesis_letter',
            'campus_transcript',
            'campus_wisuda',
            'campus_yudisium',
            'procurement_vendor_bill',
            'procurement_goods_receipt',
            'procurement_request_for_quotation',
            'procurement_purchase_requisition',
            'procurement_purchase_order',
            'school_report_card',
            'school_attendance_recap',
            'school_student_achievement',
            'school_grade_ledger',
        ], $codes);
    }

    public function test_tenant_wide_and_org_scoped_template_codes_can_coexist(): void
    {
        ['tenant' => $tenant, 'organization' => $organization] = $this->makeTenantContext(['core', 'printing']);

        $service = app(PrintTemplateRegistrationService::class);

        $tenantWide = $service->register($tenant->id, null, 'CUSTOM-A', 'Tenant Template');
        $orgScoped = $service->register($tenant->id, $organization->id, 'CUSTOM-A', 'Org Template');

        $this->assertSame('CUSTOM-A', $tenantWide->code);
        $this->assertSame('CUSTOM-A', $orgScoped->code);
        $this->assertNull($tenantWide->organization_id);
        $this->assertSame($organization->id, $orgScoped->organization_id);
    }
}
