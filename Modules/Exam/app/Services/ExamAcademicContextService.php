<?php

namespace Modules\Exam\Services;

use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Support\ExamRuntimeEntityRef;

class ExamAcademicContextService
{
    /**
     * @return array<string, mixed>
     */
    public function build(ExamDefinition $definition): array
    {
        $definition->loadMissing([
            'tenant',
            'organization',
            'academicYear',
            'campusAcademicYear',
            'schoolClass',
            'schoolSubject',
            'schoolTeacher.user',
            'campusFaculty',
            'campusStudyProgram',
            'campusCourse',
            'campusClass.course',
            'campusLecturer',
        ]);

        $context = $definition->exam_academic_context;

        $payload = [
            'academic_context_type' => $context->value,
            'academic_context_label' => $context->label(),
            'organization_label' => $definition->organization?->name,
            'academic_year_label' => $definition->academicYear?->name
                ?? $definition->campusAcademicYear?->name,
        ];

        return match ($context) {
            ExamAcademicContext::School => array_merge($payload, $this->schoolContext($definition)),
            ExamAcademicContext::Campus => array_merge($payload, $this->campusContext($definition)),
            ExamAcademicContext::Standalone => array_merge($payload, $this->standaloneContext($definition)),
        };
    }

    /**
     * @return array<string, mixed>
     */
    protected function schoolContext(ExamDefinition $definition): array
    {
        $teacher = $definition->schoolTeacher;

        return [
            'school_context' => [
                'school_class_reference' => ExamRuntimeEntityRef::forModel($definition->schoolClass),
                'school_class_label' => $definition->schoolClass?->name,
                'subject_reference' => ExamRuntimeEntityRef::forModel($definition->schoolSubject),
                'subject_label' => $definition->schoolSubject?->name,
                'teacher_reference' => ExamRuntimeEntityRef::forModel($teacher),
                'teacher_name' => $teacher?->user?->name ?? $teacher?->nip,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function campusContext(ExamDefinition $definition): array
    {
        $offering = $definition->campusClass;
        $lecturer = $definition->campusLecturer;
        $classCode = $offering?->class_code ?? '';
        $courseName = $offering?->course?->name ?? '';

        return [
            'campus_context' => [
                'faculty_reference' => ExamRuntimeEntityRef::forModel($definition->campusFaculty),
                'faculty_label' => $definition->campusFaculty?->name,
                'study_program_reference' => ExamRuntimeEntityRef::forModel($definition->campusStudyProgram),
                'study_program_label' => $definition->campusStudyProgram?->name,
                'course_reference' => ExamRuntimeEntityRef::forModel($definition->campusCourse),
                'course_label' => $definition->campusCourse?->name,
                'class_reference' => ExamRuntimeEntityRef::forModel($offering),
                'class_label' => trim($classCode.' '.$courseName),
                'lecturer_reference' => ExamRuntimeEntityRef::forModel($lecturer),
                'lecturer_name' => $lecturer?->full_name,
            ],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function standaloneContext(ExamDefinition $definition): array
    {
        return [
            'standalone_context' => [
                'subject' => $definition->standalone_subject,
                'level' => $definition->standalone_level,
                'target_description' => $definition->target_description,
            ],
        ];
    }
}
