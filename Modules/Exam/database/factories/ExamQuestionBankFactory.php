<?php

namespace Modules\Exam\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionBankStatus;
use Modules\Exam\Models\ExamQuestionBank;

/**
 * @extends Factory<ExamQuestionBank>
 */
class ExamQuestionBankFactory extends Factory
{
    protected $model = ExamQuestionBank::class;

    public function definition(): array
    {
        return [
            'academic_context_type' => ExamAcademicContext::Standalone,
            'standalone_subject' => 'General',
            'code' => strtoupper(fake()->unique()->bothify('QB-###')),
            'name' => fake()->words(3, true),
            'status' => QuestionBankStatus::Active,
            'description' => fake()->sentence(),
            'metadata_json' => [],
        ];
    }
}
