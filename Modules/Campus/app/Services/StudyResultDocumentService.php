<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\StudyResult;

class StudyResultDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(StudyResult $studyResult): array
    {
        $studyResult->load([
            'studyPlanItem.course',
            'studyPlanItem.courseOffering.lecturer.user',
            'studyPlanItem.studyPlan.collageStudent.studyProgram',
            'studyPlanItem.studyPlan.collageStudent.user',
            'studyPlanItem.studyPlan.academicPeriod',
        ]);

        $item = $studyResult->studyPlanItem;
        $studyPlan = $item?->studyPlan;

        return [
            'studyResult' => $studyResult,
            'item' => $item,
            'studyPlan' => $studyPlan,
            'student' => $studyPlan?->collageStudent,
            'showSignature' => true,
            'signatureLabel' => 'Dosen Pengampu',
        ];
    }

    public function filename(StudyResult $studyResult): string
    {
        $courseCode = $studyResult->studyPlanItem?->course?->code ?? 'course';

        return sprintf('StudyResult_%s_%s.pdf', $courseCode, $studyResult->getKey());
    }
}
