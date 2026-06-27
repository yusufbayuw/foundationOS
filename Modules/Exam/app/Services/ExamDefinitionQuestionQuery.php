<?php

namespace Modules\Exam\Services;

use App\Support\TypedValue;
use Illuminate\Database\Eloquent\Builder;
use Modules\Campus\Models\Course;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\School\Models\Subject;

class ExamDefinitionQuestionQuery
{
    /**
     * @param  array<string, mixed>  $filters
     * @return Builder<ExamQuestion>
     */
    public function forPicker(ExamDefinition $definition, User $user, array $filters = [], bool $includeCrossContext = false): Builder
    {
        $query = ExamQuestion::query()
            ->with(['examQuestionBank'])
            ->where('tenant_id', $definition->tenant_id)
            ->where('status', QuestionStatus::Active);

        if (! $includeCrossContext || ! $user->isGlobalSuperAdmin()) {
            $context = $definition->exam_academic_context;

            $query->whereHas('examQuestionBank', function (Builder $bankQuery) use ($context): void {
                /** @var Builder<ExamQuestionBank> $bankQuery */
                $bankQuery->where('academic_context_type', $context->value);
            });
        }

        if (filled($filters['type'] ?? null)) {
            $query->where('type', TypedValue::string($filters['type']));
        }

        if (filled($filters['difficulty'] ?? null)) {
            $query->where('difficulty', TypedValue::string($filters['difficulty']));
        }

        if (filled($filters['topic'] ?? null)) {
            $query->where('topic', 'like', '%'.TypedValue::string($filters['topic']).'%');
        }

        if (filled($filters['subtopic'] ?? null)) {
            $query->where('subtopic', 'like', '%'.TypedValue::string($filters['subtopic']).'%');
        }

        if (filled($filters['olympiad_level'] ?? null)) {
            $query->whereRaw(
                'JSON_UNQUOTE(JSON_EXTRACT(metadata_json, ?)) = ?',
                ['$.olympiad_level', TypedValue::string($filters['olympiad_level'])],
            );
        }

        if ($definition->exam_academic_context === ExamAcademicContext::School && filled($filters['subject'] ?? null)) {
            $query->whereHas('examQuestionBank.schoolSubject', function (Builder $subjectQuery) use ($filters): void {
                /** @var Builder<Subject> $subjectQuery */
                $subjectQuery->where('name', 'like', '%'.TypedValue::string($filters['subject']).'%');
            });
        }

        if ($definition->exam_academic_context === ExamAcademicContext::Campus && filled($filters['course'] ?? null)) {
            $query->whereHas('examQuestionBank.campusCourse', function (Builder $courseQuery) use ($filters): void {
                /** @var Builder<Course> $courseQuery */
                $courseQuery->where('name', 'like', '%'.TypedValue::string($filters['course']).'%');
            });
        }

        if ($definition->exam_academic_context === ExamAcademicContext::Standalone && filled($filters['subject'] ?? null)) {
            $query->whereHas('examQuestionBank', function (Builder $bankQuery) use ($filters): void {
                /** @var Builder<ExamQuestionBank> $bankQuery */
                $bankQuery->where('standalone_subject', 'like', '%'.TypedValue::string($filters['subject']).'%');
            });
        }

        return $query;
    }

    public function assertQuestionAttachable(ExamDefinition $definition, ExamQuestion $question, User $user, bool $includeCrossContext = false): void
    {
        if ($question->tenant_id !== $definition->tenant_id) {
            throw new \InvalidArgumentException('Question does not belong to this tenant.');
        }

        if ($question->status !== QuestionStatus::Active) {
            throw new \InvalidArgumentException('Only active questions can be added to an exam.');
        }

        if ($includeCrossContext && $user->isGlobalSuperAdmin()) {
            return;
        }

        $bankContext = $question->examQuestionBank?->academic_context_type;

        if ($bankContext !== $definition->exam_academic_context) {
            throw new \InvalidArgumentException('Question academic context does not match the exam.');
        }
    }
}
