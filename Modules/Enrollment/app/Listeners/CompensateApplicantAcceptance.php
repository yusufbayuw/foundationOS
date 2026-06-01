<?php

namespace Modules\Enrollment\Listeners;

use App\Concerns\InteractsWithTenant;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Enrollment\Events\ApplicantAcceptanceReverted;
use Modules\Enrollment\Models\Applicant;
use Modules\Finance\Models\StudentInvoice;
use Modules\Monitoring\Services\AutomationRunLogger;

class CompensateApplicantAcceptance implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct()
    {
        $this->captureCurrentTenant();
    }

    public function handle(ApplicantAcceptanceReverted $event): void
    {
        app(AutomationRunLogger::class)->run(
            ApplicantAcceptanceReverted::class,
            $event->applicant,
            fn () => $this->voidOpenInvoices($event->applicant),
            'compensate_acceptance',
            payload: ['previous_status' => $event->previousStatus],
        );
    }

    /**
     * @return array{voided:int}
     */
    protected function voidOpenInvoices(Applicant $applicant): array
    {
        $voided = StudentInvoice::query()
            ->where('tenant_id', $applicant->tenant_id)
            ->where('invoiceable_type', $applicant->getMorphClass())
            ->where('invoiceable_id', $applicant->getKey())
            ->whereIn('status', ['draft', 'issued', 'partial'])
            ->update(['status' => 'void']);

        return ['voided' => $voided];
    }
}
