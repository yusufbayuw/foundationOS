<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Enrollment\Models\Lead;

class EnrollmentLeadFollowupDueCommand extends Command
{
    protected $signature = 'enrollment:lead-followup-due';

    protected $description = 'Flag leads with overdue follow-up reminders';

    public function handle(): int
    {
        $leads = Lead::withoutTenantScope()
            ->whereNotIn('stage', ['enrolled', 'lost'])
            ->whereNotNull('next_follow_up_at')
            ->where('next_follow_up_at', '<=', now())
            ->get();

        foreach ($leads as $lead) {
            $lead->forceFill([
                'notes' => trim(($lead->notes ?? '')."\n[system] Follow-up due"),
            ])->saveQuietly();
        }

        $this->info('Processed '.$leads->count().' overdue lead follow-ups.');

        return self::SUCCESS;
    }
}
