<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Str;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamToken;

class ExamTokenService
{
    public function issue(ExamParticipant $participant, ?int $length = 8): ExamToken
    {
        return ExamToken::query()->create([
            'tenant_id' => $participant->tenant_id,
            'exam_participant_id' => $participant->id,
            'token' => $this->generateToken($length),
            'is_active' => true,
        ]);
    }

    public function generateToken(?int $length = 8): string
    {
        $length = max(6, min($length ?? 8, 16));

        return strtoupper(Str::random($length));
    }
}
