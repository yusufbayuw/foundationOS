<?php

namespace Modules\Exam\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Core\Models\Tenant;
use Modules\Core\Models\User;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\ExamStatus;
use Modules\Exam\Enums\ExamType;
use Modules\Exam\Enums\ParticipantSource;
use Modules\Exam\Enums\ParticipantStatus;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamDefinitionQuestion;
use Modules\Exam\Models\ExamParticipant;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;
use Modules\Exam\Services\ExamShieldProvisioner;
use Modules\Exam\Services\ExamTokenService;

class ExamMvpDemoSeeder extends Seeder
{
    public const DEMO_TENANT_CODE = 'FOUNDATION-DEMO';

    public const BANK_SCHOOL_UUID = 'a1000001-0001-4001-8001-000000000001';

    public const BANK_CAMPUS_UUID = 'a1000001-0001-4001-8001-000000000002';

    public const BANK_STANDALONE_UUID = 'a1000001-0001-4001-8001-000000000003';

    public const EXAM_SCHOOL_UUID = 'b2000002-0002-4002-8002-000000000001';

    public const EXAM_CAMPUS_UUID = 'b2000002-0002-4002-8002-000000000002';

    public const EXAM_STANDALONE_UUID = 'b2000002-0002-4002-8002-000000000003';

    public function run(): void
    {
        $tenant = Tenant::query()->where('code', self::DEMO_TENANT_CODE)->first();

        if ($tenant === null) {
            $this->command->warn('Exam MVP demo skipped: tenant '.self::DEMO_TENANT_CODE.' not found. Run MvpDemoSeeder first.');

            return;
        }

        app(ExamShieldProvisioner::class)->provisionForTenant($tenant);

        $admin = User::query()->where('email', 'admin@admin.com')->first();

        $schoolBank = $this->seedBank(
            self::BANK_SCHOOL_UUID,
            $tenant->id,
            ExamAcademicContext::School,
            'Demo School Question Bank',
            'SCH-BANK',
        );

        $campusBank = $this->seedBank(
            self::BANK_CAMPUS_UUID,
            $tenant->id,
            ExamAcademicContext::Campus,
            'Demo Campus Question Bank',
            'CAM-BANK',
        );

        $standaloneBank = $this->seedBank(
            self::BANK_STANDALONE_UUID,
            $tenant->id,
            ExamAcademicContext::Standalone,
            'Demo OSN / Standalone Bank',
            'OSN-BANK',
            standaloneSubject: 'Physics',
            standaloneLevel: 'OSN Regional',
        );

        $schoolQuestion = $this->seedQuestion($schoolBank, 1, 'School: What is 3 × 4?', 12);
        $campusQuestion = $this->seedQuestion($campusBank, 1, 'Campus: Define algorithm.', 15);
        $standaloneQuestion = $this->seedQuestion($standaloneBank, 1, 'OSN: A ball is dropped from 20 m. Find v after 2 s (g=10).', 20);

        $schoolExam = $this->seedExam(
            self::EXAM_SCHOOL_UUID,
            $tenant->id,
            ExamAcademicContext::School,
            'Demo School Midterm',
            'SCH-MID',
            ExamType::Exam,
            $schoolQuestion,
            $admin,
        );

        $campusExam = $this->seedExam(
            self::EXAM_CAMPUS_UUID,
            $tenant->id,
            ExamAcademicContext::Campus,
            'Demo Campus Quiz',
            'CAM-QUIZ',
            ExamType::Quiz,
            $campusQuestion,
            $admin,
        );

        $standaloneExam = $this->seedExam(
            self::EXAM_STANDALONE_UUID,
            $tenant->id,
            ExamAcademicContext::Standalone,
            'Demo OSN Tryout',
            'OSN-TRY',
            ExamType::OsnPrep,
            $standaloneQuestion,
            $admin,
            standaloneSubject: 'Physics',
            standaloneLevel: 'OSN Regional',
        );

        $this->seedParticipant($schoolExam, 'Budi Santoso', 'SCH-001', $admin);
        $this->seedParticipant($schoolExam, 'Siti Aminah', 'SCH-002', $admin);
        $this->seedParticipant($campusExam, 'Andi Pratama', 'NIM-24001', $admin);
        $this->seedParticipant($standaloneExam, 'OSN Candidate 1', 'OSN-001', $admin);
        $this->seedParticipant($standaloneExam, 'OSN Candidate 2', 'OSN-002', $admin);

        $this->command->info('Exam MVP demo data seeded (banks, questions, exams, participants, tokens).');
    }

    protected function seedBank(
        string $id,
        int $tenantId,
        ExamAcademicContext $context,
        string $name,
        string $code,
        ?string $standaloneSubject = null,
        ?string $standaloneLevel = null,
    ): ExamQuestionBank {
        return ExamQuestionBank::withoutTenantScope()->updateOrCreate(
            ['id' => $id],
            [
                'tenant_id' => $tenantId,
                'academic_context_type' => $context,
                'name' => $name,
                'code' => $code,
                'description' => 'MVP demo question bank',
                'status' => QuestionBankStatus::Active,
                'standalone_subject' => $standaloneSubject,
                'standalone_level' => $standaloneLevel,
            ],
        );
    }

    protected function seedQuestion(ExamQuestionBank $bank, int $number, string $text, float $score): ExamQuestion
    {
        $question = ExamQuestion::withoutTenantScope()->updateOrCreate(
            [
                'exam_question_bank_id' => $bank->id,
                'question_number' => $number,
            ],
            [
                'tenant_id' => $bank->tenant_id,
                'type' => QuestionType::SingleChoice,
                'topic' => 'Demo topic',
                'subtopic' => 'Demo subtopic',
                'question_text' => $text,
                'score' => $score,
                'status' => QuestionStatus::Active,
            ],
        );

        if ($question->examQuestionOptions()->count() === 0) {
            ExamQuestionOption::withoutTenantScope()->create([
                'tenant_id' => $bank->tenant_id,
                'exam_question_id' => $question->id,
                'option_text' => 'Correct',
                'is_correct' => true,
                'sort_order' => 1,
            ]);
            ExamQuestionOption::withoutTenantScope()->create([
                'tenant_id' => $bank->tenant_id,
                'exam_question_id' => $question->id,
                'option_text' => 'Wrong',
                'is_correct' => false,
                'sort_order' => 2,
            ]);
        }

        return $question;
    }

    protected function seedExam(
        string $id,
        int $tenantId,
        ExamAcademicContext $context,
        string $name,
        string $code,
        ExamType $type,
        ExamQuestion $question,
        ?User $admin,
        ?string $standaloneSubject = null,
        ?string $standaloneLevel = null,
    ): ExamDefinition {
        $proctorIds = $admin !== null ? [(int) $admin->id] : [];

        $exam = ExamDefinition::withoutTenantScope()->updateOrCreate(
            ['id' => $id],
            [
                'tenant_id' => $tenantId,
                'name' => $name,
                'code' => $code,
                'exam_academic_context' => $context,
                'exam_type' => $type,
                'status' => ExamStatus::Ready,
                'max_score' => (float) $question->score,
                'passing_score' => (float) $question->score * 0.6,
                'duration_minutes' => 60,
                'standalone_subject' => $standaloneSubject,
                'standalone_level' => $standaloneLevel,
                'metadata_json' => [
                    'proctor_user_ids' => $proctorIds,
                    'demo' => true,
                ],
                'owner_user_id' => $admin?->id,
            ],
        );

        ExamDefinitionQuestion::query()->updateOrCreate(
            [
                'exam_definition_id' => $exam->id,
                'exam_question_id' => $question->id,
            ],
            [
                'tenant_id' => $tenantId,
                'sort_order' => 0,
            ],
        );

        return $exam;
    }

    protected function seedParticipant(
        ExamDefinition $exam,
        string $name,
        string $identifier,
        ?User $admin,
    ): void {
        $participant = ExamParticipant::withoutTenantScope()->updateOrCreate(
            [
                'exam_definition_id' => $exam->id,
                'student_identifier' => $identifier,
            ],
            [
                'tenant_id' => $exam->tenant_id,
                'student_name' => $name,
                'participant_source' => ParticipantSource::Manual,
                'status' => ParticipantStatus::Assigned,
                'assigned_at' => now(),
                'metadata_json' => ['demo' => true],
            ],
        );

        if ($participant->activeToken === null) {
            app(ExamTokenService::class)->generateToken($participant);
        }
    }
}
