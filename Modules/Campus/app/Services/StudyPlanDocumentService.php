<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\StudyPlan;

class StudyPlanDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(StudyPlan $studyPlan): array
    {
        $studyPlan->load([
            'collageStudent.studyProgram',
            'collageStudent.user',
            'academicPeriod',
            'approver',
            'items.course',
            'items.courseOffering',
        ]);

        return [
            'studyPlan' => $studyPlan,
            'student' => $studyPlan->collageStudent,
            'showSignature' => true,
            'signatureLabel' => 'Dosen Wali / Kaprodi',
        ];
    }

    public function filename(StudyPlan $studyPlan): string
    {
        return sprintf('KRS_%s.pdf', str_replace(' ', '_', $studyPlan->plan_number ?? (string) $studyPlan->getKey()));
    }
}
