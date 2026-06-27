<?php

namespace Modules\Enrollment\Listeners;

use App\Concerns\InteractsWithTenant;
use App\Support\TypedValue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Modules\Core\Support\NotificationService;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\Registration;
use Modules\Finance\Events\StudentInvoicePaid;
use Modules\Monitoring\Services\AutomationRunLogger;

class UpdateRegistrationOnInvoicePaid implements ShouldQueue
{
    use InteractsWithTenant, Queueable;

    public function __construct()
    {
        $this->captureCurrentTenant();
    }

    public function handle(StudentInvoicePaid $event): void
    {
        $invoice = TypedValue::model($event->invoice->fresh(['invoiceable']));

        if (! $invoice->invoiceable instanceof Applicant) {
            return;
        }

        app(AutomationRunLogger::class)->run(
            StudentInvoicePaid::class,
            $invoice,
            fn () => $this->confirmRegistration($invoice->invoiceable, $event),
            'update_registration_payment',
        );
    }

    /**
     * @return array{registration_id:int|null}
     */
    protected function confirmRegistration(Applicant $applicant, StudentInvoicePaid $event): array
    {
        $registration = Registration::query()
            ->where('applicant_id', $applicant->getKey())
            ->first();

        if (! $registration) {
            return ['registration_id' => null];
        }

        if ($registration->payment_status === 'paid') {
            return ['registration_id' => TypedValue::int($registration->getKey())];
        }

        $registration->forceFill([
            'payment_status' => 'paid',
            'paid_amount' => $event->invoice->paid_amount,
            'status' => $registration->status === 'pending' ? 'confirmed' : $registration->status,
            'completed_at' => $registration->completed_at ?? now(),
        ])->save();

        $this->notifyActor($event);

        return ['registration_id' => TypedValue::int($registration->getKey())];
    }

    protected function notifyActor(StudentInvoicePaid $event): void
    {
        if (! $event->actor) {
            return;
        }

        try {
            NotificationService::registrationPaymentConfirmed($event->invoice, $event->actor);
        } catch (\Throwable) {
            // Best-effort notification.
        }
    }
}
