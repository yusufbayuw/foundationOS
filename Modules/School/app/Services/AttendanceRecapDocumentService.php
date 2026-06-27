<?php

namespace Modules\School\Services;

use App\Support\TypedValue;
use Modules\Core\Models\AcademicPeriod;
use Modules\School\Models\SchoolClass;

class AttendanceRecapDocumentService
{
    public function __construct(private readonly AttendanceRecapService $recapService) {}

    /**
     * @return array<string, mixed>
     */
    public function assemble(
        SchoolClass $schoolClass,
        int $academicPeriodId,
        int $month,
        int $year,
    ): array {
        $period = AcademicPeriod::query()->findOrFail($academicPeriodId);
        $schoolClass->loadMissing('academicPeriod');

        $recap = $this->recapService->getStudentRecap(
            (int) $schoolClass->tenant_id,
            $academicPeriodId,
            TypedValue::int($schoolClass->getKey()),
            $month,
            $year,
        );

        return [
            'schoolClass' => $schoolClass,
            'period' => $period,
            'month' => $month,
            'year' => $year,
            'rows' => $recap,
            'showSignature' => true,
            'signatureLabel' => 'Wali Kelas',
        ];
    }

    public function filename(SchoolClass $schoolClass, int $month, int $year): string
    {
        return sprintf(
            'Rekap_Absensi_%s_%02d_%d.pdf',
            str_replace(' ', '_', $schoolClass->name ?? 'Kelas'),
            $month,
            $year,
        );
    }
}
