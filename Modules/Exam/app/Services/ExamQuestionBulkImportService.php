<?php

namespace Modules\Exam\Services;

use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Enums\QuestionDifficulty;
use Modules\Exam\Enums\QuestionStatus;
use Modules\Exam\Enums\QuestionType;
use Modules\Exam\Filament\Support\ExamQuestionFormSupport;
use Modules\Exam\Models\ExamQuestion;
use Modules\Exam\Models\ExamQuestionBank;
use Modules\Exam\Models\ExamQuestionOption;

class ExamQuestionBulkImportService
{
    /** @var array<string, string> */
    private const OPTION_COLUMNS = [
        'A' => 'option_a',
        'B' => 'option_b',
        'C' => 'option_c',
        'D' => 'option_d',
        'E' => 'option_e',
    ];

    /**
     * @param  array<string, mixed>  $row
     */
    public function importRow(array $row, int $tenantId): ExamQuestion
    {
        $prepared = $this->validateRow($row, $tenantId);

        return DB::transaction(function () use ($prepared, $tenantId): ExamQuestion {
            $question = ExamQuestion::withoutTenantScope()->create([
                'tenant_id' => $tenantId,
                'exam_question_bank_id' => $prepared['bank_id'],
                'question_number' => $prepared['question_number'],
                'type' => $prepared['type'],
                'topic' => $prepared['topic'],
                'subtopic' => $prepared['subtopic'],
                'difficulty' => $prepared['difficulty'],
                'question_text' => $prepared['question_text'],
                'correct_answer' => $prepared['correct_answer'],
                'explanation' => $prepared['explanation'],
                'answer_key' => $prepared['answer_key'],
                'score' => $prepared['score'],
                'metadata_json' => $prepared['metadata_json'],
                'mi_mapping_json' => $prepared['mi_mapping_json'],
                'status' => QuestionStatus::Draft,
            ]);

            foreach ($prepared['options'] as $option) {
                ExamQuestionOption::withoutTenantScope()->create([
                    'tenant_id' => $tenantId,
                    'exam_question_id' => $question->id,
                    'option_text' => $option['text'],
                    'is_correct' => $option['is_correct'],
                    'sort_order' => $option['sort_order'],
                ]);
            }

            return $question;
        });
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array{
     *     bank_id: string,
     *     question_number: int,
     *     type: QuestionType,
     *     topic: ?string,
     *     subtopic: ?string,
     *     difficulty: ?QuestionDifficulty,
     *     question_text: string,
     *     correct_answer: ?string,
     *     explanation: ?string,
     *     answer_key: ?string,
     *     score: float,
     *     metadata_json: ?array<string, mixed>,
     *     mi_mapping_json: ?array<string, float>,
     *     options: list<array{text: string, is_correct: bool, sort_order: int}>
     * }
     */
    public function validateRow(array $row, int $tenantId): array
    {
        $bankUuid = trim((string) ($row['question_bank_uuid'] ?? ''));

        if ($bankUuid === '' || ! Str::isUuid($bankUuid)) {
            throw new RowImportFailedException('question_bank_uuid must be a valid UUID.');
        }

        $bank = ExamQuestionBank::withoutTenantScope()
            ->where('tenant_id', $tenantId)
            ->whereKey($bankUuid)
            ->first();

        if ($bank === null) {
            throw new RowImportFailedException('Question bank not found for this tenant.');
        }

        $contextValue = strtolower(trim((string) ($row['academic_context_type'] ?? '')));

        try {
            $context = ExamAcademicContext::from($contextValue);
        } catch (\ValueError) {
            throw new RowImportFailedException('academic_context_type is invalid.');
        }

        if ($bank->academic_context_type !== $context) {
            throw new RowImportFailedException('academic_context_type does not match the question bank.');
        }

        $this->validateContextFields($row, $bank, $context);

        $questionText = trim(strip_tags((string) ($row['question_text'] ?? '')));

        if ($questionText === '') {
            throw new RowImportFailedException('question_text is required.');
        }

        $typeValue = strtolower(trim((string) ($row['type'] ?? '')));

        try {
            $type = QuestionType::from($typeValue);
        } catch (\ValueError) {
            throw new RowImportFailedException('type is invalid.');
        }

        $scoreRaw = $row['score'] ?? null;

        if ($scoreRaw !== null && $scoreRaw !== '' && ! is_numeric($scoreRaw)) {
            throw new RowImportFailedException('score must be numeric.');
        }

        $score = ($scoreRaw === null || $scoreRaw === '') ? 1.0 : (float) $scoreRaw;

        $difficulty = null;
        $difficultyValue = strtolower(trim((string) ($row['difficulty'] ?? '')));

        if ($difficultyValue !== '') {
            try {
                $difficulty = QuestionDifficulty::from($difficultyValue);
            } catch (\ValueError) {
                throw new RowImportFailedException('difficulty is invalid.');
            }
        }

        $miMapping = $this->parseMiMapping($row);
        $metadata = $this->parseMetadata($row);
        $options = $this->buildOptions($row, $type);
        $correctAnswer = $this->resolveCorrectAnswer($row, $type, $options);

        $questionNumber = (int) ExamQuestion::withoutTenantScope()
            ->where('exam_question_bank_id', $bank->id)
            ->max('question_number');

        return [
            'bank_id' => $bank->id,
            'question_number' => $questionNumber + 1,
            'type' => $type,
            'topic' => $this->nullableString($row['topic'] ?? null),
            'subtopic' => $this->nullableString($row['subtopic'] ?? null),
            'difficulty' => $difficulty,
            'question_text' => (string) ($row['question_text'] ?? $questionText),
            'correct_answer' => $correctAnswer,
            'explanation' => $this->nullableString($row['explanation'] ?? null),
            'answer_key' => $type->supportsOptions() ? null : $correctAnswer,
            'score' => $score,
            'metadata_json' => $metadata,
            'mi_mapping_json' => $miMapping,
            'options' => $options,
        ];
    }

    /**
     * @param  array<string, mixed>  $row
     */
    protected function validateContextFields(array $row, ExamQuestionBank $bank, ExamAcademicContext $context): void
    {
        if ($context === ExamAcademicContext::School) {
            $subject = $this->nullableString($row['subject'] ?? null);
            $gradeLevel = $this->nullableString($row['grade_level'] ?? null);

            if ($subject !== null && $bank->schoolSubject && strcasecmp($subject, $bank->schoolSubject->name) !== 0) {
                throw new RowImportFailedException('subject does not match the question bank context.');
            }

            if (
                $gradeLevel !== null
                && $bank->school_grade_level_reference !== null
                && strcasecmp($gradeLevel, $bank->school_grade_level_reference) !== 0
            ) {
                throw new RowImportFailedException('grade_level does not match the question bank context.');
            }

            return;
        }

        if ($context === ExamAcademicContext::Campus) {
            $course = $this->nullableString($row['course'] ?? null);

            if ($course !== null && $bank->campusCourse && strcasecmp($course, $bank->campusCourse->name) !== 0) {
                throw new RowImportFailedException('course does not match the question bank context.');
            }

            return;
        }

        $subject = $this->nullableString($row['subject'] ?? null);

        if (
            $subject !== null
            && $bank->standalone_subject !== null
            && strcasecmp($subject, $bank->standalone_subject) !== 0
        ) {
            throw new RowImportFailedException('subject does not match the question bank context.');
        }
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, float>|null
     */
    protected function parseMiMapping(array $row): ?array
    {
        $mapping = [];

        foreach (ExamQuestionFormSupport::MI_KEYS as $key) {
            $column = 'mi_'.$key;
            $raw = $row[$column] ?? null;

            if ($raw === null || $raw === '') {
                continue;
            }

            if (! is_numeric($raw)) {
                throw new RowImportFailedException("{$column} must be numeric between 0 and 100.");
            }

            $value = (float) $raw;

            if ($value < 0 || $value > 100) {
                throw new RowImportFailedException("{$column} must be between 0 and 100.");
            }

            $mapping[$key] = $value > 1 ? round($value / 100, 4) : round($value, 4);
        }

        return $mapping === [] ? null : $mapping;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return array<string, mixed>|null
     */
    protected function parseMetadata(array $row): ?array
    {
        $metadata = [];

        foreach (['olympiad_subject', 'olympiad_level'] as $key) {
            $value = $this->nullableString($row[$key] ?? null);

            if ($value !== null) {
                $metadata[$key] = $value;
            }
        }

        $skillCodes = trim((string) ($row['skill_codes'] ?? ''));

        if ($skillCodes !== '') {
            $metadata['skill_codes'] = array_values(array_filter(array_map(
                trim(...),
                preg_split('/[,;]/', $skillCodes) ?: [],
            )));
        }

        return $metadata === [] ? null : $metadata;
    }

    /**
     * @param  array<string, mixed>  $row
     * @return list<array{text: string, is_correct: bool, sort_order: int}>
     */
    protected function buildOptions(array $row, QuestionType $type): array
    {
        if (! $type->supportsOptions()) {
            return [];
        }

        if ($type === QuestionType::TrueFalse) {
            $trueText = $this->nullableString($row['option_a'] ?? null) ?? 'True';
            $falseText = $this->nullableString($row['option_b'] ?? null) ?? 'False';

            return [
                ['text' => $trueText, 'is_correct' => false, 'sort_order' => 0],
                ['text' => $falseText, 'is_correct' => false, 'sort_order' => 1],
            ];
        }

        $options = [];
        $sortOrder = 0;

        foreach (self::OPTION_COLUMNS as $letter => $column) {
            $text = $this->nullableString($row[$column] ?? null);

            if ($text === null) {
                continue;
            }

            $options[] = [
                'text' => $text,
                'is_correct' => false,
                'sort_order' => $sortOrder,
                'letter' => $letter,
            ];

            $sortOrder++;
        }

        if ($options === []) {
            throw new RowImportFailedException('At least one answer option is required for this question type.');
        }

        return $options;
    }

    /**
     * @param  array<string, mixed>  $row
     * @param  list<array{text: string, is_correct: bool, sort_order: int, letter?: string}>  $options
     */
    protected function resolveCorrectAnswer(array $row, QuestionType $type, array &$options): ?string
    {
        $rawCorrect = trim((string) ($row['correct_answer'] ?? ''));

        if (! $type->supportsOptions()) {
            if ($rawCorrect === '') {
                throw new RowImportFailedException('correct_answer is required.');
            }

            return $rawCorrect;
        }

        if ($rawCorrect === '') {
            throw new RowImportFailedException('correct_answer is required.');
        }

        if ($type === QuestionType::MultipleChoice) {
            $letters = array_map(
                strtoupper(...),
                array_filter(array_map(trim(...), preg_split('/[,;|]/', $rawCorrect) ?: [])),
            );

            if ($letters === []) {
                throw new RowImportFailedException('correct_answer must list valid option letters.');
            }

            $stored = [];

            foreach ($options as &$option) {
                $letter = $option['letter'] ?? null;

                if ($letter !== null && in_array($letter, $letters, true)) {
                    $option['is_correct'] = true;
                    $stored[] = $letter;
                }
            }

            unset($option);

            if (count($stored) !== count($letters)) {
                throw new RowImportFailedException('correct_answer must match provided options.');
            }

            return implode(',', $stored);
        }

        if ($type === QuestionType::TrueFalse) {
            $normalized = strtoupper($rawCorrect);
            $wantTrue = in_array($normalized, ['TRUE', 'T', 'A'], true);
            $wantFalse = in_array($normalized, ['FALSE', 'F', 'B'], true);

            if (! $wantTrue && ! $wantFalse) {
                throw new RowImportFailedException('correct_answer must be True or False (or A/B).');
            }

            $options[0]['is_correct'] = $wantTrue;
            $options[1]['is_correct'] = $wantFalse;

            return $wantTrue ? $options[0]['text'] : $options[1]['text'];
        }

        $matchedLetter = $this->matchOptionLetter($rawCorrect, $options);

        if ($matchedLetter === null) {
            throw new RowImportFailedException('correct_answer must match option A–E or option text.');
        }

        foreach ($options as &$option) {
            if (($option['letter'] ?? null) === $matchedLetter) {
                $option['is_correct'] = true;
            }
        }

        unset($option);

        return $matchedLetter;
    }

    /**
     * @param  list<array{text: string, is_correct: bool, sort_order: int, letter?: string}>  $options
     */
    protected function matchOptionLetter(string $rawCorrect, array $options): ?string
    {
        $normalized = strtoupper($rawCorrect);

        if (strlen($normalized) === 1 && array_key_exists($normalized, self::OPTION_COLUMNS)) {
            foreach ($options as $option) {
                if (($option['letter'] ?? null) === $normalized) {
                    return $normalized;
                }
            }
        }

        foreach ($options as $option) {
            if (strcasecmp($rawCorrect, $option['text']) === 0) {
                return $option['letter'] ?? null;
            }
        }

        if (in_array($normalized, ['TRUE', 'FALSE', 'T', 'F'], true)) {
            $trueAliases = ['TRUE', 'T'];
            $wantTrue = in_array($normalized, $trueAliases, true);

            return $wantTrue ? 'A' : 'B';
        }

        return null;
    }

    protected function nullableString(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        return $string === '' ? null : $string;
    }
}
