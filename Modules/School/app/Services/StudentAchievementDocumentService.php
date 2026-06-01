<?php

namespace Modules\School\Services;

use Modules\School\Models\StudentAchievement;

class StudentAchievementDocumentService
{
    /**
     * @return array<string, mixed>
     */
    public function assemble(StudentAchievement $achievement): array
    {
        $achievement->load(['student.user', 'achievementType', 'academicYear', 'organization']);

        return [
            'achievement' => $achievement,
            'showSignature' => true,
            'signatureLabel' => 'Kepala Sekolah',
        ];
    }

    public function filename(StudentAchievement $achievement): string
    {
        $title = str_replace(' ', '_', $achievement->title ?? 'Sertifikat');

        return sprintf('Sertifikat_%s.pdf', $title);
    }
}
