<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\School\Models\Student;
use Modules\School\Services\StudentRiskScoreService;

class RecomputeStudentRiskScoresCommand extends Command
{
    protected $signature = 'school:recompute-risk-scores {--tenant=}';

    protected $description = 'Recompute composite student risk scores for active students';

    public function handle(StudentRiskScoreService $service): int
    {
        $query = Student::query()->where('status', 'active');

        if ($tenantId = $this->option('tenant')) {
            $query->where('tenant_id', $tenantId);
        }

        $count = 0;
        $query->each(function (Student $student) use ($service, &$count): void {
            $service->recomputeAndStore($student);
            $count++;
        });

        $this->info("Recomputed risk scores for {$count} student(s).");

        return self::SUCCESS;
    }
}
