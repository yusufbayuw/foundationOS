<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Core\Models\SubscriptionPlan;
use Modules\Core\Models\Tenant;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Filament\Support\ExamQuestionFormSupport;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;
use Tests\TestCase;

class ExamQuestionOptionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_question_persists_options_and_mi_osn_metadata(): void
    {
        $plan = SubscriptionPlan::create([
            'code' => 'exam-q',
            'name' => 'Exam Q',
            'included_modules' => ['core', 'exam'],
        ]);

        $tenant = Tenant::create([
            'uuid' => (string) Str::uuid(),
            'code' => 'exam-q-tenant',
            'name' => 'Exam Q Tenant',
            'subscription_plan_id' => $plan->id,
        ]);

        $bank = ExamQuestionBank::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'academic_context_type' => ExamAcademicContext::Standalone,
            'standalone_subject' => 'Math',
            'standalone_level' => 'Regional',
            'name' => 'Math Bank',
            'status' => QuestionBankStatus::Active,
        ]);

        $question = ExamQuestion::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_bank_id' => $bank->id,
            'question_number' => 1,
            'type' => QuestionType::SingleChoice,
            'topic' => 'Algebra',
            'question_text' => '<p>What is 2+2?</p>',
            'score' => 5,
            'status' => QuestionStatus::Draft,
            'mi_mapping_json' => ['logical' => 0.8],
            'metadata_json' => [
                'olympiad_subject' => 'Mathematics',
                'olympiad_level' => 'Regional',
                'skill_codes' => ['ALG-01'],
                'estimated_time_seconds' => 120,
            ],
        ]);

        ExamQuestionOption::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_id' => $question->id,
            'option_text' => '4',
            'is_correct' => true,
            'sort_order' => 0,
        ]);

        ExamQuestionOption::withoutTenantScope()->create([
            'tenant_id' => $tenant->id,
            'exam_question_id' => $question->id,
            'option_text' => '5',
            'is_correct' => false,
            'sort_order' => 1,
        ]);

        $question->refresh()->load('examQuestionOptions', 'examQuestionBank');

        $this->assertCount(2, $question->examQuestionOptions);
        $this->assertSame(ExamAcademicContext::Standalone, $question->examQuestionBank->academic_context_type);
        $this->assertSame('Mathematics', $question->metadata_json['olympiad_subject']);
        $this->assertSame(0.8, $question->mi_mapping_json['logical']);

        $hydrated = ExamQuestionFormSupport::hydrateFormData($question);
        $this->assertSame(0.8, $hydrated['mi_logical']);
        $this->assertSame('Regional', $hydrated['olympiad_level']);
    }

    public function test_form_support_dehydrates_mi_and_osn_fields(): void
    {
        $data = ExamQuestionFormSupport::dehydrateFormData([
            'tenant_id' => 1,
            'exam_question_bank_id' => (string) Str::uuid(),
            'type' => QuestionType::Essay->value,
            'mi_logical' => '0.5',
            'mi_linguistic' => '0.25',
            'olympiad_subject' => 'Physics',
            'olympiad_level' => 'National',
            'skill_codes' => ['PHY-1'],
            'estimated_time_seconds' => 300,
        ]);

        $this->assertSame(0.5, $data['mi_mapping_json']['logical']);
        $this->assertSame('Physics', $data['metadata_json']['olympiad_subject']);
        $this->assertArrayNotHasKey('mi_logical', $data);
        $this->assertArrayNotHasKey('olympiad_subject', $data);
    }
}
