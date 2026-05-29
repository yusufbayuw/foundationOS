<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamAttempt;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamResult;
use Modules\Exam\Services\ExamManualGradingService;
use Tests\TestCase;

class ExamManualGradingTest extends TestCase
{
    use RefreshDatabase;

    public function test_grade_essay_updates_answer_attempt_and_result(): void
    {
        [$exam, $participant, $question] = $this->seedExamWithEssay();
        $grader = User::factory()->create();

        $attempt = ExamAttempt::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_attempt_id' => (string) Str::uuid(),
            'status' => 'submitted',
            'score' => 0,
        ]);

        $answer = ExamAnswer::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_attempt_id' => $attempt->id,
            'exam_question_id' => $question->id,
            'runtime_answer_id' => (string) Str::uuid(),
            'answer_value' => 'Long essay response.',
            'score' => null,
        ]);

        ExamResult::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'exam_attempt_id' => $attempt->id,
            'runtime_result_id' => (string) Str::uuid(),
            'score' => 0,
            'max_score' => 100,
            'status' => 'final',
        ]);

        $graded = app(ExamManualGradingService::class)->gradeEssay(
            $answer,
            $grader,
            85.5,
            'Well structured.',
            ['clarity' => 4],
        );

        $this->assertTrue(Str::isUuid($graded->id));
        $this->assertSame('85.50', $graded->manual_score);
        $this->assertSame($grader->id, $graded->graded_by);
        $this->assertNotNull($graded->graded_at);
        $this->assertSame('Well structured.', $graded->feedback);

        $attempt->refresh();
        $this->assertSame('85.50', $attempt->score);

        $result = ExamResult::withoutTenantScope()->first();
        $this->assertSame('85.50', $result->score);
        $this->assertSame('85.50', $result->percentage);
    }

    public function test_non_essay_answer_cannot_be_manually_graded(): void
    {
        [$exam, $participant, $question] = $this->seedExamWithEssay(QuestionType::SingleChoice);
        $grader = User::factory()->create();

        $attempt = ExamAttempt::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_participant_id' => $participant->id,
            'runtime_attempt_id' => (string) Str::uuid(),
            'status' => 'submitted',
        ]);

        $answer = ExamAnswer::withoutTenantScope()->create([
            'tenant_id' => $exam->tenant_id,
            'exam_definition_id' => $exam->id,
            'exam_attempt_id' => $attempt->id,
            'exam_question_id' => $question->id,
            'runtime_answer_id' => (string) Str::uuid(),
            'answer_value' => 'A',
        ]);

        $this->expectException(\InvalidArgumentException::class);

        app(ExamManualGradingService::class)->gradeEssay($answer, $grader, 10);
    }

    /**
     * @return array{0: ExamDefinition, 1: ExamParticipant, 2: ExamQuestion}
     */
    protected function seedExamWithEssay(QuestionType $type = QuestionType::Essay): array
    {
        $tenant = $this->createExamTenant();

        $exam = ExamDefinition::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'name' => 'Essay exam',
            'exam_academic_context' => ExamAcademicContext::School,
            'status' => ExamStatus::Published,
            'max_score' => 100,
            'passing_score' => 60,
            'runtime_exam_id' => (string) Str::uuid(),
        ]);

        $participant = ExamParticipant::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_definition_id' => $exam->id,
            'student_name' => 'Budi',
            'student_identifier' => 'S001',
            'participant_source' => ParticipantSource::SchoolStudent,
            'status' => ParticipantStatus::Submitted,
        ]);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::School,
            'name' => 'Bank',
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $bank->id,
            'question_number' => 1,
            'type' => $type,
            'status' => QuestionStatus::Active,
            'topic' => 'Algebra',
            'question_text' => 'Explain.',
            'score' => 100,
        ]);

        return [$exam, $participant, $question];
    }

    protected function createExamTenant(): Tenant
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-b8-'.Str::random(4),
            'name' => 'Exam B8',
            'included_modules' => ['core', 'exam'],
        ]);

        return Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-b8-'.Str::random(4),
            'name' => 'Exam B8 Tenant',
            'subscription_plan_id' => $plan->id,
        ]);
    }
}
