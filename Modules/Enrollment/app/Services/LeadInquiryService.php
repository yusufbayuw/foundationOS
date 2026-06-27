<?php

namespace Modules\Enrollment\Services;

use App\Support\TypedValue;
use Modules\Enrollment\Models\Applicant;
use Modules\Enrollment\Models\Lead;
use Modules\Enrollment\Models\LeadActivity;
use Modules\Enrollment\Models\LeadSource;

class LeadInquiryService
{
    /**
     * @param  array<string, mixed>  $utm
     */
    public function createFromInquiry(
        int $tenantId,
        string $fullName,
        ?string $email,
        ?string $phone,
        ?string $sourceCode = 'website',
        ?string $sourceDetail = null,
        array $utm = [],
    ): Lead {
        $resolvedSourceCode = TypedValue::string($sourceCode, 'website');

        $source = LeadSource::query()->firstOrCreate(
            ['tenant_id' => $tenantId, 'code' => $resolvedSourceCode],
            ['name' => ucfirst($resolvedSourceCode), 'is_active' => true],
        );

        $lead = Lead::query()->create([
            'tenant_id' => $tenantId,
            'lead_source_id' => $source->getKey(),
            'full_name' => $fullName,
            'email' => $email,
            'phone' => $phone,
            'stage' => 'new',
            'source_detail' => $sourceDetail,
            'utm' => $utm ?: null,
            'next_follow_up_at' => now()->addDay(),
        ]);

        LeadActivity::query()->create([
            'tenant_id' => $tenantId,
            'lead_id' => $lead->getKey(),
            'activity_type' => 'inquiry',
            'notes' => 'Created from public inquiry form',
            'activity_at' => now(),
        ]);

        return $lead;
    }

    public function convertToApplicant(Lead $lead, Applicant $applicant): Lead
    {
        $lead->forceFill([
            'stage' => 'applied',
            'applicant_id' => $applicant->getKey(),
            'converted_at' => now(),
        ])->save();

        $applicant->forceFill(['lead_id' => $lead->getKey()])->save();

        return TypedValue::model($lead->fresh());
    }
}
