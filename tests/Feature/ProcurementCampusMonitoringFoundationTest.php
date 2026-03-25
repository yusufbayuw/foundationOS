<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\Course;
use Modules\Campus\Models\CourseOffering;
use Modules\Campus\Models\Faculty;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Campus\Models\StudyPlanItem;
use Modules\Campus\Models\StudyProgram;
use Modules\Core\Models\AcademicPeriod;
use Modules\Core\Models\AcademicYear;
use Modules\Core\Models\Organization;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Enrollment\Models\AdmissionPeriod;
use Modules\Enrollment\Models\Applicant;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Models\AuditLog;
use Modules\Monitoring\Models\FileUpload;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\GoodsReceiptItem;
use Modules\Procurement\Models\ProcurementCategory;
use Modules\Procurement\Models\ProcurementItem;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseOrderItem;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\PurchaseRequisitionItem;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\Vendor;
use Modules\Procurement\Models\VendorBill;
use Modules\Procurement\Models\VendorBillItem;
use Modules\School\Models\Student as SchoolStudent;
use Tests\TestCase;

class ProcurementCampusMonitoringFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_procurement_entities_follow_tenant_relations(): void
    {
        [$tenant, $organization, $user, $period] = $this->makeAcademicTenantContext();

        $vendor = Vendor::create([
            'tenant_id' => $tenant->id,
            'code' => 'V-001',
            'name' => 'PT Vendor Prima',
        ]);

        $category = ProcurementCategory::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'OFFICE',
            'name' => 'Office Supplies',
        ]);

        $item = ProcurementItem::create([
            'tenant_id' => $tenant->id,
            'category_id' => $category->id,
            'preferred_vendor_id' => $vendor->id,
            'code' => 'ITEM-001',
            'name' => 'Printer Paper',
            'estimated_price' => 75000,
        ]);

        $requisition = PurchaseRequisition::create([
            'tenant_id' => $tenant->id,
            'user_id' => $user->id,
            'requested_by' => $user->id,
            'request_number' => 'PR-001',
            'request_date' => '2026-08-01',
        ]);

        $requisitionItem = PurchaseRequisitionItem::create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $requisition->id,
            'procurement_item_id' => $item->id,
            'preferred_vendor_id' => $vendor->id,
            'quantity_requested' => 10,
            'estimated_unit_price' => 75000,
            'estimated_total_price' => 750000,
        ]);

        $rfq = RequestForQuotation::create([
            'tenant_id' => $tenant->id,
            'purchase_requisition_id' => $requisition->id,
            'created_by' => $user->id,
            'rfq_number' => 'RFQ-001',
            'rfq_date' => '2026-08-02',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'tenant_id' => $tenant->id,
            'request_for_quotation_id' => $rfq->id,
            'vendor_id' => $vendor->id,
            'approved_by' => $user->id,
            'po_number' => 'PO-001',
            'po_date' => '2026-08-03',
            'total_amount' => 750000,
        ]);

        $purchaseOrderItem = PurchaseOrderItem::create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $purchaseOrder->id,
            'purchase_requisition_item_id' => $requisitionItem->id,
            'procurement_item_id' => $item->id,
            'quantity' => 10,
            'unit_price' => 75000,
            'line_total' => 750000,
        ]);

        $receipt = GoodsReceipt::create([
            'tenant_id' => $tenant->id,
            'purchase_order_id' => $purchaseOrder->id,
            'received_by' => $user->id,
            'receipt_number' => 'GR-001',
            'receipt_date' => '2026-08-04',
        ]);

        $receiptItem = GoodsReceiptItem::create([
            'tenant_id' => $tenant->id,
            'goods_receipt_id' => $receipt->id,
            'purchase_order_item_id' => $purchaseOrderItem->id,
            'quantity_received' => 10,
            'quantity_accepted' => 10,
            'unit_price' => 75000,
            'total_amount' => 750000,
        ]);

        $bill = VendorBill::create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'purchase_order_id' => $purchaseOrder->id,
            'goods_receipt_id' => $receipt->id,
            'processed_by' => $user->id,
            'bill_number' => 'BILL-001',
            'bill_date' => '2026-08-05',
            'total_amount' => 750000,
        ]);

        $billItem = VendorBillItem::create([
            'tenant_id' => $tenant->id,
            'vendor_bill_id' => $bill->id,
            'purchase_order_item_id' => $purchaseOrderItem->id,
            'goods_receipt_item_id' => $receiptItem->id,
            'quantity' => 10,
            'unit_price' => 75000,
            'line_total' => 750000,
        ]);

        $this->assertSame($tenant->id, $item->tenant->id);
        $this->assertSame($category->id, $item->category->id);
        $this->assertSame($vendor->id, $item->preferredVendor->id);
        $this->assertSame($vendor->id, $purchaseOrder->vendor->id);
        $this->assertSame($purchaseOrder->id, $receipt->purchaseOrder->id);
        $this->assertSame($receiptItem->id, $billItem->goodsReceiptItem->id);
        $this->assertSame($item->id, $purchaseOrderItem->procurementItem->id);
        $this->assertSame($purchaseOrder->id, $tenant->purchaseOrders->first()->id);
        $this->assertSame($period->id, $period->id);
    }

    public function test_campus_entities_link_students_courses_and_study_plans(): void
    {
        [$tenant, $organization, $user, $period] = $this->makeAcademicTenantContext();

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'FT',
            'name' => 'Faculty of Technology',
        ]);

        $studyProgram = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'faculty_id' => $faculty->id,
            'code' => 'SI',
            'name' => 'Information Systems',
            'total_credits_required' => 144,
        ]);

        $lecturer = Lecturer::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'study_program_id' => $studyProgram->id,
            'nidn' => '00112233',
        ]);

        $studyProgram->update([
            'head_of_program_id' => $lecturer->id,
        ]);

        $student = CollageStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'study_program_id' => $studyProgram->id,
            'academic_advisor_id' => $lecturer->id,
            'student_number' => '2026001',
        ]);

        $course = Course::create([
            'tenant_id' => $tenant->id,
            'study_program_id' => $studyProgram->id,
            'code' => 'IF101',
            'name' => 'Algorithms',
            'credits' => 3,
        ]);

        $offering = CourseOffering::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'course_id' => $course->id,
            'academic_period_id' => $period->id,
            'lecturer_id' => $lecturer->id,
            'class_code' => 'A',
            'capacity' => 30,
        ]);

        $studyPlan = StudyPlan::create([
            'tenant_id' => $tenant->id,
            'collage_student_id' => $student->id,
            'academic_period_id' => $period->id,
            'approved_by' => $user->id,
            'plan_number' => 'KRS-001',
            'total_credits' => 3,
        ]);

        $item = StudyPlanItem::create([
            'tenant_id' => $tenant->id,
            'study_plan_id' => $studyPlan->id,
            'course_offering_id' => $offering->id,
            'course_id' => $course->id,
            'credits' => 3,
        ]);

        $this->assertSame($faculty->id, $studyProgram->faculty->id);
        $this->assertSame($studyProgram->id, $course->studyProgram->id);
        $this->assertSame($lecturer->id, $student->academicAdvisor->id);
        $this->assertSame($course->id, $offering->course->id);
        $this->assertSame($offering->id, $item->courseOffering->id);
        $this->assertSame($lecturer->id, $studyProgram->headOfProgram->id);
    }

    public function test_invoiceable_polymorphic_relations_cover_student_applicant_and_collage_student(): void
    {
        [$tenant, $organization, $user] = $this->makeAcademicTenantContext();

        $schoolStudent = SchoolStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'nis' => 'SCH-001',
        ]);

        $admissionPeriod = AdmissionPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => 'PPDB 2026',
            'code' => 'PPDB-26',
            'start_date' => '2026-01-01',
            'end_date' => '2026-03-31',
        ]);

        $applicant = Applicant::create([
            'tenant_id' => $tenant->id,
            'admission_period_id' => $admissionPeriod->id,
            'registration_number' => 'REG-001',
            'full_name' => 'Calon Siswa',
        ]);

        $faculty = Faculty::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'code' => 'FEB',
            'name' => 'Faculty of Business',
        ]);

        $studyProgram = StudyProgram::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'faculty_id' => $faculty->id,
            'code' => 'AK',
            'name' => 'Accounting',
        ]);

        $collageStudent = CollageStudent::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'study_program_id' => $studyProgram->id,
            'student_number' => 'C-001',
        ]);

        $studentInvoice = $schoolStudent->studentInvoices()->create([
            'tenant_id' => $tenant->id,
            'invoice_number' => 'INV-STUDENT',
            'issue_date' => '2026-07-01',
            'due_date' => '2026-07-10',
            'amount' => 100000,
            'total_amount' => 100000,
            'remaining_amount' => 100000,
        ]);

        $applicantInvoice = $applicant->studentInvoices()->create([
            'tenant_id' => $tenant->id,
            'invoice_number' => 'INV-APPLICANT',
            'issue_date' => '2026-02-01',
            'due_date' => '2026-02-10',
            'amount' => 150000,
            'total_amount' => 150000,
            'remaining_amount' => 150000,
        ]);

        $campusInvoice = $collageStudent->studentInvoices()->create([
            'tenant_id' => $tenant->id,
            'invoice_number' => 'INV-CAMPUS',
            'issue_date' => '2026-08-01',
            'due_date' => '2026-08-10',
            'amount' => 500000,
            'total_amount' => 500000,
            'remaining_amount' => 500000,
        ]);

        $this->assertTrue($studentInvoice->invoiceable->is($schoolStudent));
        $this->assertTrue($applicantInvoice->invoiceable->is($applicant));
        $this->assertTrue($campusInvoice->invoiceable->is($collageStudent));
        $this->assertCount(1, $schoolStudent->studentInvoices);
        $this->assertCount(1, $applicant->studentInvoices);
        $this->assertCount(1, $collageStudent->studentInvoices);
        $this->assertSame(3, StudentInvoice::count());
    }

    public function test_monitoring_entities_capture_tenant_activity(): void
    {
        [$tenant, $organization, $user] = $this->makeAcademicTenantContext();

        $vendor = Vendor::create([
            'tenant_id' => $tenant->id,
            'code' => 'V-LOG',
            'name' => 'Vendor Audit',
        ]);

        $purchaseOrder = PurchaseOrder::create([
            'tenant_id' => $tenant->id,
            'vendor_id' => $vendor->id,
            'approved_by' => $user->id,
            'po_number' => 'PO-LOG',
            'po_date' => '2026-09-01',
            'total_amount' => 200000,
        ]);

        $auditLog = $purchaseOrder->auditLogs()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'user_id' => $user->id,
            'action' => 'purchase_order.approved',
            'new_values' => ['status' => 'approved'],
        ]);

        $upload = $purchaseOrder->fileUploads()->create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'uploaded_by' => $user->id,
            'collection_name' => 'attachments',
            'original_name' => 'invoice.pdf',
            'stored_name' => 'invoice-001.pdf',
            'file_size' => 2048,
            'metadata' => ['ext' => 'pdf'],
        ]);

        $this->assertSame($tenant->id, $auditLog->tenant->id);
        $this->assertSame($user->id, $auditLog->user->id);
        $this->assertTrue($auditLog->auditable->is($purchaseOrder));
        $this->assertCount(1, $purchaseOrder->auditLogs);
        $this->assertCount(1, $tenant->auditLogs);
        $this->assertCount(1, $organization->auditLogs);
        $this->assertSame($tenant->id, $upload->tenant->id);
        $this->assertSame($user->id, $upload->uploader->id);
        $this->assertTrue($upload->fileable->is($purchaseOrder));
        $this->assertCount(1, $purchaseOrder->fileUploads);
        $this->assertCount(1, $tenant->fileUploads);
        $this->assertCount(1, $organization->fileUploads);
    }

    private function makeAcademicTenantContext(): array
    {
        $plan = SubscriptionPlan::create([
            'code' => 'suite',
            'name' => 'Suite',
            'included_modules' => ['core', 'school', 'campus', 'procurement', 'monitoring'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'universitas-prima',
            'name' => 'Universitas Prima',
            'subscription_plan_id' => $plan->id,
        ]);

        $organization = Organization::create([
            'tenant_id' => $tenant->id,
            'code' => 'utama',
            'name' => 'Kampus Utama',
        ]);

        $user = User::create([
            'name' => 'Admin Kampus',
            'username' => 'admin-kampus',
            'email' => 'admin@example.com',
            'password' => 'secret',
        ]);

        $academicYear = AcademicYear::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'name' => '2026/2027',
            'code' => 'AY-26',
            'start_date' => '2026-07-01',
            'end_date' => '2027-06-30',
        ]);

        $period = AcademicPeriod::create([
            'tenant_id' => $tenant->id,
            'organization_id' => $organization->id,
            'academic_year_id' => $academicYear->id,
            'name' => 'Semester Ganjil',
            'code' => '2026-GANJIL',
            'start_date' => '2026-07-01',
            'end_date' => '2026-12-31',
        ]);

        return [$tenant, $organization, $user, $period];
    }
}
