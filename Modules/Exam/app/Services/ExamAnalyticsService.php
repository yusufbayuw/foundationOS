<?php

namespace Modules\Exam\Services;

use Illuminate\Support\Collection;
use Modules\Exam\Enums\ExamAcademicContext;
use Modules\Exam\Models\ExamAnswer;
use Modules\Exam\Models\ExamDefinition;
use Modules\Exam\Models\ExamResult;

class ExamAnalyticsService
{
    /** @var list<string> */
    protected array $suspiciousEvents = [
        'tab_blur',
        'tab_switch',
        'copy',
        'paste',
        'suspicious',
        'fullscreen_exit',
        'devtools',
    ];

    /**
     * @return array{context: string, sections: list<array{key: string, title: string, html: string}>, ranking: Collection<int, ExamResult>}
     */
    public function build(ExamDefinition $exam): array
    {
        $exam->loadMissing(['schoolClass', 'schoolSubject', 'campusCourse', 'campusStudyProgram', 'campusFaculty']);

        $results = ExamResult::withoutTenantScope()
            ->where('exam_definition_id', $exam->id)
            ->with(['examParticipant', 'examAttempt'])
            ->get();

        $sections = match ($exam->exam_academic_context) {
            ExamAcademicContext::School => $this->schoolSections($exam, $results),
            ExamAcademicContext::Campus => $this->campusSections($exam, $results),
            ExamAcademicContext::Standalone => $this->standaloneSections($exam, $results),
        };

        return [
            'context' => $exam->exam_academic_context->value,
            'sections' => $sections,
            'ranking' => $results->sortByDesc('score')->values(),
        ];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return list<array{key: string, title: string, html: string}>
     */
    protected function schoolSections(ExamDefinition $exam, Collection $results): array
    {
        $classLabel = $exam->schoolClass->name ?? '-';
        $subjectLabel = $exam->schoolSubject->name ?? '-';
        $average = round((float) $results->avg('score'), 2);
        $topicStats = $this->topicPerformance($exam);
        $remedial = $this->remedialRecommendations($exam, $results);

        return [
            $this->section('class_overview', 'Class overview', [
                'Class' => $classLabel,
                'Subject' => $subjectLabel,
                'Participants' => (string) $results->count(),
                'Class average' => (string) $average,
            ]),
            $this->section('per_student', 'Scores per student', $this->studentScoreLines($results)),
            $this->section('topic_analysis', 'Analysis per topic', $topicStats),
            $this->section('class_performance', 'Class performance', [
                'Pass rate' => $this->passRate($results).'%',
                'Average percentage' => (string) round((float) $results->avg('percentage'), 2),
            ]),
            $this->section('remedial', 'Remedial recommendations', $remedial),
        ];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return list<array{key: string, title: string, html: string}>
     */
    protected function campusSections(ExamDefinition $exam, Collection $results): array
    {
        $courseLabel = $exam->campusCourse->name ?? '-';
        $programLabel = $exam->campusStudyProgram->name ?? '-';
        $facultyLabel = $exam->campusFaculty->name ?? '-';
        $topicStats = $this->topicPerformance($exam);
        $subCloStats = $this->subtopicPerformance($exam);

        return [
            $this->section('course_overview', 'Course overview', [
                'Faculty' => $facultyLabel,
                'Study program' => $programLabel,
                'Course' => $courseLabel,
                'Class average' => (string) round((float) $results->avg('score'), 2),
            ]),
            $this->section('per_student', 'Scores per student', $this->studentScoreLines($results)),
            $this->section('topic_analysis', 'Analysis per topic', $topicStats),
            $this->section('sub_clo_analysis', 'Analysis per sub-CLO', $subCloStats),
            $this->section('program_performance', 'Program performance', [
                'Pass rate' => $this->passRate($results).'%',
            ]),
            $this->section('remedial', 'Remedial and enrichment', $this->remedialAndEnrichment($exam, $results)),
        ];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return list<array{key: string, title: string, html: string}>
     */
    protected function standaloneSections(ExamDefinition $exam, Collection $results): array
    {
        $ranking = $results->sortByDesc('score')->values();
        $readiness = $this->passRate($results);
        $weakTopics = $this->weakTopics($exam);
        $tryoutSummary = [
            'Subject' => $exam->standalone_subject ?? '-',
            'Level' => $exam->standalone_level ?? '-',
            'Participants' => (string) $results->count(),
            'Average score' => (string) round((float) $results->avg('score'), 2),
            'Readiness score' => $readiness.'%',
        ];

        return [
            $this->section('ranking', 'Ranking', $this->rankingLines($ranking)),
            $this->section('readiness', 'Readiness score', [
                'Readiness' => $readiness.'%',
                'Passed' => (string) $results->where('is_passed', true)->count(),
                'Failed' => (string) $results->where('is_passed', false)->count(),
            ]),
            $this->section('weak_topics', 'OSN weak topics', $weakTopics),
            $this->section('tryout_summary', 'Try out summary', $tryoutSummary),
        ];
    }

    /**
     * @param  array<string, string>  $lines
     * @return array{key: string, title: string, html: string}
     */
    protected function section(string $key, string $title, array $lines): array
    {
        $html = '<ul class="list-disc ps-5 space-y-1 text-sm">';

        foreach ($lines as $label => $value) {
            $html .= '<li><strong>'.e($label).':</strong> '.e((string) $value).'</li>';
        }

        $html .= '</ul>';

        return [
            'key' => $key,
            'title' => $title,
            'html' => $html,
        ];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return array<string, string>
     */
    protected function studentScoreLines(Collection $results): array
    {
        $lines = [];

        foreach ($results->sortByDesc('score') as $result) {
            $name = $result->examParticipant->student_name ?? '-';
            $lines[$name] = ($result->score ?? 0).' ('.($result->percentage ?? 0).'%)';
        }

        return $lines !== [] ? $lines : ['-' => 'No results yet'];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return array<string, string>
     */
    protected function rankingLines(Collection $results): array
    {
        $lines = [];
        $rank = 1;

        foreach ($results as $result) {
            $name = $result->examParticipant->student_name ?? '-';
            $lines['#'.$rank.' '.$name] = ($result->score ?? 0).' pts';
            $rank++;
        }

        return $lines !== [] ? $lines : ['-' => 'No results yet'];
    }

    /**
     * @return array<string, string>
     */
    protected function topicPerformance(ExamDefinition $exam): array
    {
        $answers = ExamAnswer::withoutTenantScope()
            ->where('exam_definition_id', $exam->id)
            ->with('examQuestion')
            ->get();

        return $answers
            ->groupBy(fn (ExamAnswer $answer): string => $answer->examQuestion->topic ?? 'Unknown')
            ->mapWithKeys(fn (Collection $group, string $topic): array => [
                $topic => (string) round((float) $group->avg(fn (ExamAnswer $a) => $a->effectiveScore() ?? 0), 2).' avg',
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function subtopicPerformance(ExamDefinition $exam): array
    {
        $answers = ExamAnswer::withoutTenantScope()
            ->where('exam_definition_id', $exam->id)
            ->with('examQuestion')
            ->get();

        return $answers
            ->groupBy(fn (ExamAnswer $answer): string => $answer->examQuestion->subtopic ?? 'General')
            ->mapWithKeys(fn (Collection $group, string $subtopic): array => [
                $subtopic => (string) round((float) $group->avg(fn (ExamAnswer $a) => $a->effectiveScore() ?? 0), 2).' avg',
            ])
            ->all();
    }

    /**
     * @return array<string, string>
     */
    protected function weakTopics(ExamDefinition $exam): array
    {
        $topics = $this->topicPerformance($exam);
        $maxScore = (float) ($exam->max_score ?? 100);
        $threshold = max(1, $maxScore * 0.6);

        $weak = [];

        foreach ($topics as $topic => $avgLabel) {
            $avg = (float) strtok($avgLabel, ' ');
            if ($avg < $threshold) {
                $weak[$topic] = $avgLabel;
            }
        }

        return $weak !== [] ? $weak : ['-' => 'No weak topics identified'];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return array<string, string>
     */
    protected function remedialRecommendations(ExamDefinition $exam, Collection $results): array
    {
        $passing = (float) ($exam->passing_score ?? 0);
        $lines = [];

        foreach ($results->where('score', '<', $passing) as $result) {
            $name = $result->examParticipant->student_name ?? '-';
            $lines[$name] = 'Remedial — score '.($result->score ?? 0);
        }

        return $lines !== [] ? $lines : ['-' => 'All participants met the passing score'];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     * @return array<string, string>
     */
    protected function remedialAndEnrichment(ExamDefinition $exam, Collection $results): array
    {
        $passing = (float) ($exam->passing_score ?? 0);
        $lines = [];

        foreach ($results as $result) {
            $name = $result->examParticipant->student_name ?? '-';
            $score = (float) ($result->score ?? 0);

            if ($score < $passing) {
                $lines[$name] = 'Remedial';
            } elseif ($score >= $passing * 1.2) {
                $lines[$name] = 'Enrichment';
            }
        }

        return $lines !== [] ? $lines : ['-' => 'Balanced performance'];
    }

    /**
     * @param  Collection<int, ExamResult>  $results
     */
    protected function passRate(Collection $results): float
    {
        if ($results->isEmpty()) {
            return 0.0;
        }

        return round(($results->where('is_passed', true)->count() / $results->count()) * 100, 2);
    }
}
