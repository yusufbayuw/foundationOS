<?php

namespace Modules\Campus\Services;

use Illuminate\Support\Collection;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\StudyPlanItem;

class TranscriptDocumentService
{
    public function __construct(protected CampusGpaCalculator $gpaCalculator) {}

    /**
     * @return array<string, mixed>
     */
    public function assemble(CollageStudent $student): array
    {
        $student->load(['studyProgram.faculty', 'user', 'academicAdvisor.user']);

        $rows = $this->publishedResultRows($student);

        $gpa = $student->gpa_cached;
        if ($gpa === null && $rows->isNotEmpty()) {
            $gpa = $this->gpaCalculator->recalculateForStudent($student->getKey());
            $student->refresh();
            $gpa = $student->gpa_cached ?? $gpa;
        }

        return [
            'student' => $student,
            'rows' => $rows,
            'gpa' => $gpa,
            'totalCredits' => $rows->sum(fn (StudyPlanItem $item): int => (int) ($item->credits ?? 0)),
            'showSignature' => true,
            'signatureLabel' => 'Ketua Program Studi',
        ];
    }

    public function filename(CollageStudent $student): string
    {
        return sprintf('Transcript_%s.pdf', str_replace(' ', '_', $student->student_number ?? (string) $student->getKey()));
    }

    /**
     * @return Collection<int, StudyPlanItem>
     */
    protected function publishedResultRows(CollageStudent $student): Collection
    {
        return StudyPlanItem::query()
            ->where('tenant_id', $student->tenant_id)
            ->whereHas('studyPlan', fn ($query) => $query->where('collage_student_id', $student->getKey()))
            ->whereHas('studyResult', fn ($query) => $query->whereNotNull('published_at'))
            ->with([
                'course',
                'studyResult',
                'studyPlan.academicPeriod',
            ])
            ->get()
            ->sortBy([
                fn (StudyPlanItem $item) => $item->studyPlan->academicPeriod->start_date ?? '',
                fn (StudyPlanItem $item) => $item->course->code ?? '',
            ])
            ->values();
    }
}
