<?php

namespace Modules\Campus\Services;

use Modules\Campus\Models\Thesis;

class ThesisDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(Thesis $thesis): array
    {
        $thesis->load([
            'collageStudent.studyProgram.faculty',
            'collageStudent.user',
            'advisorLecturer.user',
            'examinerLecturer.user',
        ]);

        return [
            'thesis' => $thesis,
            'student' => $thesis->collageStudent,
            'showSignature' => true,
            'signatureLabel' => 'Pembimbing',
            'stampLabel' => 'Ketua Program Studi',
        ];
    }

    public function filename(Thesis $thesis): string
    {
        $npm = $thesis->collageStudent->student_number ?? (string) $thesis->getKey();

        return sprintf('ThesisLetter_%s.pdf', str_replace(' ', '_', $npm));
    }
}
