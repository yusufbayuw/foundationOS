<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use InvalidArgumentException;
use Modules\Core\Models\User;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\StudentInvoiceItem;
use Modules\Monitoring\Models\PrintExportLog;
use Modules\Printing\Models\PrintTemplate;
use Modules\Printing\Services\PrintTemplateResolver;
use Modules\School\Models\Student as SchoolStudent;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class PrintTemplateResolverTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use LazilyRefreshDatabase;

    public function test_resolver_returns_hardcoded_defaults_when_no_db_override(): void
    {
        [$tenant] = $this->makePdfTenantContext();

        $resolved = app(PrintTemplateResolver::class)->resolve('finance_student_invoice', $tenant->id);

        $this->assertSame('finance_student_invoice', $resolved->code);
        $this->assertSame('finance::pdf.student-invoice', $resolved->view);
        $this->assertSame('a4', $resolved->paper);
        $this->assertSame('portrait', $resolved->orientation);
    }

    public function test_resolver_applies_tenant_db_override_from_meta(): void
    {
        [$tenant] = $this->makePdfTenantContext();

        PrintTemplate::create([
            'tenant_id' => $tenant->id,
            'code' => 'finance_student_invoice',
            'name' => 'Custom Student Invoice',
            'status' => 'active',
            'meta' => [
                'view' => 'finance::pdf.customer-invoice',
                'orientation' => 'landscape',
            ],
        ]);

        $resolved = app(PrintTemplateResolver::class)->resolve('finance_student_invoice', $tenant->id);

        $this->assertSame('finance::pdf.customer-invoice', $resolved->view);
        $this->assertSame('landscape', $resolved->orientation);
        $this->assertSame('a4', $resolved->paper);
    }

    public function test_resolver_ignores_inactive_db_override(): void
    {
        [$tenant] = $this->makePdfTenantContext();

        PrintTemplate::create([
            'tenant_id' => $tenant->id,
            'code' => 'finance_student_invoice',
            'name' => 'Inactive Override',
            'status' => 'inactive',
            'meta' => [
                'view' => 'finance::pdf.customer-invoice',
                'orientation' => 'landscape',
            ],
        ]);

        $resolved = app(PrintTemplateResolver::class)->resolve('finance_student_invoice', $tenant->id);

        $this->assertSame('finance::pdf.student-invoice', $resolved->view);
        $this->assertSame('portrait', $resolved->orientation);
    }

    public function test_resolver_throws_for_unknown_template_code(): void
    {
        [$tenant] = $this->makePdfTenantContext();

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Unknown print template code [missing_template].');

        app(PrintTemplateResolver::class)->resolve('missing_template', $tenant->id);
    }

    public function test_student_invoice_pdf_download_logs_print_export(): void
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();
        $student = $this->makeSchoolStudent($tenant, $organization);

        $invoice = StudentInvoice::create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => 'school_student',
            'invoiceable_id' => $student->id,
            'invoice_number' => 'INV-PRINT-001',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'amount' => 250000,
            'total_amount' => 250000,
            'paid_amount' => 0,
            'remaining_amount' => 250000,
            'status' => 'issued',
        ]);

        StudentInvoiceItem::create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'description' => 'SPP',
            'quantity' => 1,
            'unit_price' => 250000,
            'subtotal' => 250000,
        ]);

        $this->actingAs($user)
            ->get(route('finance.student-invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->assertDatabaseHas('print_export_logs', [
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'template_code' => 'finance_student_invoice',
            'subject_type' => $invoice->getMorphClass(),
            'subject_id' => $invoice->id,
            'filename' => 'StudentInvoice_INV-PRINT-001.pdf',
        ]);

        $log = PrintExportLog::query()->first();
        $this->assertNotNull($log?->exported_at);
    }

    private function makeSchoolStudent($tenant, $organization): SchoolStudent
    {
        $studentUser = User::factory()->create([
            'name' => 'Student Print',
        ]);

        return SchoolStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $studentUser->id,
            'student_number' => 'STD-PRINT-001',
        ]);
    }
}
