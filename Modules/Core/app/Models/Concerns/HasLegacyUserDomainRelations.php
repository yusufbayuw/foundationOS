<?php

namespace Modules\Core\Models\Concerns;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Campus\Models\FeederLog;
use Modules\Campus\Models\Lecturer;
use Modules\Campus\Models\StudyPlan;
use Modules\Employee\Models\AttendanceLog;
use Modules\Employee\Models\Employee;
use Modules\Employee\Models\KpiScore;
use Modules\Employee\Models\LeaveRequest;
use Modules\Enrollment\Models\ExamResult;
use Modules\Enrollment\Models\Registration;
use Modules\Finance\Models\Budget as FinanceBudget;
use Modules\Finance\Models\JournalEntry;
use Modules\Finance\Models\Payment;
use Modules\Library\Models\Loan;
use Modules\Library\Models\Member;
use Modules\Procurement\Models\GoodsReceipt;
use Modules\Procurement\Models\PurchaseOrder;
use Modules\Procurement\Models\PurchaseRequisition;
use Modules\Procurement\Models\RequestForQuotation;
use Modules\Procurement\Models\VendorBill;
use Modules\School\Models\Attendance;
use Modules\School\Models\Student;
use Modules\School\Models\StudentAchievement;
use Modules\School\Models\StudentAssessmentAnswer;
use Modules\School\Models\StudentGrade;
use Modules\School\Models\Teacher;
use Modules\School\Models\Violation;

/**
 * Cross-module Eloquent relations kept for backward compatibility.
 *
 * @deprecated Prefer querying domain models from their owning modules.
 */
trait HasLegacyUserDomainRelations
{
    public function students(): HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function lecturers(): HasMany
    {
        return $this->hasMany(Lecturer::class);
    }

    /**
     * @deprecated Query Modules\Employee\Models\Employee by user_id from the Employee module instead.
     */
    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }

    /**
     * @deprecated Query Modules\Library\Models\Member by user_id from the Library module instead.
     */
    public function libraryMembers(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    /**
     * @deprecated Query Modules\Finance\Models\Payment by verified_by from the Finance module instead.
     */
    public function verifiedPayments(): HasMany
    {
        return $this->hasMany(Payment::class, 'verified_by');
    }

    /**
     * @deprecated Query Modules\Finance\Models\JournalEntry by posted_by from the Finance module instead.
     */
    public function postedJournalEntries(): HasMany
    {
        return $this->hasMany(JournalEntry::class, 'posted_by');
    }

    /**
     * @deprecated Query Modules\Finance\Models\Budget by approved_by from the Finance module instead.
     */
    public function approvedBudgets(): HasMany
    {
        return $this->hasMany(FinanceBudget::class, 'approved_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\PurchaseRequisition by requested_by from the Procurement module instead.
     */
    public function requestedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class, 'requested_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\PurchaseRequisition by user_id from the Procurement module instead.
     */
    public function ownedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class);
    }

    /**
     * @deprecated Query Modules\Procurement\Models\PurchaseRequisition by approved_by from the Procurement module instead.
     */
    public function approvedPurchaseRequisitions(): HasMany
    {
        return $this->hasMany(PurchaseRequisition::class, 'approved_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\RequestForQuotation by created_by from the Procurement module instead.
     */
    public function createdRequestForQuotations(): HasMany
    {
        return $this->hasMany(RequestForQuotation::class, 'created_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\PurchaseOrder by approved_by from the Procurement module instead.
     */
    public function approvedPurchaseOrders(): HasMany
    {
        return $this->hasMany(PurchaseOrder::class, 'approved_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\GoodsReceipt by received_by from the Procurement module instead.
     */
    public function receivedGoodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'received_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\GoodsReceipt by inspected_by from the Procurement module instead.
     */
    public function inspectedGoodsReceipts(): HasMany
    {
        return $this->hasMany(GoodsReceipt::class, 'inspected_by');
    }

    /**
     * @deprecated Query Modules\Procurement\Models\VendorBill by processed_by from the Procurement module instead.
     */
    public function processedVendorBills(): HasMany
    {
        return $this->hasMany(VendorBill::class, 'processed_by');
    }

    /**
     * @deprecated Query Modules\Library\Models\Loan by processed_by from the Library module instead.
     */
    public function processedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'processed_by');
    }

    /**
     * @deprecated Query Modules\Library\Models\Loan by returned_by from the Library module instead.
     */
    public function returnedLoans(): HasMany
    {
        return $this->hasMany(Loan::class, 'returned_by');
    }

    /**
     * @deprecated Query Modules\Employee\Models\AttendanceLog by approved_by from the Employee module instead.
     */
    public function approvedAttendanceLogs(): HasMany
    {
        return $this->hasMany(AttendanceLog::class, 'approved_by');
    }

    /**
     * @deprecated Query Modules\Employee\Models\LeaveRequest by supervisor_id from the Employee module instead.
     */
    public function supervisedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'supervisor_id');
    }

    /**
     * @deprecated Query Modules\Employee\Models\LeaveRequest by approver_id from the Employee module instead.
     */
    public function approvedLeaveRequests(): HasMany
    {
        return $this->hasMany(LeaveRequest::class, 'approver_id');
    }

    /**
     * @deprecated Query Modules\Employee\Models\KpiScore by evaluator_id from the Employee module instead.
     */
    public function evaluatedKpiScores(): HasMany
    {
        return $this->hasMany(KpiScore::class, 'evaluator_id');
    }

    public function verifiedAttendances(): HasMany
    {
        return $this->hasMany(Attendance::class, 'verified_by');
    }

    public function reportedViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'reported_by');
    }

    public function handledViolations(): HasMany
    {
        return $this->hasMany(Violation::class, 'handled_by');
    }

    public function gradedStudentAnswers(): HasMany
    {
        return $this->hasMany(StudentAssessmentAnswer::class, 'graded_by');
    }

    public function gradedStudentGrades(): HasMany
    {
        return $this->hasMany(StudentGrade::class, 'graded_by');
    }

    public function verifiedStudentAchievements(): HasMany
    {
        return $this->hasMany(StudentAchievement::class, 'verified_by');
    }

    /**
     * @deprecated Query Modules\Enrollment\Models\Registration by completed_by from the Enrollment module instead.
     */
    public function completedRegistrations(): HasMany
    {
        return $this->hasMany(Registration::class, 'completed_by');
    }

    /**
     * @deprecated Query Modules\Enrollment\Models\ExamResult by examiner_id from the Enrollment module instead.
     */
    public function examinedExamResults(): HasMany
    {
        return $this->hasMany(ExamResult::class, 'examiner_id');
    }

    public function approvedStudyPlans(): HasMany
    {
        return $this->hasMany(StudyPlan::class, 'approved_by');
    }

    public function syncedFeederLogs(): HasMany
    {
        return $this->hasMany(FeederLog::class, 'synced_by');
    }
}
