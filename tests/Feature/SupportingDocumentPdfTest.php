<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Str;
use Modules\Boarding\Models\BoardingLeavePermit;
use Modules\Consulting\Models\EngagementInvoice;
use Modules\Core\Models\Organization;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Donation\Models\Campaign;
use Modules\Donation\Models\Donation;
use Modules\Donation\Models\Donor;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\EmploymentContract;
use Modules\Employee\Models\LeaveRequest;
use Modules\Event\Models\EventCertificate;
use Modules\Legal\Models\Contract;
use Modules\Library\Models\Book;
use Modules\Library\Models\BookCopy;
use Modules\Library\Models\Fine;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\MerchOrder\Models\MerchOrder;
use Modules\Property\Models\LeaseInvoice;
use Modules\Sales\Models\Customer;
use Modules\Sales\Models\SalesOrder;
use Modules\Training\Models\TrainingBatch;
use Modules\Training\Models\TrainingCertificate;
use Modules\Training\Models\TrainingEnrollment;
use Modules\Training\Models\TrainingProgram;
use Tests\Concerns\InteractsWithPdfDocuments;
use Tests\TestCase;

class SupportingDocumentPdfTest extends TestCase
{
    use InteractsWithPdfDocuments;
    use LazilyRefreshDatabase;

    public function test_guest_cannot_download_supporting_module_pdfs(): void
    {
        [$tenant, $organization, , $records] = $this->seedSupportingPdfRecords();

        $this->getJson(route('employee.leave-requests.pdf', $records['leaveRequest']))
            ->assertUnauthorized();
        $this->getJson(route('library.loans.pdf', $records['loan']))
            ->assertUnauthorized();
        $this->getJson(route('donation.receipts.pdf', $records['donation']))
            ->assertUnauthorized();
    }

    public function test_super_admin_can_download_employee_pdfs(): void
    {
        [, , $user, $records] = $this->seedSupportingPdfRecords();

        $this->actingAs($user)
            ->get(route('employee.leave-requests.pdf', $records['leaveRequest']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('employee.employment-contracts.pdf', $records['employmentContract']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_library_pdfs(): void
    {
        [, , $user, $records] = $this->seedSupportingPdfRecords();

        $this->actingAs($user)
            ->get(route('library.loans.pdf', $records['loan']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('library.fines.pdf', $records['fine']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_boarding_training_and_event_pdfs(): void
    {
        [, , $user, $records] = $this->seedSupportingPdfRecords();

        $this->actingAs($user)
            ->get(route('boarding.leave-permits.pdf', $records['boardingLeavePermit']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('training.certificates.pdf', $records['trainingCertificate']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('event.certificates.pdf', $records['eventCertificate']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_sales_property_and_donation_pdfs(): void
    {
        [, , $user, $records] = $this->seedSupportingPdfRecords();

        $this->actingAs($user)
            ->get(route('sales.orders.pdf', $records['salesOrder']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('property.lease-invoices.pdf', $records['leaseInvoice']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('donation.receipts.pdf', $records['donation']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_super_admin_can_download_legal_merch_and_consulting_pdfs(): void
    {
        [, , $user, $records] = $this->seedSupportingPdfRecords();

        $this->actingAs($user)
            ->get(route('legal.contracts.pdf', $records['contract']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('merch.orders.pdf', $records['merchOrder']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');

        $this->actingAs($user)
            ->get(route('consulting.engagement-invoices.pdf', $records['engagementInvoice']))
            ->assertOk()
            ->assertHeader('content-type', 'application/pdf');
    }

    public function test_non_printable_leave_request_pdf_is_forbidden(): void
    {
        [$tenant, $organization, $user] = array_slice($this->seedSupportingPdfRecords(), 0, 3);

        $employee = Employee::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'employee_number' => 'EMP-DRAFT',
            'full_name' => 'Draft Employee',
            'employment_status' => 'active',
            'join_date' => now()->toDateString(),
        ]);

        $leaveRequest = LeaveRequest::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'total_days' => 3,
            'reason' => 'Pending approval',
            'status' => 'pending',
        ]);

        $this->actingAs($user)
            ->get(route('employee.leave-requests.pdf', $leaveRequest))
            ->assertForbidden();
    }

    /**
     * @return array{0: Tenant, 1: Organization, 2: User, 3: array<string, mixed>}
     */
    private function seedSupportingPdfRecords(): array
    {
        [$tenant, $organization, $user] = $this->makePdfTenantContext();

        $employee = Employee::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'employee_number' => 'EMP-001',
            'full_name' => 'Budi Santoso',
            'employment_status' => 'active',
            'join_date' => now()->toDateString(),
        ]);

        $leaveRequest = LeaveRequest::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'leave_type' => 'annual',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(2)->toDateString(),
            'total_days' => 3,
            'reason' => 'Liburan keluarga',
            'status' => 'approved',
            'approved_at' => now(),
        ]);

        $employmentContract = EmploymentContract::create([
            'tenant_id' => $tenant->id,
            'employee_id' => $employee->id,
            'contract_number' => 'PKWT-001',
            'contract_type' => 'pkwt',
            'start_date' => now()->toDateString(),
            'basic_salary' => 5000000,
            'status' => 'active',
        ]);

        $book = Book::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'title' => 'Algoritma Dasar',
            'authors' => ['Cormen'],
            'total_copies' => 1,
            'available_copies' => 0,
        ]);

        $bookCopy = BookCopy::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'book_id' => $book->id,
            'copy_number' => 'CP-001',
            'status' => 'loaned',
        ]);

        $member = Member::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'member_number' => 'LIB-001',
        ]);

        $loan = Loan::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'book_copy_id' => $bookCopy->id,
            'member_id' => $member->id,
            'processed_by' => $user->id,
            'loan_date' => now()->toDateString(),
            'due_date' => now()->addDays(7)->toDateString(),
            'status' => 'borrowed',
        ]);

        $fine = Fine::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'loan_id' => $loan->id,
            'fine_type' => 'late_return',
            'amount' => 5000,
            'status' => 'issued',
            'issued_at' => now()->toDateString(),
        ]);

        $boardingLeavePermit = BoardingLeavePermit::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'BLP-001',
            'name' => 'Izin Pulang Akhir Pekan',
            'status' => 'approved',
            'description' => 'Izin pulang untuk siswa asrama.',
        ]);

        $trainingProgram = TrainingProgram::create([
            'tenant_id' => $tenant->id,
            'code' => 'TRN-001',
            'name' => 'Leadership Bootcamp',
            'status' => 'active',
        ]);

        $trainingBatch = TrainingBatch::create([
            'tenant_id' => $tenant->id,
            'training_program_id' => $trainingProgram->id,
            'code' => 'BATCH-01',
            'status' => 'completed',
        ]);

        $trainingEnrollment = TrainingEnrollment::create([
            'tenant_id' => $tenant->id,
            'training_batch_id' => $trainingBatch->id,
            'participant_name' => 'Andi Wijaya',
            'participant_email' => 'andi@example.com',
            'status' => 'completed',
        ]);

        $trainingCertificate = TrainingCertificate::create([
            'tenant_id' => $tenant->id,
            'training_enrollment_id' => $trainingEnrollment->id,
            'certificate_number' => 'TC-001',
            'verification_token' => (string) Str::uuid(),
            'issued_at' => now(),
        ]);

        $eventCertificate = EventCertificate::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'EVT-CERT-001',
            'name' => 'Peserta Seminar Nasional',
            'status' => 'active',
        ]);

        $customer = Customer::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'Koperasi Sekolah',
        ]);

        $salesOrder = SalesOrder::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'customer_id' => $customer->id,
            'order_number' => 'SO-001',
            'order_date' => now()->toDateString(),
            'status' => 'confirmed',
            'subtotal' => 100000,
            'tax_amount' => 11000,
            'total_amount' => 111000,
            'confirmed_at' => now(),
        ]);

        $leaseInvoice = LeaseInvoice::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'INV-LEASE-001',
            'name' => 'Tagihan Sewa Ruang A',
            'status' => 'issued',
        ]);

        $campaign = Campaign::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'CAMP-001',
            'name' => 'Beasiswa Anak Juara',
            'status' => 'active',
        ]);

        $donor = Donor::create([
            'tenant_id' => $tenant->id,
            'name' => 'Donatur Baik',
            'email' => 'donor@example.com',
        ]);

        $donation = Donation::create([
            'tenant_id' => $tenant->id,
            'campaign_id' => $campaign->id,
            'donor_id' => $donor->id,
            'donation_number' => 'DON-001',
            'amount' => 250000,
            'payment_status' => 'paid',
            'paid_at' => now(),
        ]);

        $contract = Contract::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'LEG-001',
            'name' => 'Perjanjian Kerja Sama',
            'status' => 'active',
            'effective_date' => now()->toDateString(),
        ]);

        $merchOrder = MerchOrder::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'MER-001',
            'name' => 'Paket Seragam Baru',
            'status' => 'picked_up',
        ]);

        $engagementInvoice = EngagementInvoice::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'CON-INV-001',
            'name' => 'Invoice Engagement Q1',
            'status' => 'issued',
        ]);

        return [
            $tenant,
            $organization,
            $user,
            [
                'leaveRequest' => $leaveRequest,
                'employmentContract' => $employmentContract,
                'loan' => $loan,
                'fine' => $fine,
                'boardingLeavePermit' => $boardingLeavePermit,
                'trainingCertificate' => $trainingCertificate,
                'eventCertificate' => $eventCertificate,
                'salesOrder' => $salesOrder,
                'leaseInvoice' => $leaseInvoice,
                'donation' => $donation,
                'contract' => $contract,
                'merchOrder' => $merchOrder,
                'engagementInvoice' => $engagementInvoice,
            ],
        ];
    }
}
