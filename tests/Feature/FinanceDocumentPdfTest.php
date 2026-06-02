<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Modules\Core\Models\User;
use Modules\Finance\Models\ChartOfAccount;
use Modules\Finance\Models\CustomerInvoice;
use Modules\Finance\Models\CustomerInvoiceItem;
use Modules\Finance\Models\Payment;
use Modules\Finance\Models\StudentInvoice;
use Modules\Finance\Models\StudentInvoiceItem;
use Modules\School\Models\Student as SchoolStudent;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class FinanceDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use LazilyRefreshDatabase;

    public function test_guest_cannot_download_student_invoice_pdf(): void
    {
        [$tenant, $organization] = $this->makePdfTenantContext();
        $student = $this->makeSchoolStudent($tenant, $organization);

        $invoice = $this->makeStudentInvoice($tenant, $student, 'issued', 'INV-001');

        $this->getJson(route('finance.student-invoices.pdf', $invoice))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_issued_student_invoice_pdf(): void
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();
        $student = $this->makeSchoolStudent($tenant, $organization);

        $invoice = $this->makeStudentInvoice($tenant, $student, 'issued', 'INV-002');

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
    }

    public function test_draft_student_invoice_pdf_is_forbidden(): void
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();
        $student = $this->makeSchoolStudent($tenant, $organization);

        $invoice = $this->makeStudentInvoice($tenant, $student, 'draft', 'INV-DRAFT');

        $this->actingAs($user)
            ->get(route('finance.student-invoices.pdf', $invoice))
            ->assertForbidden();
    }

    public function test_super_admin_can_download_verified_payment_pdf(): void
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();
        $student = $this->makeSchoolStudent($tenant, $organization);

        $invoice = $this->makeStudentInvoice($tenant, $student, 'paid', 'INV-PAY', 100000, 100000, 0);

        $account = ChartOfAccount::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => '1101',
            'name' => 'Kas',
            'account_type' => 'asset',
        ]);

        $payment = Payment::create([
            'tenant_id' => $tenant->id,
            'student_invoice_id' => $invoice->id,
            'chart_of_account_id' => $account->id,
            'payment_number' => 'PAY-001',
            'payment_date' => now()->toDateString(),
            'amount' => 100000,
            'payment_method' => 'transfer',
            'status' => 'verified',
            'verified_by' => $user->id,
            'verified_at' => now(),
        ]);

        $this->actingAs($user)
            ->get(route('finance.payments.pdf', $payment))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_customer_invoice_pdf(): void
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();

        $invoice = CustomerInvoice::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'invoice_number' => 'CINV-001',
            'customer_name' => 'PT Klien',
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(14)->toDateString(),
            'subtotal' => 500000,
            'total_amount' => 500000,
            'remaining_amount' => 500000,
            'status' => 'issued',
        ]);

        CustomerInvoiceItem::create([
            'tenant_id' => $tenant->id,
            'customer_invoice_id' => $invoice->id,
            'description' => 'Consulting',
            'quantity' => 1,
            'unit_price' => 500000,
            'line_total' => 500000,
        ]);

        $this->actingAs($user)
            ->get(route('finance.customer-invoices.pdf', $invoice))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    private function makeSchoolStudent($tenant, $organization): SchoolStudent
    {
        $user = User::factory()->create([
            'name' => 'Student PDF',
        ]);

        return SchoolStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'student_number' => 'STD-PDF-001',
        ]);
    }

    private function makeStudentInvoice(
        $tenant,
        SchoolStudent $student,
        string $status,
        string $number,
        float $total = 250000,
        float $paid = 0,
        float $remaining = 250000,
    ): StudentInvoice {
        return StudentInvoice::create([
            'tenant_id' => $tenant->id,
            'invoiceable_type' => 'school_student',
            'invoiceable_id' => $student->id,
            'invoice_number' => $number,
            'issue_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'amount' => $total,
            'total_amount' => $total,
            'paid_amount' => $paid,
            'remaining_amount' => $remaining,
            'status' => $status,
        ]);
    }
}
