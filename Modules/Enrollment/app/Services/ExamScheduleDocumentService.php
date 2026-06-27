<?php

namespace Modules\Enrollment\Services;

use App\Support\TypedValue;
use Modules\Enrollment\Models\ExamSchedule;

class ExamScheduleDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(ExamSchedule $examSchedule): array
    {
        $examSchedule->load([
            'admissionPeriod.organization',
            'examResults.applicant',
        ]);

        return [
            'examSchedule' => $examSchedule,
            'admissionPeriod' => $examSchedule->admissionPeriod,
            'showSignature' => false,
        ];
    }

    public function filename(ExamSchedule $examSchedule): string
    {
        $name = str_replace(' ', '_', $examSchedule->name ?? TypedValue::string($examSchedule->getKey()));

        return sprintf('Kartu_Jadwal_Ujian_%s.pdf', $name);
    }
}
