<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Str;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamToken;

class ExamTokenService
{
    public function generateToken(ExamParticipant $participant, ?int $length = 8): ExamToken
    {
        $participant->examTokens()->where('is_active', true)->update(['is_active' => false]);

        $exam = $participant->examDefinition ?? ExamDefinition::query()->find($participant->exam_definition_id);

        do {
            $tokenValue = $this->generateTokenValue($length);
        } while ($exam !== null && ! $this->isUniqueForExam($exam, $tokenValue, $participant));

        return ExamToken::query()->create([
            'tenant_id' => $participant->tenant_id,
            'exam_participant_id' => $participant->id,
            'token' => $tokenValue,
            'is_active' => true,
        ]);
    }

    public function regenerate(ExamParticipant $participant, ?int $length = 8): ExamToken
    {
        return $this->generateToken($participant, $length);
    }

    public function issue(ExamParticipant $participant, ?int $length = 8): ExamToken
    {
        $existing = $participant->activeToken;

        if ($existing !== null) {
            return $existing;
        }

        return $this->generateToken($participant, $length);
    }

    public function isUniqueForExam(ExamDefinition $exam, string $token, ?ExamParticipant $excludeParticipant = null): bool
    {
        $query = ExamToken::query()
            ->where('token', $token)
            ->whereHas('examParticipant', fn ($q) => $q->where('exam_definition_id', $exam->id));

        if ($excludeParticipant !== null) {
            $query->where('exam_participant_id', '!=', $excludeParticipant->id);
        }

        return ! $query->exists();
    }

    public function generateTokenValue(?int $length = 8): string
    {
        $length = max(6, min($length ?? 8, 16));

        return strtoupper(Str::random($length));
    }
}
