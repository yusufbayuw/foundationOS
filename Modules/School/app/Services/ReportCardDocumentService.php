<?php

namespace Modules\School\Services;

use App\Support\TypedValue;
use Modules\School\Models\Student;

class ReportCardDocumentService
{
    public function __construct(private readonly ReportCardService $reportCardService) {}

    /**
     * @return array<string, mixed>
     */
    public function assemble(Student $student, int $academicPeriodId): array
    {
        $data = $this->reportCardService->generate(TypedValue::int($student->getKey()), $academicPeriodId);

        return [
            'data' => $data,
            'showSignature' => true,
            'signatureLabel' => 'Wali Kelas',
        ];
    }

    public function filename(Student $student, int $academicPeriodId): string
    {
        $data = $this->reportCardService->generate(TypedValue::int($student->getKey()), $academicPeriodId);
        $studentName = TypedValue::string(data_get($data, 'student.user.name'), 'Student');

        return sprintf('Rapor_%s.pdf', str_replace(' ', '_', $studentName));
    }
}
