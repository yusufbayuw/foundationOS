<?php

namespace Modules\Exam\Services;

use Illuminate\Database\Eloquent\Model;
use Modules\Campus\Models\CollageStudent;
use Modules\Campus\Models\CourseOffering;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\School\Models\SchoolClass;
use Modules\School\Models\Student;
use Modules\School\Models\Subject;

class ExamContextResolver
{
    /**
     * @return array<string, mixed>
     */
    public function resolveDefinitionContext(ExamDefinition $definition): array
    {
        return [
            'exam_academic_context' => $definition->exam_academic_context->value,
            'context_reference_type' => $definition->context_reference_type,
            'context_reference_id' => $definition->context_reference_id,
            'context_reference_uuid' => $definition->context_reference_uuid,
            'metadata_json' => $definition->metadata_json ?? [],
            'resolved_label' => $this->resolveReferenceLabel(
                $definition->context_reference_type,
                $definition->context_reference_id,
            ),
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function resolveParticipantContext(ExamParticipant $participant): array
    {
        return [
            'context_reference_type' => $participant->context_reference_type,
            'context_reference_id' => $participant->context_reference_id,
            'context_reference_uuid' => $participant->context_reference_uuid,
            'metadata_json' => $participant->metadata_json ?? [],
            'resolved_label' => $this->resolveReferenceLabel(
                $participant->context_reference_type,
                $participant->context_reference_id,
            ),
        ];
    }

    public function requiresAcademicPeriod(ExamAcademicContext $context): bool
    {
        return in_array($context, [ExamAcademicContext::School, ExamAcademicContext::Campus], true);
    }

    public function allowsSchoolAssessmentBridge(ExamDefinition $definition): bool
    {
        return $definition->exam_academic_context === ExamAcademicContext::School;
    }

    protected function resolveReferenceLabel(?string $type, ?int $id): ?string
    {
        if ($type === null || $id === null) {
            return null;
        }

        $model = $this->resolveModel($type, $id);

        if ($model === null) {
            return null;
        }

        return match (true) {
            $model instanceof Student => $model->user->name ?? $model->nis ?? (string) $id,
            $model instanceof CollageStudent => $model->full_name ?? (string) $id,
            $model instanceof SchoolClass => $model->name ?? (string) $id,
            $model instanceof Subject => $model->name ?? (string) $id,
            $model instanceof CourseOffering => trim(($model->class_code ?? '').' '.($model->course->name ?? '')),
            default => (string) ($model->getAttribute('name') ?? $id),
        };
    }

    protected function resolveModel(string $type, int $id): ?Model
    {
        $class = class_exists($type) ? $type : $this->mapSlugToClass($type);

        if ($class === null || ! is_subclass_of($class, Model::class)) {
            return null;
        }

        return $class::query()->withoutGlobalScopes()->find($id);
    }

    protected function mapSlugToClass(string $slug): ?string
    {
        return match ($slug) {
            'student', Student::class => Student::class,
            'school_class', 'class', SchoolClass::class => SchoolClass::class,
            'subject', Subject::class => Subject::class,
            'collage_student', CollageStudent::class => CollageStudent::class,
            'course_offering', CourseOffering::class => CourseOffering::class,
            default => null,
        };
    }
}
